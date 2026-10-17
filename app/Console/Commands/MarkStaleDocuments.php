<?php

namespace App\Console\Commands;

use App\Mail\InvoiceOverdueMail;
use App\Mail\QuotationExpiredMail;
use App\Mail\QuotationReminderMail;
use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MarkStaleDocuments extends Command
{
    protected $signature   = 'documents:mark-stale';
    protected $description = 'Mark overdue invoices and expired quotations, send quotation reminders, and notify owners';

    public function handle(): void
    {
        $this->markOverdueInvoices();
        $this->sendUpcomingQuotationReminders();
        $this->markExpiredQuotations();
        $this->info('Done.');
    }

    // ─── Invoices ────────────────────────────────────────────────────────────

    private function markOverdueInvoices(): void
    {
        // Invoices past due date that are still sent/draft/stalled (not paid, already overdue)
        $invoices = Invoice::whereIn('status', ['sent', 'draft', 'stalled'])
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
                $owner = $invoice->company?->owner;
                if ($owner && $owner->email) {
                    $invoice->company->sendMail($owner->email, new InvoiceOverdueMail($invoice), null, false);
                }

                $this->info("✓ Invoice {$invoice->invoice_number} marked overdue");
            } catch (\Exception $e) {
                $this->error("✗ Invoice {$invoice->invoice_number}: " . $e->getMessage());
            }
        }

        $this->info("Invoices: {$count} marked overdue.");
    }

    // ─── Quotations ──────────────────────────────────────────────────────────

    private function sendUpcomingQuotationReminders(): void
    {
        // Sent quotations expiring in 2 days or today
        $targetDates = [today()->toDateString(), today()->addDays(2)->toDateString()];

        $quotations = Quotation::where('status', 'sent')
            ->whereNotNull('expiry_date')
            ->whereIn('expiry_date', $targetDates)
            ->with('company.owner', 'client', 'items', 'items.itemType')
            ->get();

        $count = 0;

        foreach ($quotations as $quotation) {
            $company = $quotation->company;
            if (!$company || !($company->auto_quotation_reminders ?? true) || !$quotation->client?->email) {
                continue;
            }

            try {
                $company->sendMail(
                    $quotation->client->email,
                    new QuotationReminderMail($quotation, $company->name ?? 'Invoxa')
                );
                $count++;
                $this->info("✓ Quotation reminder sent for {$quotation->quotation_number}");
            } catch (\Exception $e) {
                $this->error("✗ Quotation reminder {$quotation->quotation_number}: " . $e->getMessage());
            }
        }

        $this->info("Quotation reminders: {$count} sent.");
    }

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
                $owner = $quotation->company?->owner;
                if ($owner && $owner->email) {
                    $quotation->company->sendMail($owner->email, new QuotationExpiredMail($quotation), null, false);
                }

                $this->info("✓ Quotation {$quotation->quotation_number} marked expired");
            } catch (\Exception $e) {
                $this->error("✗ Quotation {$quotation->quotation_number}: " . $e->getMessage());
            }
        }

        $this->info("Quotations: {$count} marked expired.");
    }
}