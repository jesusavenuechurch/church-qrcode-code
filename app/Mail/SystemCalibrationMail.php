<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class SystemCalibrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Required: Initializing VENTIQ System Access',
            from: new Address(config('ventiq.emails.noreply'), 'VENTIQ'),
            replyTo: [
                new Address(config('ventiq.emails.support'), 'VENTIQ Support'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.system-test',
        );
    }
}