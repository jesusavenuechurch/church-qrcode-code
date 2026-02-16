<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgentPayoutReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $agent;
    public $totalAmount;
    public $paymentMethod;
    public $paymentReference;
    public $earnings;

    /**
     * Create a new message instance.
     */
    public function __construct($agent, $totalAmount, $paymentMethod, $paymentReference, $earnings)
    {
        $this->agent = $agent;
        $this->totalAmount = $totalAmount;
        $this->paymentMethod = $paymentMethod;
        $this->paymentReference = $paymentReference;
        $this->earnings = $earnings;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'VENTIQ Payout Receipt: M ' . number_format($this->totalAmount, 2),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.agents.payout-receipt',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}