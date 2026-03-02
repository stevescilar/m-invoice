<?php

namespace App\Jobs;

use App\Mail\InvoiceReminderMail;
use App\Models\Invoice;
use App\Models\InvoiceReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendInvoiceReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $reminders = InvoiceReminder::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->with(['invoice' => function ($q) {
                $q->with(['client', 'items', 'company']);
            }])
            ->get();

        foreach ($reminders as $reminder) {
            $invoice = $reminder->invoice;

            // Skip if invoice is already paid or client has no email
            if ($invoice->status === 'paid' || !$invoice->client->email) {
                $reminder->update(['status' => 'skipped']);
                continue;
            }

            $company = $invoice->company;

            try {
                Mail::to($invoice->client->email)
                    ->send(new InvoiceReminderMail($invoice, $company, $reminder->reminder_type));

                $reminder->update([
                    'status'  => 'sent',
                    'sent_at' => now(),
                ]);

                // Mark overdue invoices as overdue
                if ($reminder->reminder_type === 'after_due' && $invoice->status !== 'paid') {
                    $invoice->update(['status' => 'overdue']);
                }

                Log::info("Reminder sent for invoice {$invoice->invoice_number} to {$invoice->client->email}");

            } catch (\Exception $e) {
                $reminder->update(['status' => 'failed']);
                Log::error("Reminder failed for invoice {$invoice->invoice_number}: " . $e->getMessage());
            }
        }
    }
}