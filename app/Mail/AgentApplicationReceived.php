<?php

namespace App\Mail;

use App\Models\Agent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address; // Added this for replyTo
use Illuminate\Queue\SerializesModels;

class AgentApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Received: Agent Partnership',
            replyTo: [
                new Address('info@ventiq.co.ls', 'VENTIQ Team'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.agents.received',
            with: [
                'agent' => $this->agent,
            ],
        );
    }
}