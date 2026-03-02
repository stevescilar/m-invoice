<?php

namespace App\Http\Controllers\Quotation;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::where('company_id', Auth::user()->company_id)
            ->with('client')
            ->latest()
            ->paginate(15);

        $stats = [
            'all'       => Quotation::where('company_id', Auth::user()->company_id)->count(),
            'draft'     => Quotation::where('company_id', Auth::user()->company_id)->where('status', 'draft')->count(),
            'sent'      => Quotation::where('company_id', Auth::user()->company_id)->where('status', 'sent')->count(),
            'approved'  => Quotation::where('company_id', Auth::user()->company_id)->where('status', 'approved')->count(),
            'rejected'  => Quotation::where('company_id', Auth::user()->company_id)->where('status', 'rejected')->count(),
            'converted' => Quotation::where('company_id', Auth::user()->company_id)->where('status', 'converted')->count(),
        ];

        return view('quotations.index', compact('quotations', 'stats'));
    }

    public function create()
    {
        $clients = Client::where('company_id', Auth::user()->company_id)->orderBy('name')->get();
        $categories = ServiceCategory::where('company_id', Auth::user()->company_id)
            ->with('catalogItems')
            ->get();

        $quotationNumber = $this->generateQuotationNumber();

        return view('quotations.create', compact('clients', 'categories', 'quotationNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id'          => 'required|exists:clients,id',
            'quotation_number'   => 'required|string|unique:quotations,quotation_number',
            'issue_date'         => 'required|date',
            'expiry_date'        => 'nullable|date|after_or_equal:issue_date',
            'notes'              => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'   => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $items        = $request->input('items');
            $materialCost = 0;
            $labourCost   = 0;
            $totalCost    = 0;

            foreach ($items as $item) {
                $sellTotal = $item['quantity'] * $item['unit_price'];
                $costTotal = $item['quantity'] * ($item['buying_price'] ?? 0);
                $totalCost += $costTotal;
                if (!empty($item['is_labour'])) {
                    $labourCost += $sellTotal;
                } else {
                    $materialCost += $sellTotal;
                }
            }

            $grandTotal    = $materialCost + $labourCost;
            $totalProfit   = $grandTotal - $totalCost;
            $overallMargin = $grandTotal > 0 ? round(($totalProfit / $grandTotal) * 100, 2) : 0;

            $quotation = Quotation::create([
                'company_id'      => Auth::user()->company_id,
                'client_id'       => $request->client_id,
                'quotation_number' => $request->quotation_number,
                'issue_date'      => $request->issue_date,
                'expiry_date'     => $request->expiry_date,
                'status'          => 'draft',
                'material_cost'   => $materialCost,
                'labour_cost'     => $labourCost,
                'grand_total'     => $grandTotal,
                'total_cost'      => $totalCost,
                'total_profit'    => $totalProfit,
                'overall_margin'  => $overallMargin,
                'notes'           => $request->notes,
                'created_by'      => Auth::id(),
            ]);

            foreach ($items as $item) {
                $quantity        = $item['quantity'];
                $unitPrice       = $item['unit_price'];
                $buyingPrice     = $item['buying_price'] ?? 0;
                $totalPrice      = $quantity * $unitPrice;
                $itemCost        = $quantity * $buyingPrice;
                $profit          = $totalPrice - $itemCost;
                $marginPct       = $totalPrice > 0 ? round(($profit / $totalPrice) * 100, 2) : 0;

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
                ]);
                // Update catalog item buying price if it came from catalog
                if (!empty($item['catalog_item_id'])) {
                    \App\Models\CatalogItem::where('id', $item['catalog_item_id'])
                        ->update(['default_buying_price' => $buyingPrice]);
                }
            }
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation created successfully.');
    }

    public function show(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);
        $quotation->load('client', 'items');
        $company = Auth::user()->company;
        return view('quotations.show', compact('quotation', 'company'));
    }

    public function edit(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);

        if (in_array($quotation->status, ['converted', 'approved'])) {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'This quotation cannot be edited.');
        }

        $clients = Client::where('company_id', Auth::user()->company_id)->orderBy('name')->get();
        $categories = ServiceCategory::where('company_id', Auth::user()->company_id)
            ->with('catalogItems')
            ->get();

        $quotation->load('items');

        $quotationItems = $quotation->items->map(function ($i) {
            return [
                'catalog_item_id' => $i->catalog_item_id,
                'description'     => $i->description,
                'quantity'        => (float) $i->quantity,
                'unit_price'      => (float) $i->unit_price,
                'buying_price'    => (float) $i->buying_price,
                'is_labour'       => (bool) $i->is_labour,
            ];
        })->values()->toArray();

        return view('quotations.edit', compact('quotation', 'clients', 'categories', 'quotationItems'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);

        $request->validate([
            'client_id'           => 'required|exists:clients,id',
            'issue_date'          => 'required|date',
            'expiry_date'         => 'nullable|date',
            'notes'               => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $quotation) {
            $items        = $request->input('items');
            $materialCost = 0;
            $labourCost   = 0;
            $totalCost    = 0;

            foreach ($items as $item) {
                $sellTotal = $item['quantity'] * $item['unit_price'];
                $costTotal = $item['quantity'] * ($item['buying_price'] ?? 0);
                $totalCost += $costTotal;
                if (!empty($item['is_labour'])) {
                    $labourCost += $sellTotal;
                } else {
                    $materialCost += $sellTotal;
                }
            }

            $grandTotal    = $materialCost + $labourCost;
            $totalProfit   = $grandTotal - $totalCost;
            $overallMargin = $grandTotal > 0 ? round(($totalProfit / $grandTotal) * 100, 2) : 0;

            $quotation->update([
                'client_id'      => $request->client_id,
                'issue_date'     => $request->issue_date,
                'expiry_date'    => $request->expiry_date,
                'material_cost'  => $materialCost,
                'labour_cost'    => $labourCost,
                'grand_total'    => $grandTotal,
                'total_cost'     => $totalCost,
                'total_profit'   => $totalProfit,
                'overall_margin' => $overallMargin,
                'notes'          => $request->notes,
            ]);

            $quotation->items()->delete();

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
                ]);
            }
        });

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation updated.');
    }

    public function destroy(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }

    public function send(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);
        $quotation->update(['status' => 'sent']);
        return back()->with('success', 'Quotation marked as sent.');
    }

    public function convertToInvoice(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);

        if ($quotation->status === 'converted') {
            return back()->with('error', 'This quotation has already been converted.');
        }

        DB::transaction(function () use ($quotation) {
            $count         = Invoice::where('company_id', Auth::user()->company_id)->count() + 1;
            $invoiceNumber = 'INV-' . str_pad($count, 6, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'company_id'             => $quotation->company_id,
                'client_id'              => $quotation->client_id,
                'invoice_number'         => $invoiceNumber,
                'issue_date'             => now()->toDateString(),
                'due_date'               => now()->addDays(14)->toDateString(),
                'status'                 => 'draft',
                'etr_enabled'            => false,
                'vat_amount'             => 0,
                'material_cost'          => $quotation->material_cost,
                'labour_cost'            => $quotation->labour_cost,
                'grand_total'            => $quotation->grand_total,
                'total_cost'             => $quotation->total_cost,
                'total_profit'           => $quotation->total_profit,
                'overall_margin'         => $quotation->overall_margin,
                'profit_from_quotation'  => true,
                'notes'                  => $quotation->notes,
                'created_by'             => Auth::id(),
            ]);

            foreach ($quotation->items as $item) {
                InvoiceItem::create([
                    'invoice_id'      => $invoice->id,
                    'catalog_item_id' => $item->catalog_item_id,
                    'description'     => $item->description,
                    'quantity'        => $item->quantity,
                    'unit_price'      => $item->unit_price,
                    'total_price'     => $item->total_price,
                    'is_labour'       => $item->is_labour,
                ]);
            }

            $quotation->update([
                'status'               => 'converted',
                'converted_invoice_id' => $invoice->id,
            ]);
        });

        return redirect()->route('invoices.index')->with('success', 'Quotation converted to invoice successfully.');
    }

    public function download(Quotation $quotation)
    {
        $this->authorizeQuotation($quotation);
        $quotation->load('client', 'items');
        $company = Auth::user()->company;

        $pdf = Pdf::loadView('quotations.pdf', compact('quotation', 'company'));

        return $pdf->download('quotation-' . $quotation->quotation_number . '.pdf');
    }

    private function generateQuotationNumber(): string
    {
        $count = Quotation::where('company_id', Auth::user()->company_id)->count() + 1;
        return 'QT-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    private function authorizeQuotation(Quotation $quotation): void
    {
        if ($quotation->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}