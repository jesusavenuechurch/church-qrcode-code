<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = -10;

    public static function canView(): bool
    {
        $user = Auth::user();
        if (!$user || $user->isSuperAdmin() || $user->isSalesAgent()) {
            return false;
        }

        if (!$user->organization_id) {
            return false;
        }

        return Event::where('organization_id', $user->organization_id)->count() === 0;
    }

    protected function getViewData(): array
    {
        $user = Auth::user();
        $org = $user->organization;
        
        if (!$org) return [];

        return [
            'org_name' => $org->name,
            'package' => $org->activePackages()->first(),
            'steps_completed' => [
                'profile' => !empty($org->logo_path) || !empty($org->description),
                'payment_method' => $org->paymentMethods()->exists(),
                'event' => false,
            ],
            // ADD THIS ARRAY HERE
            'steps' => [
                [
                    'id' => 'profile',
                    'title' => 'Set Up Your Team',
                    'desc' => 'Invite your staff and give access to people who will manage or scan tickets.',
                    'icon' => 'heroicon-o-users',
                    'route' => 'filament.admin.resources.users.create',
                    'params' => [],
                    'color' => '#1D4069',
                ],
                [
                    'id' => 'payment_method',
                    'title' => 'Add Payment Details',
                    'desc' => 'Add your Mobile Money or bank account details so attendees know where to pay.',
                    'icon' => 'heroicon-o-credit-card',
                    'route' => 'filament.admin.resources.organization-payment-methods.index',
                    'params' => [],
                    'color' => '#F07F22',
                ],
                [
                    'id' => 'event',
                    'title' => 'Create Event',
                    'desc' => 'Add your event details, create your own ticket tiers, set the date and time, choose public or private access, upload a poster, publish, and share the link.',
                    'icon' => 'heroicon-o-rocket-launch',
                    'route' => 'filament.admin.resources.events.create',
                    'params' => [],
                    'color' => '#F07F22',
                ],
            ],
        ];
    }
}