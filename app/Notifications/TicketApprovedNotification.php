<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Notifications\Notification;

class TicketApprovedNotification extends Notification
{
    public function __construct(
        public Ticket $ticket
    ) {}

    public function via($notifiable): array
    {
        return ['database']; // Only database for now
    }

    public function toArray($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'event_name' => $this->ticket->event->name,
            'download_url' => route('ticket.download', $this->ticket->qr_code),
            'message' => "Your ticket for {$this->ticket->event->name} has been approved!",
        ];
    }
}