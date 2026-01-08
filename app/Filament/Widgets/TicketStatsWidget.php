<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $user = auth()->user();
        
        $query = Ticket::query();
        
        if (!$user?->isSuperAdmin()) {
            $query->whereHas('event', fn($q) => 
                $q->where('organization_id', $user?->organization_id)
            );
        }

        $pendingCount = (clone $query)->where('payment_status', 'pending')->count();
        $activeCount = (clone $query)->where('status', 'active')->count();
        $totalRevenue = (clone $query)
            ->where('payment_status', 'completed')
            ->sum('amount');
        
        $todayRegistrations = (clone $query)
            ->whereDate('created_at', today())
            ->count();

        return [
            Stat::make('Pending Approvals', $pendingCount)
                ->description('Awaiting payment verification')
                ->descriptionIcon('heroicon-o-clock')
                ->color($pendingCount > 0 ? 'warning' : 'success'),

            Stat::make('Active Tickets', $activeCount)
                ->description('Ready for check-in')
                ->descriptionIcon('heroicon-o-ticket')
                ->color('primary'),

            Stat::make('Total Revenue', number_format($totalRevenue, 2) . ' ' . config('constants.currency.code'))
                ->description('Confirmed in ' . config('constants.currency.name'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Today\'s Registrations', $todayRegistrations)
                ->description('New signups today')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('info'),
        ];
    }
}