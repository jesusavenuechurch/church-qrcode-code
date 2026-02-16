<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgentAccountInitialized extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;

    public function __construct(User $user, $url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'PROTOCOL ACTIVE: Your Ventiq Account is ready',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.agents.approved',
            with: [
                'user' => $this->user,
                'url' => $this->url,
            ],
        );
    }
}