<?php

namespace App\Http\Controllers\Quotation;

use App\Http\Controllers\Controller;
use App\Mail\QuotationMail;
use App\Mail\QuotationReminderMail;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\ServiceCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
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

        $query = Quotation::where('company_id', $company->id)->with('client');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        match ($sort) {
            'oldest'      => $query->oldest(),
            'amount_high' => $query->orderByDesc('grand_total'),
            'amount_low'  => $query->orderBy('grand_total'),
            'expiry_soon' => $query->orderBy('expiry_date'),
            default       => $query->latest(),
        };

        $quotations = $query->paginate(15);

        // Single grouped query instead of 6 separate COUNTs
        $statusCounts = Quotation::where('company_id', $company->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats = [
            'all'       => $statusCounts->sum(),
            'draft'     => $statusCounts->get('draft', 0),
            'sent'      => $statusCounts->get('sent', 0),
            'approved'  => $statusCounts->get('approved', 0),
            'rejected'  => $statusCounts->get('rejected', 0),
            'expired'   => $statusCounts->get('expired', 0),
            'converted' => $statusCounts->get('converted', 0),
        ];

        return view('quotations.index', compact('quotations', 'stats'));
    }

    // -------------------------------------------------------------------------
    // Create / Store
    // -------------------------------------------------------------------------

    public function create()
    {
        $company         = Auth::user()->company;
        $clients         = Client::where('company_id', $company->id)->orderBy('name')->get();
        $categories      = ServiceCategory::where('company_id', $company->id)->with('catalogItems')->get();
        $itemTypes       = $company->itemTypes()->where('is_active', true)->get();
        $quotationNumber = $this->generateQuotationNumber();

        return view('quotations.create', compact('clients', 'categories', 'quotationNumber', 'itemTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = Auth::user()->company_id;

        $request->validate([
            'client_id'            => ['required', \Illuminate\Validation\Rule::exists('clients', 'id')->where('company_id', $companyId)],
            'quotation_number'     => ['required', 'string', \Illuminate\Validation\Rule::unique('quotations', 'quotation_number')->where('company_id', $companyId)],
            'issue_date'           => 'required|date',
            'expiry_date'          => 'nullable|date|after_or_equal:issue_date',
            'notes'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.description'  => 'required|string',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.item_type_id' => 'nullable|exists:item_types,id',
        ]);

        DB::transaction(function () use ($request) {
            $totals = $this->calculateTotals($request->input('items'));
            $totals = $this->applyDiscount($totals, $request);

            $quotation = Quotation::create([
                'company_id'          => Auth::user()->company_id,
                'client_id'           => $request->client_id,
                'quotation_number'    => $request->quotation_number,
                'issue_date'          => $request->issue_date,
                'expiry_date'         => $request->expiry_date,
                'status'              => 'draft',
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

            $this->saveItems($quotation, $request->input('items'));
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation created successfully.');
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);
        $this->requireActiveSubscription('view quotations');

        $quotation->load('client', 'items', 'items.itemType');
        $company = Auth::user()->company;

        return view('quotations.show', compact('quotation', 'company'));
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function edit(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);
        $this->requireActiveSubscription('edit quotations');

        if (in_array($quotation->status, ['converted', 'approved'])) {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'This quotation cannot be edited.');
        }

        $company    = Auth::user()->company;
        $clients    = Client::where('company_id', $company->id)->orderBy('name')->get();
        $categories = ServiceCategory::where('company_id', $company->id)->with('catalogItems')->get();
        $itemTypes  = $company->itemTypes()->where('is_active', true)->get();

        $quotation->load('items');

        $quotationItems = $quotation->items->map(fn ($i) => [
            'catalog_item_id' => $i->catalog_item_id,
            'description'     => $i->description,
            'quantity'        => (float) $i->quantity,
            'unit_price'      => (float) $i->unit_price,
            'buying_price'    => (float) $i->buying_price,
            'is_labour'       => (bool) $i->is_labour,
            'item_type_id'    => (string) $i->item_type_id,
        ])->values()->toArray();

        return view('quotations.edit', compact('quotation', 'clients', 'categories', 'quotationItems', 'itemTypes'));
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->authorizeQuotation($quotation);

        // Quick status-only update (e.g. "Mark as Approved" from show page)
        if ($request->boolean('status_change') || ($request->has('status') && !$request->has('items'))) {
            $request->validate([
                'status' => 'required|in:draft,sent,approved,rejected,expired,converted',
            ]);

            $quotation->update(['status' => $request->status]);

            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'Quotation marked as ' . ucfirst($request->status) . '.');
        }

        $companyId = Auth::user()->company_id;

        $request->validate([
            'client_id'            => ['required', \Illuminate\Validation\Rule::exists('clients', 'id')->where('company_id', $companyId)],
            'quotation_number'     => ['sometimes', 'required', 'string', \Illuminate\Validation\Rule::unique('quotations', 'quotation_number')->where('company_id', $companyId)->ignore($quotation->id)],
            'status'               => 'nullable|in:draft,sent,approved,rejected,expired,converted',
            'issue_date'           => 'required|date',
            'expiry_date'          => 'nullable|date|after_or_equal:issue_date',
            'notes'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.description'  => 'required|string',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.item_type_id' => 'nullable|exists:item_types,id',
        ]);

        DB::transaction(function () use ($request, $quotation) {
            $totals = $this->calculateTotals($request->input('items'));
            $totals = $this->applyDiscount($totals, $request);

            $quotation->update([
                'client_id'           => $request->client_id,
                'quotation_number'    => $request->input('quotation_number', $quotation->quotation_number),
                'status'              => $request->input('status', $quotation->status),
                'issue_date'          => $request->issue_date,
                'expiry_date'         => $request->expiry_date,
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

            $quotation->items()->delete();
            $this->saveItems($quotation, $request->input('items'));
        });

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation updated.');
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function destroy(Quotation $quotation): RedirectResponse
    {
        $this->authorizeQuotation($quotation);
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }

    // -------------------------------------------------------------------------
    // Send / Reminders
    // -------------------------------------------------------------------------

    public function send(Quotation $quotation): RedirectResponse
    {
        $this->authorizeQuotation($quotation);

        if (!$quotation->client->email) {
            return back()->with('error', 'This client has no email address.');
        }

        $quotation->load('client', 'items', 'items.itemType', 'company');
        $company = Auth::user()->company;

        try {
            $company->sendMail($quotation->client->email, new QuotationMail($quotation, $company), Auth::user());
            if ($quotation->status === 'draft') {
                $quotation->update(['status' => 'sent']);
            }

            $copies = $company->getCopyRecipients($quotation->client->email, Auth::user());
            $copyNote = !empty($copies) ? ' (Copy sent to ' . implode(', ', $copies) . ')' : '';

            return back()->with('success', 'Quotation sent to ' . $quotation->client->email . $copyNote);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function sendReminder(Quotation $quotation): RedirectResponse
    {
        $this->authorizeQuotation($quotation);

        if (!$quotation->client->email) {
            return back()->with('error', 'This client has no email address on record.');
        }

        $quotation->load('client', 'items', 'items.itemType', 'company');
        $company = Auth::user()->company;

        try {
            $company->sendMail(
                $quotation->client->email,
                new QuotationReminderMail($quotation, $company->name ?? Auth::user()->name),
                Auth::user()
            );

            $copies = $company->getCopyRecipients($quotation->client->email, Auth::user());
            $copyNote = !empty($copies) ? ' (Copy sent to ' . implode(', ', $copies) . ')' : '';

            return back()->with('success', 'Quotation reminder sent to ' . $quotation->client->email . $copyNote);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send reminder: ' . $e->getMessage());
        }
    }

    public function whatsappReminder(Quotation $quotation): RedirectResponse
    {
        $this->authorizeQuotation($quotation);

        $company = Auth::user()->company;
        $phone   = $this->normalizePhone($quotation->client->phone ?? '');

        $expiry = $quotation->expiry_date
            ? Carbon::parse($quotation->expiry_date)->format('d M Y')
            : 'soon';

        $amount = number_format($quotation->grand_total, 2);

        $message = "Hello {$quotation->client->name},\n\n"
            . "I hope you're doing well! I wanted to follow up on Quotation "
            . "*{$quotation->quotation_number}* for *Ksh {$amount}* that I sent you.\n\n"
            . "The quotation is valid until *{$expiry}*.\n\n"
            . "✅ *View & Approve Quotation Online:*\n{$quotation->public_url}\n\n"
            . "Please let me know if you have any questions or would like to proceed.\n\n"
            . "Regards,\n{$company->name}";

        return redirect('https://wa.me/' . $phone . '?text=' . urlencode($message));
    }

    // -------------------------------------------------------------------------
    // Convert to Invoice
    // -------------------------------------------------------------------------

    public function convertToInvoice(Quotation $quotation): RedirectResponse
    {
        $this->authorizeQuotation($quotation);

        if ($quotation->status === 'converted') {
            return back()->with('error', 'This quotation has already been converted.');
        }

        DB::transaction(function () use ($quotation) {
            // Scoped to company so invoice numbers don't collide across tenants
            $last = Invoice::where('company_id', $quotation->company_id)
                ->orderByDesc('id')
                ->value('invoice_number');

            $lastNumber    = $last ? (int) substr($last, 4) : 0;
            $invoiceNumber = 'INV-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'company_id'            => $quotation->company_id,
                'client_id'             => $quotation->client_id,
                'invoice_number'        => $invoiceNumber,
                'issue_date'            => now()->toDateString(),
                'due_date'              => now()->addDays(14)->toDateString(),
                'status'                => 'draft',
                'etr_enabled'           => false,
                'vat_amount'            => 0,
                'material_cost'         => $quotation->material_cost,
                'labour_cost'           => $quotation->labour_cost,
                'discount_amount'       => $quotation->discount_amount,
                'discount_percentage'   => $quotation->discount_percentage,
                'grand_total'           => $quotation->grand_total,
                'total_cost'            => $quotation->total_cost,
                'total_profit'          => $quotation->total_profit,
                'overall_margin'        => $quotation->overall_margin,
                'profit_from_quotation' => true,
                'notes'                 => $quotation->notes,
                'created_by'            => Auth::id(),
            ]);

            foreach ($quotation->items as $item) {
                InvoiceItem::create([
                    'invoice_id'      => $invoice->id,
                    'catalog_item_id' => $item->catalog_item_id,
                    'description'     => $item->description,
                    'quantity'        => $item->quantity,
                    'unit_price'      => $item->unit_price,
                    'buying_price'    => $item->buying_price,
                    'total_price'     => $item->total_price,
                    'is_labour'       => $item->is_labour,
                    'item_type_id'    => $item->item_type_id,
                ]);
            }

            $quotation->update([
                'status'               => 'converted',
                'converted_invoice_id' => $invoice->id,
            ]);
        });

        return redirect()->route('invoices.index')
            ->with('success', 'Quotation converted to invoice successfully.');
    }

    // -------------------------------------------------------------------------
    // PDF Download & Delivery Note
    // -------------------------------------------------------------------------

    public function download(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);
        $this->requireActiveSubscription('download quotations');

        $quotation->load('client', 'items', 'items.itemType');
        $company = Auth::user()->company;

        $pdf = Pdf::loadView('quotations.pdf', compact('quotation', 'company'));

        return $pdf->download('quotation-' . $quotation->quotation_number . '.pdf');
    }

    public function downloadDeliveryNote(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);

        $quotation->load('client', 'items', 'items.itemType');
        $company = Auth::user()->company;

        $pdf = Pdf::loadView('pdf.delivery-note', [
            'company'        => $company,
            'client'         => $quotation->client,
            'items'          => $quotation->items,
            'documentNumber' => $quotation->quotation_number,
            'notes'          => $quotation->notes,
            'quotation'      => $quotation,
        ]);

        return $pdf->download('delivery-note-' . $quotation->quotation_number . '.pdf');
    }

    // -------------------------------------------------------------------------
    // Duplicate
    // -------------------------------------------------------------------------

    public function duplicate(Quotation $quotation): RedirectResponse
    {
        $this->authorizeQuotation($quotation);

        $quotation->load('items');

        DB::transaction(function () use ($quotation) {
            $newQuotation                       = $quotation->replicate();
            $newQuotation->quotation_number     = $this->generateQuotationNumber();
            $newQuotation->public_token         = \Illuminate\Support\Str::random(32);
            $newQuotation->status               = 'draft';
            $newQuotation->approved_at          = null;
            $newQuotation->issue_date           = now()->toDateString();
            $newQuotation->expiry_date          = now()->addDays(30)->toDateString();
            $newQuotation->converted_invoice_id = null;
            $newQuotation->created_by           = Auth::id();
            $newQuotation->save();

            foreach ($quotation->items as $item) {
                $newItem               = $item->replicate();
                $newItem->quotation_id = $newQuotation->id;
                $newItem->save();
            }
        });

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation duplicated as a new draft.');
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    /**
     * Calculate material, labour, cost, and subtotal from line items.
     */
    private function calculateTotals(array $items): array
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

        $subtotal = $materialCost + $labourCost;

        return compact('materialCost', 'labourCost', 'totalCost', 'subtotal');
    }

    /**
     * Apply discount and derive grand total, profit, and margin.
     */
    private function applyDiscount(array $totals, Request $request): array
    {
        $subtotal  = $totals['subtotal'];
        $totalCost = $totals['totalCost'];

        $discountPct    = (float) ($request->discount_percentage ?? 0);
        $discountAmount = $discountPct > 0
            ? round($subtotal * $discountPct / 100, 2)
            : round((float) ($request->discount_amount ?? 0), 2);

        $grandTotal    = max(0, $subtotal - $discountAmount);
        $totalProfit   = $grandTotal - $totalCost;
        $overallMargin = $grandTotal > 0 ? round(($totalProfit / $grandTotal) * 100, 2) : 0;

        return [
            'material_cost'  => $totals['materialCost'],
            'labour_cost'    => $totals['labourCost'],
            'total_cost'     => $totalCost,
            'discount_pct'   => $discountPct,
            'discount_amount'=> $discountAmount,
            'grand_total'    => $grandTotal,
            'total_profit'   => $totalProfit,
            'overall_margin' => $overallMargin,
        ];
    }

    /**
     * Persist quotation line items, including per-item profit/margin and
     * updating the catalog item's default buying price.
     */
    private function saveItems(Quotation $quotation, array $items): void
    {
        foreach ($items as $item) {
            $quantity    = $item['quantity'];
            $unitPrice   = $item['unit_price'];
            $buyingPrice = $item['buying_price'] ?? 0;
            $totalPrice  = $quantity * $unitPrice;
            $itemCost    = $quantity * $buyingPrice;
            $profit      = $totalPrice - $itemCost;
            $marginPct   = $totalPrice > 0 ? round(($profit / $totalPrice) * 100, 2) : 0;

            QuotationItem::create([
                'quotation_id'      => $quotation->id,
                'catalog_item_id'   => $item['catalog_item_id'] ?? null,
                'description'       => $item['description'],
                'quantity'          => $quantity,
                'unit_price'        => $unitPrice,
                'buying_price'      => $buyingPrice,
                'profit'            => $profit,
                'margin_percentage' => $marginPct,
                'total_price'       => $totalPrice,
                'is_labour'         => !empty($item['is_labour']),
                'item_type_id'      => $item['item_type_id'] ?? null,
            ]);

            if (!empty($item['catalog_item_id']) && $buyingPrice > 0) {
                \App\Models\CatalogItem::where('id', $item['catalog_item_id'])
                    ->where('company_id', $quotation->company_id)
                    ->update(['default_buying_price' => $buyingPrice]);
            }
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
     * Abort with 403 if the quotation does not belong to the authenticated user's company.
     */
    private function authorizeQuotation(Quotation $quotation): void
    {
        if ($quotation->company_id !== Auth::user()->company_id) {
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
     * Generate the next quotation number scoped to the current company.
     */
    private function generateQuotationNumber(): string
    {
        $last = Quotation::where('company_id', Auth::user()->company_id)
            ->orderByDesc('id')
            ->value('quotation_number');

        $lastNumber = $last ? (int) substr($last, 3) : 0;

        return 'QT-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }
}