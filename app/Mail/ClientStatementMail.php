<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientStatementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Client $client,
        public Company $company,
        public Collection $invoices,
        public float $totalBilled,
        public float $totalPaid,
        public float $balanceDue,
        public ?string $fromDate = null,
        public ?string $toDate = null
    ) {}

    public function envelope(): Envelope
    {
        $replyTo = [];
        if (!empty($this->company->email) && filter_var($this->company->email, FILTER_VALIDATE_EMAIL)) {
            $replyTo[] = new Address($this->company->email, $this->company->name);
        }

        return new Envelope(
            subject: "Statement of Account — {$this->company->name}",
            replyTo: $replyTo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-statement',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('clients.statement-pdf', [
            'client'      => $this->client,
            'company'     => $this->company,
            'invoices'    => $this->invoices,
            'totalBilled' => $this->totalBilled,
            'totalPaid'   => $this->totalPaid,
            'balanceDue'  => $this->balanceDue,
            'fromDate'    => $this->fromDate,
            'toDate'      => $this->toDate,
        ]);

        $slug = \Illuminate\Support\Str::slug($this->client->name ?: 'client');

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                "statement-of-account-{$slug}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}

