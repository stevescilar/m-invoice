<?php

namespace App\Http\Controllers\Invoice;

use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceReminder;
use App\Models\Client;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('company_id', Auth::user()->company_id)
            ->with('client')
            ->latest()
            ->paginate(15);

        $stats = [
            'all' => Invoice::where('company_id', Auth::user()->company_id)->count(),
            'draft' => Invoice::where('company_id', Auth::user()->company_id)->where('status', 'draft')->count(),
            'sent' => Invoice::where('company_id', Auth::user()->company_id)->where('status', 'sent')->count(),
            'paid' => Invoice::where('company_id', Auth::user()->company_id)->where('status', 'paid')->count(),
            'overdue' => Invoice::where('company_id', Auth::user()->company_id)->where('status', 'overdue')->count(),
            'stalled' => Invoice::where('company_id', Auth::user()->company_id)->where('status', 'stalled')->count(),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function create()
    {
        $clients = Client::where('company_id', Auth::user()->company_id)->orderBy('name')->get();
        $categories = ServiceCategory::where('company_id', Auth::user()->company_id)
            ->with('catalogItems')
            ->get();

        $invoiceNumber = $this->generateInvoiceNumber();

        return view('invoices.create', compact('clients', 'categories', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
            'etr_enabled' => 'boolean',
            'is_recurring' => 'boolean',
            'recurrence_interval' => 'nullable|in:weekly,monthly',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.is_labour' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request) {
            $etrEnabled = $request->boolean('etr_enabled');
            $items = $request->input('items');

            $materialCost = 0;
            $labourCost = 0;

            foreach ($items as $item) {
                $total = $item['quantity'] * $item['unit_price'];
                if (!empty($item['is_labour'])) {
                    $labourCost += $total;
                } else {
                    $materialCost += $total;
                }
            }

            $vatAmount = $etrEnabled ? round($materialCost * 0.16, 2) : 0;
            $grandTotal = $materialCost + $labourCost + $vatAmount;

            $invoice = Invoice::create([
                'company_id' => Auth::user()->company_id,
                'client_id' => $request->client_id,
                'invoice_number' => $request->invoice_number,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'status' => 'draft',
                'etr_enabled' => $etrEnabled,
                'vat_amount' => $vatAmount,
                'material_cost' => $materialCost,
                'labour_cost' => $labourCost,
                'grand_total' => $grandTotal,
                'notes' => $request->notes,
                'is_recurring' => $request->boolean('is_recurring'),
                'recurrence_interval' => $request->recurrence_interval,
                'next_recurrence_date' => $request->boolean('is_recurring') ? $this->getNextRecurrenceDate($request->issue_date, $request->recurrence_interval) : null,
                'created_by' => Auth::id(),
            ]);

            foreach ($items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'catalog_item_id' => $item['catalog_item_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'is_labour' => !empty($item['is_labour']),
                ]);
            }

            // Create reminders
            if ($request->due_date) {
                $this->createReminders($invoice);
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->load('client', 'items', 'items.catalogItem');
        $company = Auth::user()->company;
        return view('invoices.show', compact('invoice', 'company'));
    }

    public function edit(Invoice $invoice)
{
    $this->authorizeInvoice($invoice);

    if ($invoice->status === 'paid') {
        return redirect()->route('invoices.show', $invoice)
            ->with('error', 'Paid invoices cannot be edited.');
    }

    $clients = Client::where('company_id', Auth::user()->company_id)->orderBy('name')->get();
    $categories = ServiceCategory::where('company_id', Auth::user()->company_id)
        ->with('catalogItems')
        ->get();

    $invoice->load('items');

    $invoiceItems = $invoice->items->map(function($i) {
        return [
            'catalog_item_id' => $i->catalog_item_id,
            'description' => $i->description,
            'quantity' => (float)$i->quantity,
            'unit_price' => (float)$i->unit_price,
            'is_labour' => (bool)$i->is_labour,
        ];
    })->values()->toArray();

    return view('invoices.edit', compact('invoice', 'clients', 'categories', 'invoiceItems'));
}

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
            'etr_enabled' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            $etrEnabled = $request->boolean('etr_enabled');
            $items = $request->input('items');

            $materialCost = 0;
            $labourCost = 0;

            foreach ($items as $item) {
                $total = $item['quantity'] * $item['unit_price'];
                if (!empty($item['is_labour'])) {
                    $labourCost += $total;
                } else {
                    $materialCost += $total;
                }
            }

            $vatAmount = $etrEnabled ? round($materialCost * 0.16, 2) : 0;
            $grandTotal = $materialCost + $labourCost + $vatAmount;

            $invoice->update([
                'client_id' => $request->client_id,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'etr_enabled' => $etrEnabled,
                'vat_amount' => $vatAmount,
                'material_cost' => $materialCost,
                'labour_cost' => $labourCost,
                'grand_total' => $grandTotal,
                'notes' => $request->notes,
            ]);

            // Delete old items and recreate
            $invoice->items()->delete();

            foreach ($items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'catalog_item_id' => $item['catalog_item_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'is_labour' => !empty($item['is_labour']),
                ]);
            }
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    public function send(Invoice $invoice)
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

    public function markPaid(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $request->validate([
            'mpesa_code' => 'nullable|string|max:20',
        ]);

        $invoice->update([
            'status' => 'paid',
            'mpesa_code' => $request->mpesa_code,
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Invoice marked as paid.');
    }

    public function download(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $company      = Auth::user()->company;
        $subscription = $company->subscription;

        // Check if allowed to download
        if (!$subscription || !$subscription->canDownloadPdf()) {
            return redirect()->route('subscription.index')
                ->with('error', 'Your trial has expired. Please subscribe to download invoices.');
        }

        $invoice->load('client', 'items');
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'company'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function duplicate(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        DB::transaction(function () use ($invoice) {
            $newInvoice = $invoice->replicate();
            $newInvoice->invoice_number = $this->generateInvoiceNumber();
            $newInvoice->status = 'draft';
            $newInvoice->issue_date = now();
            $newInvoice->paid_at = null;
            $newInvoice->mpesa_code = null;
            $newInvoice->save();

            foreach ($invoice->items as $item) {
                $newItem = $item->replicate();
                $newItem->invoice_id = $newInvoice->id;
                $newItem->save();
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice duplicated successfully.');
    }

    private function generateInvoiceNumber(): string
    {
        $last = Invoice::orderByDesc('id')->value('invoice_number');
        $lastNumber = $last ? (int) substr($last, 4) : 0;
        return 'INV-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    private function getNextRecurrenceDate(string $date, string $interval): string
    {
        return $interval === 'weekly'
            ? now()->parse($date)->addWeek()->toDateString()
            : now()->parse($date)->addMonth()->toDateString();
    }

    private function createReminders(Invoice $invoice): void
    {
        $reminders = [
            ['type' => 'before_due', 'date' => $invoice->due_date->subDays(3)],
            ['type' => 'on_due', 'date' => $invoice->due_date],
            ['type' => 'after_due', 'date' => $invoice->due_date->addDays(7)],
        ];

        foreach ($reminders as $reminder) {
            InvoiceReminder::create([
                'invoice_id' => $invoice->id,
                'reminder_type' => $reminder['type'],
                'scheduled_at' => $reminder['date'],
                'status' => 'pending',
            ]);
        }
    }

    private function authorizeInvoice(Invoice $invoice): void
    {
        if ($invoice->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}