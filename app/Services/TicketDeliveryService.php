<?php

namespace App\Services;

use App\Models\Ticket;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class TicketDeliveryService
{
    public function deliver(Ticket $ticket): void
    {
        Log::info("🚀 Delivering ticket {$ticket->ticket_number}");

        // WhatsApp
        if ($ticket->shouldDeliverViaWhatsApp()) {
            $sent = app(WhatsAppService::class)
                ->sendTicket($ticket, $ticket->client->phone);

            if (!$sent) {
                $ticket->logDeliveryFailure('whatsapp', 'Failed to send via Twilio');
            }
        }

        // Email (future)
        if ($ticket->shouldDeliverViaEmail()) {
            // Mail::to(...)->send(...)
            Log::info("📧 Email delivery placeholder for {$ticket->ticket_number}");
        }
    }
}