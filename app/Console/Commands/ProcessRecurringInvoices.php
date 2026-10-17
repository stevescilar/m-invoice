<?php

namespace App\Console\Commands;

use App\Mail\RecurringInvoiceGeneratedMail;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessRecurringInvoices extends Command
{
    protected $signature   = 'invoices:process-recurring';
    protected $description = 'Generate recurring invoices that are due today';

    public function handle(): void
    {
        $due = Invoice::where('is_recurring', true)
            ->where('recurring_active', true)
            ->whereDate('recurring_next_date', '<=', today())
            ->where(function($q) {
                $q->whereNull('recurring_ends_at')
                  ->orWhereDate('recurring_ends_at', '>=', today());
            })
            ->get();

        $this->info("Found {$due->count()} recurring invoice(s) to process.");

        foreach ($due as $invoice) {
            try {
                // Generate next invoice number (6-digit padding, matching InvoiceController)
                $company    = $invoice->company;
                $lastNumber = Invoice::where('company_id', $company->id)
                    ->orderByDesc('id')
                    ->value('invoice_number');
                $nextInt    = $lastNumber ? ((int) substr($lastNumber, 4) + 1) : 1;
                $nextNumber = 'INV-' . str_pad($nextInt, 6, '0', STR_PAD_LEFT);

                // Calculate new dates
                $newIssueDate = today();
                $newDueDate   = $invoice->due_date
                    ? $invoice->getNextRecurringDate()
                    : null;

                // Create new invoice
                $newInvoice = Invoice::create([
                    'company_id'            => $invoice->company_id,
                    'client_id'             => $invoice->client_id,
                    'invoice_number'        => $nextNumber,
                    'issue_date'            => $newIssueDate,
                    'due_date'              => $newDueDate,
                    'status'                => 'draft',
                    'notes'                 => $invoice->notes,
                    'discount_amount'       => $invoice->discount_amount,
                    'discount_percentage'   => $invoice->discount_percentage,
                    'grand_total'           => $invoice->grand_total,
                    'total_cost'            => $invoice->total_cost,
                    'total_profit'          => $invoice->total_profit,
                    'overall_margin'        => $invoice->overall_margin,
                    'material_cost'         => $invoice->material_cost,
                    'labour_cost'           => $invoice->labour_cost,
                    'vat_amount'            => $invoice->vat_amount,
                    'etr_enabled'           => $invoice->etr_enabled,
                    'profit_from_quotation' => $invoice->profit_from_quotation,
                    'is_recurring'          => false,
                    'recurring_parent_id'   => $invoice->id,
                    'created_by'            => $invoice->created_by,
                ]);

                // Copy line items
                foreach ($invoice->items as $item) {
                    InvoiceItem::create([
                        'invoice_id'      => $newInvoice->id,
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

                // Update next date on parent
                $invoice->update([
                    'recurring_next_date' => $invoice->getNextRecurringDate(),
                ]);

                // Notify owner
                $owner = $company->owner;
                if ($owner && $owner->email) {
                    Mail::to($owner->email)
                        ->send(new RecurringInvoiceGeneratedMail($invoice, $newInvoice));
                }

                $this->info("✓ Generated {$newInvoice->invoice_number} from {$invoice->invoice_number}");

            } catch (\Exception $e) {
                $this->error("✗ Failed for invoice {$invoice->invoice_number}: " . $e->getMessage());
            }
        }

        $this->info('Done.');
    }
}