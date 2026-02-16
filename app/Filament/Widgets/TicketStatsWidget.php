<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\Event;
use Filament\Widgets\Widget; // Changed from StatsOverviewWidget

class TicketStatsWidget extends Widget
{
    protected static ?int $sort = 0;
    
    // This makes the widget take up the full width, better for mobile/design
    protected int | string | array $columnSpan = 'full';

    // We define a custom view for the design
    protected static string $view = 'filament.widgets.ticket-stats-grid';

    public static function canView(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if ($user->isSuperAdmin()) return true;

        return Event::where('organization_id', $user->organization_id)->exists();
    }

    /**
     * Data sent to the custom Blade view
     */
    protected function getViewData(): array
    {
        $user = auth()->user();
        $query = Ticket::query();
        
        if (!$user?->isSuperAdmin()) {
            $query->whereHas('event', fn($q) => 
                $q->where('organization_id', $user->organization_id)
            );
        }

        // Logic Fix: Pending is now based on TICKET STATUS, not payment
        return [
            'pending_count' => (clone $query)->where('status', 'pending')->count(),
            'active_count' => (clone $query)->where('status', 'active')->count(),
            'total_revenue' => (clone $query)->where('payment_status', 'completed')->sum('amount'),
            'today_count' => (clone $query)->whereDate('created_at', today())->count(),
        ];
    }
}