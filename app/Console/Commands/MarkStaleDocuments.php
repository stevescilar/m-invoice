<?php

namespace App\Console\Commands;

use App\Mail\InvoiceOverdueMail;
use App\Mail\QuotationExpiredMail;
use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MarkStaleDocuments extends Command
{
    protected $signature   = 'documents:mark-stale';
    protected $description = 'Mark overdue invoices and expired quotations, then notify owners';

    public function handle(): void
    {
        $this->markOverdueInvoices();
        $this->markExpiredQuotations();
        $this->info('Done.');
    }

    // ─── Invoices ────────────────────────────────────────────────────────────

    private function markOverdueInvoices(): void
    {
        // Invoices past due date that are still sent/draft (not paid, cancelled, already overdue)
        $invoices = Invoice::whereIn('status', ['sent', 'draft'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->with('company.owner', 'client')
            ->get();

        $count = 0;

        foreach ($invoices as $invoice) {
            try {
                $invoice->update(['status' => 'overdue']);
                $count++;

                // Notify the company owner
                $owner = $invoice->company->owner;
                if ($owner && $owner->email) {
                    Mail::to($owner->email)
                        ->send(new InvoiceOverdueMail($invoice));
                }

                $this->info("✓ Invoice {$invoice->invoice_number} marked overdue");
            } catch (\Exception $e) {
                $this->error("✗ Invoice {$invoice->invoice_number}: " . $e->getMessage());
            }
        }

        $this->info("Invoices: {$count} marked overdue.");
    }

    // ─── Quotations ──────────────────────────────────────────────────────────

    private function markExpiredQuotations(): void
    {
        // Quotations past expiry date that are still draft/sent (not converted, approved, rejected, already expired)
        $quotations = Quotation::whereIn('status', ['draft', 'sent'])
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', today())
            ->with('company.owner', 'client')
            ->get();

        $count = 0;

        foreach ($quotations as $quotation) {
            try {
                $quotation->update(['status' => 'expired']);
                $count++;

                // Notify the company owner
                $owner = $quotation->company->owner;
                if ($owner && $owner->email) {
                    Mail::to($owner->email)
                        ->send(new QuotationExpiredMail($quotation));
                }

                $this->info("✓ Quotation {$quotation->quotation_number} marked expired");
            } catch (\Exception $e) {
                $this->error("✗ Quotation {$quotation->quotation_number}: " . $e->getMessage());
            }
        }

        $this->info("Quotations: {$count} marked expired.");
    }
}