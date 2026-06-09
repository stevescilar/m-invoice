<?php

namespace App\Mail;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quotation $quotation,
        public string $senderName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Following Up: Quotation ' . $this->quotation->quotation_number . ' from ' . $this->senderName,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.quotation-reminder');
    }

    public function attachments(): array
    {
        $company = $this->quotation->company;
        $pdf = Pdf::loadView('quotations.pdf', [
            'quotation' => $this->quotation,
            'company'   => $company,
        ])->setPaper('a4');

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'Quotation-' . $this->quotation->quotation_number . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}