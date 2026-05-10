<?php

namespace App\Mail;

use App\Models\SupportTicket;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SupportTicketSubmittedMail extends Mailable
{
    public function __construct(public SupportTicket $ticket)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '['.$this->ticket->reference.'] '.$this->ticket->subject,
            replyTo: [
                $this->ticket->email,
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.support.submitted',
        );
    }
}
