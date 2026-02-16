<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketPendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public $paymentMethods;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->paymentMethods = \App\Models\OrganizationPaymentMethod::where('organization_id', $ticket->event->organization_id)
        ->where('is_active', true)
        ->ordered()
        ->get();
    }

    public function build()
    {
        $subject = "Registration Received - {$this->ticket->event->name}";

        return $this->view('emails.tickets.pending')
            ->subject($subject)
            ->with([
                'ticket' => $this->ticket,
                'client' => $this->ticket->client,
                'event' => $this->ticket->event,
                'tier' => $this->ticket->tier,
                'organization' => $this->ticket->event->organization,
                'paymentMethods' => $this->paymentMethods,
            ]);
    }
}
