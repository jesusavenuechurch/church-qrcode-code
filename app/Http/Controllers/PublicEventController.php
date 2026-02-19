<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    /**
     * Show public event page
     * URL: /org/{org_slug}/event/{event_slug}
     */
    public function show($orgSlug, $eventSlug)
    {
        // Find organization by slug
        $organization = Organization::where('slug', $orgSlug)
            ->firstOrFail();

        // Find event by slug and organization
        $event = Event::where('slug', $eventSlug)
            ->where('organization_id', $organization->id)
            ->where('is_public', true) // Only show public events
            ->with(['tiers' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('price', 'asc');
            }])
            ->firstOrFail();

        // Check if event is still accepting registrations
        $canRegister = $event->registration_deadline === null || 
                       now()->lt($event->registration_deadline);

        // Get available tickets per tier
        $tierAvailability = [];
        foreach ($event->tiers as $tier) {
            $sold = $tier->tickets()->whereIn('status', ['active', 'checked_in'])->count();
            $available = $tier->capacity ? ($tier->capacity - $sold) : null;
            $tierAvailability[$tier->id] = [
                'sold' => $sold,
                'available' => $available,
                'is_sold_out' => $tier->capacity && $available <= 0,
            ];
        }

        return view('public.event', compact(
            'organization', 
            'event', 
            'canRegister',
            'tierAvailability'
        ));
    }

    /**
     * Show organization's public event listing
     * URL: /org/{org_slug}/events
     */
    public function listEvents($orgSlug)
    {
        $organization = Organization::where('slug', $orgSlug)
            ->firstOrFail();

        $events = Event::where('organization_id', $organization->id)
            ->where('is_public', true)
            ->where(function($query) {
                $query->where('event_date', '>=', now())
                      ->orWhereNull('event_date');
            })
            ->with('tiers')
            ->orderBy('event_date', 'asc')
            ->get();

        return view('public.events', compact('organization', 'events'));
    }

    public function browseAll()
    {
        $events = Event::where('is_public', true)
            ->where(function($query) {
                $query->where('event_date', '>=', now())
                    ->orWhereNull('event_date');
            })
            ->with(['organization', 'tiers' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('event_date', 'asc')
            ->paginate();

        return view('events.browse', compact('events'));
    }
}