<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\TicketPayment;
use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s'; // Live updates every 15s

    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Base queries filtered by organization if not super_admin
        $ticketQuery = Ticket::query();
        $paymentQuery = TicketPayment::query();
        $eventQuery = Event::query();

        if (!$user->isSuperAdmin()) {
            $orgId = $user->organization_id;
            $ticketQuery->whereHas('event', fn($q) => $q->where('organization_id', $orgId));
            $paymentQuery->whereHas('ticket.event', fn($q) => $q->where('organization_id', $orgId));
            $eventQuery->where('organization_id', $orgId);
        }

        return [
            // 1. PENDING APPROVALS (The #1 task for Org Admins)
            Stat::make('Pending Approvals', $paymentQuery->clone()->where('status', 'pending')->count())
                ->description('Payments awaiting verification')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning') // Ventiq Orange
                ->chart([7, 3, 5, 2, 10, 12, 15]),

            // 2. LIVE CHECK-INS (The "Pulse" of the event)
            Stat::make('Checked In', $ticketQuery->clone()->whereNotNull('checked_in_at')->count())
                ->description('Total guests arrived')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary') // Ventiq Navy
                ->chart([1, 5, 10, 20, 50, 80, 100]),

            // 3. TOTAL REVENUE (Financial Health)
            Stat::make('Approved Revenue', number_format($paymentQuery->clone()->where('status', 'approved')->sum('amount')) . ' UGX')
                ->description('Confirmed payments')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
                
            // 4. ACTIVE EVENTS
            Stat::make('Active Events', $eventQuery->where('status', 'active')->count())
                ->description('Live registration pages')
                ->descriptionIcon('heroicon-m-calendar-days'),
        ];
    }
}