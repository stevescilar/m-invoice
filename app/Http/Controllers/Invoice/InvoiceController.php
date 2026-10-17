<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Mail\InvoiceReminderMail;
use App\Mail\PaymentReceiptMail;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\InvoiceReminder;
use App\Models\ServiceCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $company = auth()->user()->company;
        $status  = $request->get('status', 'all');
        $search  = $request->get('search');
        $sort    = $request->get('sort', 'latest');

        $query = Invoice::where('company_id', $company->id)->with('client');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        match ($sort) {
            'oldest'      => $query->oldest(),
            'amount_high' => $query->orderByDesc('grand_total'),
            'amount_low'  => $query->orderBy('grand_total'),
            'due_soon'    => $query->orderBy('due_date'),
            default       => $query->latest(),
        };

        $invoices = $query->paginate(15);

        // Single query instead of 6 separate COUNT queries
        $statusCounts = Invoice::where('company_id', $company->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats = [
            'all'     => $statusCounts->sum(),
            'draft'   => $statusCounts->get('draft', 0),
            'sent'    => $statusCounts->get('sent', 0),
            'paid'    => $statusCounts->get('paid', 0),
            'overdue' => $statusCounts->get('overdue', 0),
            'stalled' => $statusCounts->get('stalled', 0),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    // -------------------------------------------------------------------------
    // Create / Store
    // -------------------------------------------------------------------------

    public function create()
    {
        $company       = Auth::user()->company;
        $clients       = Client::where('company_id', $company->id)->orderBy('name')->get();
        $categories    = ServiceCategory::where('company_id', $company->id)->with('catalogItems')->get();
        $itemTypes     = $company->itemTypes()->where('is_active', true)->get();
        $invoiceNumber = $this->generateInvoiceNumber();

        return view('invoices.create', compact('clients', 'categories', 'invoiceNumber', 'itemTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = Auth::user()->company_id;

        $request->validate([
            'client_id'            => ['required', \Illuminate\Validation\Rule::exists('clients', 'id')->where('company_id', $companyId)],
            'invoice_number'       => ['required', 'string', \Illuminate\Validation\Rule::unique('invoices', 'invoice_number')->where('company_id', $companyId)],
            'issue_date'           => 'required|date',
            'due_date'             => 'nullable|date|after_or_equal:issue_date',
            'notes'                => 'nullable|string',
            'etr_enabled'          => 'boolean',
            'items'                => 'required|array|min:1',
            'items.*.description'  => 'required|string',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.is_labour'    => 'nullable|boolean',
            'items.*.item_type_id' => 'nullable|exists:item_types,id',
        ]);

        DB::transaction(function () use ($request) {
            $totals  = $this->calculateTotals($request->input('items'), $request->boolean('etr_enabled'));
            $totals  = $this->applyDiscount($totals, $request);

            $invoice = Invoice::create([
                'company_id'          => Auth::user()->company_id,
                'client_id'           => $request->client_id,
                'invoice_number'      => $request->invoice_number,
                'issue_date'          => $request->issue_date,
                'due_date'            => $request->due_date,
                'status'              => 'draft',
                'etr_enabled'         => $totals['etr_enabled'],
                'vat_amount'          => $totals['vat_amount'],
                'material_cost'       => $totals['material_cost'],
                'labour_cost'         => $totals['labour_cost'],
                'discount_amount'     => $totals['discount_amount'],
                'discount_percentage' => $totals['discount_pct'],
                'grand_total'         => $totals['grand_total'],
                'total_cost'          => $totals['total_cost'],
                'total_profit'        => $totals['total_profit'],
                'overall_margin'      => $totals['overall_margin'],
                'notes'               => $request->notes,
                'created_by'          => Auth::id(),
            ]);

            if ($request->boolean('is_recurring')) {
                $invoice->update([
                    'is_recurring'        => true,
                    'recurring_frequency' => $request->recurring_frequency ?? 'monthly',
                    'recurring_next_date' => $request->recurring_next_date
                        ? Carbon::parse($request->recurring_next_date)
                        : $invoice->getNextRecurringDate(),
                    'recurring_ends_at'   => $request->recurring_ends_at
                        ? Carbon::parse($request->recurring_ends_at)
                        : null,
                    'recurring_active'    => true,
                ]);
            }

            $this->saveItems($invoice, $request->input('items'));

            if ($request->due_date) {
                $this->createReminders($invoice);
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $this->requireActiveSubscription('view invoices');

        $invoice->load('client', 'items', 'items.catalogItem', 'items.itemType', 'payments', 'payments.recordedBy');
        $company = Auth::user()->company;

        return view('invoices.show', compact('invoice', 'company'));
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function edit(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $this->requireActiveSubscription('edit invoices');

        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Paid invoices cannot be edited.');
        }

        $company      = Auth::user()->company;
        $clients      = Client::where('company_id', $company->id)->orderBy('name')->get();
        $categories   = ServiceCategory::where('company_id', $company->id)->with('catalogItems')->get();
        $itemTypes    = $company->itemTypes()->where('is_active', true)->get();

        $invoice->load('items');

        $invoiceItems = $invoice->items->map(fn ($i) => [
            'catalog_item_id' => $i->catalog_item_id,
            'description'     => $i->description,
            'quantity'        => (float) $i->quantity,
            'unit_price'      => (float) $i->unit_price,
            'buying_price'    => (float) $i->buying_price,
            'is_labour'       => (bool) $i->is_labour,
            'item_type_id'    => (string) $i->item_type_id,
        ])->values()->toArray();

        return view('invoices.edit', compact('invoice', 'clients', 'categories', 'invoiceItems', 'itemTypes'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        // Quick status-only update (e.g. "Change to Stalled" from show page)
        if ($request->boolean('status_only') || ($request->has('status') && !$request->has('items'))) {
            $request->validate([
                'status' => 'required|in:draft,sent,paid,overdue,stalled',
            ]);

            $updateData = ['status' => $request->status];
            if ($request->status === 'paid' && !$invoice->paid_at) {
                $updateData['paid_at'] = now();
            }

            $invoice->update($updateData);

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice status updated to ' . ucfirst($request->status) . '.');
        }

        $companyId = Auth::user()->company_id;

        $request->validate([
            'client_id'            => ['required', \Illuminate\Validation\Rule::exists('clients', 'id')->where('company_id', $companyId)],
            'invoice_number'       => ['sometimes', 'required', 'string', \Illuminate\Validation\Rule::unique('invoices', 'invoice_number')->where('company_id', $companyId)->ignore($invoice->id)],
            'status'               => 'nullable|in:draft,sent,paid,overdue,stalled',
            'issue_date'           => 'required|date',
            'due_date'             => 'nullable|date|after_or_equal:issue_date',
            'notes'                => 'nullable|string',
            'etr_enabled'          => 'boolean',
            'items'                => 'required|array|min:1',
            'items.*.description'  => 'required|string',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.item_type_id' => 'nullable|exists:item_types,id',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            $totals = $this->calculateTotals($request->input('items'), $request->boolean('etr_enabled'));
            $totals = $this->applyDiscount($totals, $request);

            $newStatus = $request->input('status', $invoice->status);

            $invoice->update([
                'client_id'           => $request->client_id,
                'invoice_number'      => $request->input('invoice_number', $invoice->invoice_number),
                'status'              => $newStatus,
                'paid_at'             => $newStatus === 'paid' ? ($invoice->paid_at ?? now()) : $invoice->paid_at,
                'issue_date'          => $request->issue_date,
                'due_date'            => $request->due_date,
                'etr_enabled'         => $totals['etr_enabled'],
                'vat_amount'          => $totals['vat_amount'],
                'material_cost'       => $totals['material_cost'],
                'labour_cost'         => $totals['labour_cost'],
                'discount_amount'     => $totals['discount_amount'],
                'discount_percentage' => $totals['discount_pct'],
                'grand_total'         => $totals['grand_total'],
                'total_cost'          => $totals['total_cost'],
                'total_profit'        => $totals['total_profit'],
                'overall_margin'      => $totals['overall_margin'],
                'notes'               => $request->notes,
            ]);

            $invoice->items()->delete();
            $this->saveItems($invoice, $request->input('items'));

            // Refresh reminders whenever the due date changes
            $invoice->reminders()->delete();
            if ($request->due_date) {
                $this->createReminders($invoice->fresh());
            }
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated.');
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    // -------------------------------------------------------------------------
    // Send / Reminders
    // -------------------------------------------------------------------------

    public function send(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        if (!$invoice->client->email) {
            return back()->with('error', 'This client has no email address.');
        }

        $invoice->load('client', 'items', 'items.itemType', 'company');
        $company = Auth::user()->company;

        try {
            $company->sendMail($invoice->client->email, new InvoiceMail($invoice, $company), Auth::user());
            if ($invoice->status === 'draft') {
                $invoice->update(['status' => 'sent']);
            }

            $copies = $company->getCopyRecipients($invoice->client->email, Auth::user());
            $copyNote = !empty($copies) ? ' (Copy sent to ' . implode(', ', $copies) . ')' : '';

            return back()->with('success', 'Invoice sent to ' . $invoice->client->email . $copyNote);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function sendReminder(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        if (!$invoice->client->email) {
            return back()->with('error', 'This client has no email address on record.');
        }

        if ($invoice->status === 'paid') {
            return back()->with('error', 'This invoice is already marked as paid.');
        }

        $invoice->load('client', 'items', 'items.itemType', 'company');
        $company = Auth::user()->company;

        try {
            $company->sendMail(
                $invoice->client->email,
                new InvoiceReminderMail($invoice, $company->name ?? Auth::user()->name),
                Auth::user()
            );

            $copies = $company->getCopyRecipients($invoice->client->email, Auth::user());
            $copyNote = !empty($copies) ? ' (Copy sent to ' . implode(', ', $copies) . ')' : '';

            return back()->with('success', 'Payment reminder sent to ' . $invoice->client->email . $copyNote);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send reminder: ' . $e->getMessage());
        }
    }

    public function whatsappReminder(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        $company = Auth::user()->company;
        $phone   = $this->normalizePhone($invoice->client->phone ?? '');

        $dueDate = $invoice->due_date
            ? Carbon::parse($invoice->due_date)->format('d M Y')
            : 'soon';

        $amount = number_format($invoice->balanceDue(), 2);
        $status = $invoice->status === 'overdue' ? 'overdue' : 'due';

        $mpesa = '';
        if ($company->mpesa_paybill) {
            $acc   = $company->mpesa_account ?? $invoice->invoice_number;
            $mpesa = "\n\n💚 *Pay via M-Pesa Paybill:* {$company->mpesa_paybill}\nAccount: {$acc}";
        } elseif ($company->mpesa_till) {
            $mpesa = "\n\n💚 *Pay via M-Pesa Till:* {$company->mpesa_till}";
        }

        $dueLine = $invoice->status === 'overdue' ? '.' : " on *{$dueDate}*.";

        $message = "Hello {$invoice->client->name},\n\n"
            . "I hope you're doing well! This is a friendly reminder that Invoice "
            . "*{$invoice->invoice_number}* (Balance Due: *Ksh {$amount}*) is {$status}{$dueLine}"
            . $mpesa
            . "\n\n🔗 *View & Download Invoice Online:*\n{$invoice->public_url}"
            . "\n\nKindly arrange payment at your earliest convenience. "
            . "Please reach out if you have any questions.\n\n"
            . "Thank you!\n{$company->name}";

        return redirect('https://wa.me/' . $phone . '?text=' . urlencode($message));
    }

    // -------------------------------------------------------------------------
    // Payments, Partial Deposits & Receipts
    // -------------------------------------------------------------------------

    public function markPaid(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        $request->validate([
            'mpesa_code' => 'nullable|string|max:40',
        ]);

        $remaining = $invoice->balanceDue();
        if ($remaining > 0) {
            InvoicePayment::create([
                'invoice_id'     => $invoice->id,
                'company_id'     => $invoice->company_id,
                'receipt_number' => $this->generateReceiptNumber($invoice->company_id),
                'amount'         => $remaining,
                'payment_method' => $request->mpesa_code ? 'mpesa' : 'cash',
                'reference'      => $request->mpesa_code ? strtoupper(trim($request->mpesa_code)) : null,
                'paid_at'        => now(),
                'notes'          => 'Full balance payment',
                'recorded_by'    => Auth::id(),
            ]);
        }

        $invoice->update([
            'status'      => 'paid',
            'amount_paid' => $invoice->grand_total,
            'mpesa_code'  => $request->mpesa_code ?: $invoice->mpesa_code,
            'paid_at'     => now(),
        ]);

        return back()->with('success', 'Invoice marked as paid in full.');
    }

    public function recordPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        $maxBalance = max(0.01, $invoice->balanceDue());

        $request->validate([
            'amount'         => "required|numeric|min:1|max:{$maxBalance}",
            'payment_method' => 'required|in:mpesa,bank,cash,cheque,other',
            'reference'      => 'nullable|string|max:60',
            'paid_at'        => 'nullable|date',
            'notes'          => 'nullable|string|max:300',
            'send_receipt'   => 'nullable|boolean',
        ]);

        $company = Auth::user()->company;
        $amount  = round((float) $request->amount, 2);

        $payment = DB::transaction(function () use ($request, $invoice, $amount) {
            $payment = InvoicePayment::create([
                'invoice_id'     => $invoice->id,
                'company_id'     => $invoice->company_id,
                'receipt_number' => $this->generateReceiptNumber($invoice->company_id),
                'amount'         => $amount,
                'payment_method' => $request->payment_method,
                'reference'      => $request->reference ? strtoupper(trim($request->reference)) : null,
                'paid_at'        => $request->paid_at ? Carbon::parse($request->paid_at) : now(),
                'notes'          => $request->notes,
                'recorded_by'    => Auth::id(),
            ]);

            $totalPaid = round((float) $invoice->payments()->sum('amount'), 2);
            $isPaidFull = $totalPaid >= round((float) $invoice->grand_total, 2);

            $invoice->update([
                'amount_paid' => min((float) $invoice->grand_total, $totalPaid),
                'status'      => $isPaidFull ? 'paid' : ($invoice->status === 'draft' ? 'sent' : $invoice->status),
                'paid_at'     => $isPaidFull ? ($invoice->paid_at ?? now()) : $invoice->paid_at,
                'mpesa_code'  => $request->reference ?: $invoice->mpesa_code,
            ]);

            return $payment;
        });

        $msg = 'Payment of Ksh ' . number_format($amount, 2) . " recorded (Receipt #{$payment->receipt_number}).";

        if ($request->boolean('send_receipt') && $invoice->client->email) {
            try {
                $invoice->refresh();
                $company->sendMail(
                    $invoice->client->email,
                    new PaymentReceiptMail($invoice, $company, $payment),
                    Auth::user()
                );
                $msg .= ' Receipt emailed to ' . $invoice->client->email . '.';
            } catch (\Throwable $e) {
                $msg .= ' (Could not email receipt: ' . $e->getMessage() . ')';
            }
        }

        return back()->with('success', $msg);
    }

    public function deletePayment(Invoice $invoice, InvoicePayment $payment): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        if ($payment->invoice_id !== $invoice->id) {
            abort(403);
        }

        DB::transaction(function () use ($invoice, $payment) {
            $payment->delete();

            $totalPaid = round((float) $invoice->payments()->sum('amount'), 2);
            $isPaidFull = $totalPaid >= round((float) $invoice->grand_total, 2);

            $invoice->update([
                'amount_paid' => $totalPaid,
                'status'      => $isPaidFull ? 'paid' : ($invoice->due_date?->isPast() ? 'overdue' : 'sent'),
                'paid_at'     => $isPaidFull ? $invoice->paid_at : null,
            ]);
        });

        return back()->with('success', 'Payment entry removed and invoice balance updated.');
    }

    public function downloadReceipt(Invoice $invoice, ?InvoicePayment $payment = null)
    {
        $this->authorizeInvoice($invoice);

        if ($payment && $payment->invoice_id !== $invoice->id) {
            abort(403);
        }

        $invoice->load('client', 'items', 'items.itemType', 'payments');
        $company = Auth::user()->company;
        $payment = $payment ?? $invoice->payments->first();

        $receiptNo = $payment?->receipt_number ?? ('RCP-' . str_replace('INV-', '', $invoice->invoice_number));

        $pdf = Pdf::loadView('invoices.receipt-pdf', compact('invoice', 'company', 'payment'));

        return $pdf->download('receipt-' . $receiptNo . '.pdf');
    }

    public function emailReceipt(Invoice $invoice, ?InvoicePayment $payment = null): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        if (!$invoice->client->email) {
            return back()->with('error', 'This client has no email address.');
        }

        if ($payment && $payment->invoice_id !== $invoice->id) {
            abort(403);
        }

        $invoice->load('client', 'items', 'items.itemType', 'payments');
        $company = Auth::user()->company;
        $payment = $payment ?? $invoice->payments->first();

        try {
            $company->sendMail(
                $invoice->client->email,
                new PaymentReceiptMail($invoice, $company, $payment),
                Auth::user()
            );

            $copies = $company->getCopyRecipients($invoice->client->email, Auth::user());
            $copyNote = !empty($copies) ? ' (Copy sent to ' . implode(', ', $copies) . ')' : '';

            return back()->with('success', 'Payment receipt sent to ' . $invoice->client->email . $copyNote);
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to send receipt: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // PDF Download & Delivery Note
    // -------------------------------------------------------------------------

    public function download(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $this->requireActiveSubscription('download invoices');

        $invoice->load('client', 'items', 'items.itemType');
        $company = Auth::user()->company;

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'company'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function downloadPdf(Invoice $invoice)
    {
        return $this->download($invoice);
    }

    public function downloadDeliveryNote(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $invoice->load('client', 'items', 'items.itemType');
        $company = Auth::user()->company;

        $pdf = Pdf::loadView('pdf.delivery-note', [
            'company'        => $company,
            'client'         => $invoice->client,
            'items'          => $invoice->items,
            'documentNumber' => $invoice->invoice_number,
            'notes'          => $invoice->notes,
            'invoice'        => $invoice,
        ]);

        return $pdf->download('delivery-note-' . $invoice->invoice_number . '.pdf');
    }

    // -------------------------------------------------------------------------
    // Duplicate
    // -------------------------------------------------------------------------

    public function duplicate(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        DB::transaction(function () use ($invoice) {
            $newInvoice                 = $invoice->replicate();
            $newInvoice->invoice_number = $this->generateInvoiceNumber();
            $newInvoice->public_token   = \Illuminate\Support\Str::random(32);
            $newInvoice->status         = 'draft';
            $newInvoice->amount_paid    = 0;
            $newInvoice->issue_date     = now();
            $newInvoice->paid_at        = null;
            $newInvoice->mpesa_code     = null;
            $newInvoice->save();

            foreach ($invoice->items as $item) {
                $newItem             = $item->replicate();
                $newItem->invoice_id = $newInvoice->id;
                $newItem->save();
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice duplicated successfully.');
    }

    private function generateReceiptNumber(int $companyId): string
    {
        $count = InvoicePayment::where('company_id', $companyId)->count() + 1;
        return 'RCP-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    // -------------------------------------------------------------------------
    // Recurring Controls
    // -------------------------------------------------------------------------

    public function pauseRecurring(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);
        $invoice->update(['recurring_active' => false]);

        return back()->with('success', 'Recurring invoice paused.');
    }

    public function resumeRecurring(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);
        $invoice->update(['recurring_active' => true]);

        return back()->with('success', 'Recurring invoice resumed.');
    }

    public function cancelRecurring(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);
        $invoice->update([
            'is_recurring'        => false,
            'recurring_active'    => false,
            'recurring_next_date' => null,
        ]);

        return back()->with('success', 'Recurring cancelled. No more invoices will be generated.');
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    /**
     * Calculate material, labour, VAT, cost, profit, and margin from line items.
     */
    private function calculateTotals(array $items, bool $etrEnabled): array
    {
        $materialCost = 0;
        $labourCost   = 0;
        $totalCost    = 0;

        foreach ($items as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $totalCost += $item['quantity'] * ($item['buying_price'] ?? 0);

            if (!empty($item['is_labour'])) {
                $labourCost += $lineTotal;
            } else {
                $materialCost += $lineTotal;
            }
        }

        $vatAmount = $etrEnabled ? round($materialCost * 0.16, 2) : 0;
        $subtotal  = $materialCost + $labourCost;

        return compact('etrEnabled', 'materialCost', 'labourCost', 'totalCost', 'vatAmount', 'subtotal');
    }

    /**
     * Apply discount (percentage takes precedence over fixed amount) and derive
     * grand total, profit, and margin.
     */
    private function applyDiscount(array $totals, Request $request): array
    {
        $subtotal    = $totals['subtotal'];
        $vatAmount   = $totals['vatAmount'];
        $totalCost   = $totals['totalCost'];
        $etrEnabled  = $totals['etrEnabled'];

        $discountPct    = (float) ($request->discount_percentage ?? 0);
        $discountAmount = $discountPct > 0
            ? round($subtotal * $discountPct / 100, 2)
            : round((float) ($request->discount_amount ?? 0), 2);

        $grandTotal    = max(0, $subtotal - $discountAmount + $vatAmount);
        $totalProfit   = $grandTotal - $totalCost - $vatAmount;
        $overallMargin = $grandTotal > 0 ? round(($totalProfit / $grandTotal) * 100, 2) : 0;

        return [
            'etr_enabled'    => $etrEnabled,
            'material_cost'  => $totals['materialCost'],
            'labour_cost'    => $totals['labourCost'],
            'total_cost'     => $totalCost,
            'vat_amount'     => $vatAmount,
            'discount_pct'   => $discountPct,
            'discount_amount'=> $discountAmount,
            'grand_total'    => $grandTotal,
            'total_profit'   => $totalProfit,
            'overall_margin' => $overallMargin,
        ];
    }

    /**
     * Persist invoice line items.
     */
    private function saveItems(Invoice $invoice, array $items): void
    {
        foreach ($items as $item) {
            $buyingPrice = $item['buying_price'] ?? 0;

            InvoiceItem::create([
                'invoice_id'      => $invoice->id,
                'catalog_item_id' => $item['catalog_item_id'] ?? null,
                'description'     => $item['description'],
                'quantity'        => $item['quantity'],
                'unit_price'      => $item['unit_price'],
                'buying_price'    => $buyingPrice,
                'total_price'     => $item['quantity'] * $item['unit_price'],
                'is_labour'       => !empty($item['is_labour']),
                'item_type_id'    => $item['item_type_id'] ?? null,
            ]);

            if (!empty($item['catalog_item_id']) && $buyingPrice > 0) {
                \App\Models\CatalogItem::where('id', $item['catalog_item_id'])
                    ->where('company_id', $invoice->company_id)
                    ->update(['default_buying_price' => $buyingPrice]);
            }
        }
    }

    /**
     * Schedule the standard three reminders for an invoice.
     */
    private function createReminders(Invoice $invoice): void
    {
        $reminders = [
            ['type' => 'before_due', 'date' => $invoice->due_date->copy()->subDays(3)],
            ['type' => 'on_due',     'date' => $invoice->due_date->copy()],
            ['type' => 'after_due',  'date' => $invoice->due_date->copy()->addDays(7)],
        ];

        foreach ($reminders as $reminder) {
            InvoiceReminder::create([
                'invoice_id'    => $invoice->id,
                'reminder_type' => $reminder['type'],
                'scheduled_at'  => $reminder['date'],
                'status'        => 'pending',
            ]);
        }
    }

    /**
     * Normalize a Kenyan phone number to international format (254XXXXXXXXX).
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Abort with 403 if the invoice does not belong to the authenticated user's company.
     */
    private function authorizeInvoice(Invoice $invoice): void
    {
        if ($invoice->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }

    /**
     * Redirect to the subscription page if the company's plan does not allow PDF access.
     */
    private function requireActiveSubscription(string $action = 'perform this action'): void
    {
        $subscription = Auth::user()->company->subscription;

        if (!$subscription || !$subscription->canDownloadPdf()) {
            redirect()->route('subscription.index')
                ->with('error', "Your trial has expired. Please subscribe to {$action}.")
                ->send();

            exit;
        }
    }

    /**
     * Generate the next invoice number scoped to the current company.
     */
    private function generateInvoiceNumber(): string
    {
        $last       = Invoice::where('company_id', Auth::user()->company_id)
                        ->orderByDesc('id')
                        ->value('invoice_number');
        $lastNumber = $last ? (int) substr($last, 4) : 0;

        return 'INV-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }
}