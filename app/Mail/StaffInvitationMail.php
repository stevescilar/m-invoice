<?php

namespace App\Mail;

use App\Models\StaffInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public StaffInvitation $invitation;

    public function __construct(StaffInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been invited to join ' . $this->invitation->company->name . ' on M-Invoice',
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.staff-invitation');
    }
}