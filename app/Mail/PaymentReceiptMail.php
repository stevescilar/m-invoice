<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public Company $company,
        public ?InvoicePayment $payment = null
    ) {}

    public function envelope(): Envelope
    {
        $receiptNo = $this->payment?->receipt_number ?? ('RCP-' . str_replace('INV-', '', $this->invoice->invoice_number));
        $replyTo = [];
        if (!empty($this->company->email) && filter_var($this->company->email, FILTER_VALIDATE_EMAIL)) {
            $replyTo[] = new Address($this->company->email, $this->company->name);
        }

        return new Envelope(
            subject: "Payment Receipt {$receiptNo} for Invoice {$this->invoice->invoice_number} — {$this->company->name}",
            replyTo: $replyTo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-receipt',
        );
    }

    public function attachments(): array
    {
        $this->invoice->loadMissing('client', 'items', 'items.itemType');

        $receiptNo = $this->payment?->receipt_number ?? ('RCP-' . str_replace('INV-', '', $this->invoice->invoice_number));

        $pdf = Pdf::loadView('invoices.receipt-pdf', [
            'invoice' => $this->invoice,
            'company' => $this->company,
            'payment' => $this->payment,
        ]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'receipt-' . $receiptNo . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
