<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public Company $company;
    public string $reminderType;

    public function __construct(Invoice $invoice, Company $company, string $reminderType)
    {
        $this->invoice      = $invoice;
        $this->company      = $company;
        $this->reminderType = $reminderType;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'before_due' => 'Payment Reminder: Invoice ' . $this->invoice->invoice_number . ' due soon',
            'on_due'     => 'Payment Due Today: Invoice ' . $this->invoice->invoice_number,
            'after_due'  => 'Overdue Notice: Invoice ' . $this->invoice->invoice_number,
        ];

        return new Envelope(
            subject: $subjects[$this->reminderType] ?? 'Invoice Reminder',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-reminder',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $this->invoice,
            'company' => $this->company,
        ]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'Invoice-' . $this->invoice->invoice_number . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}