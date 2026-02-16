<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Models\Event;

class TicketObserver
{
    public function creating(Ticket $ticket): void
    {
        // Link ticket to the event's package for quota tracking
        $event = $ticket->event ?? Event::find($ticket->event_id);

        if ($event && $event->organization_package_id) {
            $ticket->organization_package_id = $event->organization_package_id;
        }
    }

public function created(Ticket $ticket): void
{
    $package = $ticket->package;

    if ($package) {
        /**
         * Logic:
         * 1. If it's an Admin-issued "Comp" ticket, $ticket->is_complimentary will be true.
         * (Bucket: 12)
         * 2. If it's a "Free" or "Paid" Standard ticket from the web, is_complimentary is false.
         * (Bucket: 50)
         */
        if ($ticket->is_complimentary === true) {
            $package->increment('comp_tickets_used'); 
        } else {
            $package->increment('tickets_used');
        }
    }
}

    public function deleted(Ticket $ticket): void
    {
        $package = $ticket->package;

        if ($package) {
            if ($ticket->is_complimentary) {
                $package->decrement('comp_tickets_used');
            } else {
                $package->decrement('tickets_used');
            }
        }
    }
}