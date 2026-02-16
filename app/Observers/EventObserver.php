<?php

namespace App\Observers;

use App\Models\Event; // Make sure this is imported
use App\Models\OrganizationPackage;

class EventObserver
{
    /**
     * Handle the Event "creating" event.
     */
    public function creating(Event $event): void // The fix is right here
    {
        // Find an active package for this organization
        $package = OrganizationPackage::where('organization_id', $event->organization_id)
            ->where('status', 'active')
            ->whereRaw('events_used < events_included')
            ->first();

        if ($package) {
            $event->organization_package_id = $package->id;
        }
    }

    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        // Once the event is actually saved, increment the counter
        if ($event->organization_package_id) {
            $event->package->increment('events_used');
        }
    }
}