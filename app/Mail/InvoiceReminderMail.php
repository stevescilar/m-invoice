<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public string $senderName
    ) {}

    public function envelope(): Envelope
    {
        $company = $this->invoice->company;
        $replyTo = array_filter([$company?->email, $company?->owner?->email]);
        $subject = $this->invoice->status === 'overdue'
            ? 'Overdue Payment: Invoice ' . $this->invoice->invoice_number . ' from ' . ($company?->name ?? $this->senderName)
            : 'Payment Reminder: Invoice ' . $this->invoice->invoice_number . ' from ' . ($company?->name ?? $this->senderName);

        return new Envelope(
            replyTo: !empty($replyTo) ? [reset($replyTo)] : [],
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.invoice-reminder');
    }

    public function attachments(): array
    {
        $this->invoice->loadMissing('client', 'items', 'items.itemType', 'company');
        $company = $this->invoice->company;
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $this->invoice,
            'company' => $company,
        ])->setPaper('a4');

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'Invoice-' . $this->invoice->invoice_number . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}