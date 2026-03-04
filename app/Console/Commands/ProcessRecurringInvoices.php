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
                // Generate next invoice number
                $company     = $invoice->company;
                $lastInvoice = Invoice::where('company_id', $company->id)->latest()->first();
                $nextNumber  = $lastInvoice
                    ? 'INV-' . str_pad((intval(substr($lastInvoice->invoice_number, 4)) + 1), 5, '0', STR_PAD_LEFT)
                    : 'INV-00001';

                // Calculate new dates
                $newIssueDate = today();
                $newDueDate   = $invoice->due_date
                    ? $invoice->getNextRecurringDate()
                    : null;

                // Create new invoice
                $newInvoice = Invoice::create([
                    'company_id'         => $invoice->company_id,
                    'client_id'          => $invoice->client_id,
                    'invoice_number'     => $nextNumber,
                    'issue_date'         => $newIssueDate,
                    'due_date'           => $newDueDate,
                    'status'             => 'draft',
                    'notes'              => $invoice->notes,
                    'footer'             => $invoice->footer,
                    'tax_rate'           => $invoice->tax_rate,
                    'discount'           => $invoice->discount,
                    'subtotal'           => $invoice->subtotal,
                    'tax_amount'         => $invoice->tax_amount,
                    'grand_total'        => $invoice->grand_total,
                    'etr_enabled'        => $invoice->etr_enabled,
                    'is_recurring'       => false, // children are not recurring themselves
                    'recurring_parent_id' => $invoice->id,
                ]);

                // Copy line items
                foreach ($invoice->items as $item) {
                    InvoiceItem::create([
                        'invoice_id'    => $newInvoice->id,
                        'description'   => $item->description,
                        'quantity'      => $item->quantity,
                        'unit_price'    => $item->unit_price,
                        'buying_price'  => $item->buying_price,
                        'total'         => $item->total,
                        'is_labour'     => $item->is_labour,
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