<?php

namespace App\Mail;

use App\Models\Quotation;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Quotation $quotation;
    public Company $company;

    public function __construct(Quotation $quotation, Company $company)
    {
        $this->quotation = $quotation;
        $this->company   = $company;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Quotation ' . $this->quotation->quotation_number . ' from ' . $this->company->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quotation',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('quotations.pdf', [
            'quotation' => $this->quotation,
            'company'   => $this->company,
        ]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'Quotation-' . $this->quotation->quotation_number . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}