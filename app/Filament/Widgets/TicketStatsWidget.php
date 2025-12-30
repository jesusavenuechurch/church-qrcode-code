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
                ->description('Tickets awaiting payment approval')
                ->descriptionIcon('heroicon-o-clock')
                ->color($pendingCount > 0 ? 'warning' : 'success'),

            Stat::make('Active Tickets', $activeCount)
                ->description('Ready for check-in')
                ->descriptionIcon('heroicon-o-ticket')
                ->color('success'),

            Stat::make('Total Revenue', number_format($totalRevenue, 0) . ' UGX')
                ->description('From completed payments')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('info'),

            Stat::make('Today\'s Registrations', $todayRegistrations)
                ->description('New registrations today')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color($todayRegistrations > 0 ? 'success' : 'gray'),
        ];
    }
}