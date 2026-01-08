<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public string $downloadLink;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->downloadLink = route('ticket.download', $ticket->qr_code);
    }

    public function build()
    {
        $subject = "🎉 Your Ticket is Ready - {$this->ticket->event->name}";

        return $this->view('emails.tickets.approved')
            ->subject($subject)
            ->with([
                'ticket' => $this->ticket,
                'client' => $this->ticket->client,
                'event' => $this->ticket->event,
                'tier' => $this->ticket->tier,
                'organization' => $this->ticket->event->organization,
                'downloadLink' => $this->downloadLink,
            ]);
    }
}
