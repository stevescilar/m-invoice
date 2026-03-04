<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecurringInvoiceGeneratedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public Invoice $newInvoice;

    public function __construct(Invoice $invoice, Invoice $newInvoice)
    {
        $this->invoice    = $invoice;
        $this->newInvoice = $newInvoice;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recurring Invoice Generated — ' . $this->newInvoice->invoice_number,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.recurring-invoice-generated');
    }
}