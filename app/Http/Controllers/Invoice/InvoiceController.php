<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Mail\InvoiceReminderMail;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
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

        $invoice->load('client', 'items', 'items.catalogItem', 'items.itemType');
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

        $invoice->load('client', 'items');
        $company = Auth::user()->company;

        try {
            Mail::to($invoice->client->email)->send(new InvoiceMail($invoice, $company));
            $invoice->update(['status' => 'sent']);

            return back()->with('success', 'Invoice sent to ' . $invoice->client->email);
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

        try {
            Mail::to($invoice->client->email)
                ->send(new InvoiceReminderMail($invoice, Auth::user()->name));

            return back()->with('success', 'Payment reminder sent to ' . $invoice->client->email);
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

        $amount = number_format($invoice->grand_total, 2);
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
            . "*{$invoice->invoice_number}* for *Ksh {$amount}* is {$status}{$dueLine}"
            . $mpesa
            . "\n\nKindly arrange payment at your earliest convenience. "
            . "Please reach out if you have any questions.\n\n"
            . "Thank you!\n{$company->name}";

        return redirect('https://wa.me/' . $phone . '?text=' . urlencode($message));
    }

    // -------------------------------------------------------------------------
    // Mark Paid
    // -------------------------------------------------------------------------

    public function markPaid(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        $request->validate([
            'mpesa_code' => 'nullable|string|max:20',
        ]);

        $invoice->update([
            'status'     => 'paid',
            'mpesa_code' => $request->mpesa_code,
            'paid_at'    => now(),
        ]);

        return back()->with('success', 'Invoice marked as paid.');
    }

    // -------------------------------------------------------------------------
    // PDF Download
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

    // -------------------------------------------------------------------------
    // Duplicate
    // -------------------------------------------------------------------------

    public function duplicate(Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoice($invoice);

        DB::transaction(function () use ($invoice) {
            $newInvoice                 = $invoice->replicate();
            $newInvoice->invoice_number = $this->generateInvoiceNumber();
            $newInvoice->status         = 'draft';
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