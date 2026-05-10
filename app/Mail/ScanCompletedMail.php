<?php

namespace App\Mail;

use App\Models\Scan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ScanCompletedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Scan $scan)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Ghostfrog scan is ready: '.$this->scan->keyword,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.scans.completed',
            with: [
                'accessUrl' => $this->accessUrl(),
            ],
        );
    }

    protected function accessUrl(): string
    {
        return URL::temporarySignedRoute(
            'email.scans.access',
            now()->addDays(7),
            ['scan' => $this->scan->id]
        );
    }
}
