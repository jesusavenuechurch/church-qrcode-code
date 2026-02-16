<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Event;
use App\Models\Ticket;

class OrgStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return !auth()->user()->isSuperAdmin();
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $org = $user->organization;

        $totalEvents = Event::where('organization_id', $org->id)->count();
        $totalTickets = Ticket::whereHas('event', fn($q) => $q->where('organization_id', $org->id))->count();
        $activePackage = $org->activePackages->first();

        return [
            Stat::make('Total Events', $totalEvents)
                ->description($activePackage ? "{$activePackage->remaining_events} events remaining" : 'No active package')
                ->icon('heroicon-o-calendar')
                ->color('primary'),

            Stat::make('Tickets Issued', $totalTickets)
                ->description($activePackage ? "{$activePackage->remaining_tickets} tickets remaining" : '')
                ->icon('heroicon-o-ticket')
                ->color('success'),

            Stat::make('Active Package', $activePackage?->display_name ?? 'None')
                ->description($activePackage?->status ?? 'No package')
                ->icon('heroicon-o-cube')
                ->color($activePackage ? 'success' : 'danger'),
        ];
    }
}