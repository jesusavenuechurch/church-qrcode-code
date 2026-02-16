<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Agent;
use App\Models\Organization;
use App\Models\AgentEarning;

class AgentWelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.agent-welcome-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = -20;

    public static function canView(): bool
    {
        // ✅ Using your existing isSalesAgent() method
        return Auth::user()?->isSalesAgent() ?? false;
    }

    protected function getViewData(): array
    {
        $user  = Auth::user();

        // ✅ Your approach — find agent by email
        $agent = Agent::where('email', $user->email)->first();

        // ✅ Your approach — map orgs with whatsapp_url
        $orgs = Organization::where('agent_id', $agent?->id)
            ->with(['activePackages', 'events'])
            ->latest()
            ->get()
            ->map(function ($org) use ($user, $agent) {
                // Pre-filled WhatsApp message
                $agentName = $agent?->name ?? $user->name;
                $message = urlencode("Hi 👋 This is {$agentName} from VENTIQ. Just checking in — can I help you set up your first event?");
                $phone = preg_replace('/\D/', '', $org->phone);
                $org->whatsapp_url  = "https://wa.me/{$phone}?text={$message}";

                // ✅ Add status label for the blade
                $pkg = $org->activePackages->first();
                $hasEvents = $org->events->isNotEmpty();

                $org->status_key = match(true) {
                    $hasEvents                            => 'active_event',
                    $pkg && !$pkg->is_free_trial          => 'paid',
                    $pkg && $pkg->is_free_trial           => 'trial',
                    default                               => 'registered',
                };

                $org->status_label = match($org->status_key) {
                    'active_event' => 'Active Event',
                    'paid'         => 'Paid',
                    'trial'        => 'Free Trial',
                    default        => 'Registered',
                };

                $org->last_activity = match($org->status_key) {
                    'active_event' => 'Running events',
                    'paid'         => 'Paid package active',
                    'trial'        => 'Trial active — no events yet',
                    default        => 'Registered — not activated',
                };

                return $org;
            });

        return [
            'agent'        => $agent,
            'orgs'         => $orgs,
            'mission'      => $this->getMission($orgs),
            'stats'        => $this->getStats($agent, $orgs),
            'cert'         => $agent ? \App\Models\AgentCertification::where('agent_id', $agent->id)->first() : null,
            'referral_url' => $agent?->registration_url ?? route('agent.registration.form', $agent?->referral_token),
        ];
    }

    protected function getMission($orgs): array
    {
        if ($orgs->isEmpty()) {
            return [
                'icon'  => '🚀',
                'text'  => 'Share your agent link to onboard your first organization. Once they register, you\'ll see them here.',
                'color' => 'warning',
            ];
        }

        // Orgs registered but no package at all
        $notActivated = $orgs->filter(fn($org) => $org->status_key === 'registered');
        if ($notActivated->isNotEmpty()) {
            $name = $notActivated->first()->name;
            return [
                'icon'  => '📞',
                'text'  => "{$name} registered but hasn't activated yet. A quick WhatsApp can make the difference.",
                'color' => 'primary',
            ];
        }

        // On trial but no events yet
        $trialNoEvents = $orgs->filter(fn($org) => $org->status_key === 'trial');
        if ($trialNoEvents->isNotEmpty()) {
            $name = $trialNoEvents->first()->name;
            return [
                'icon'  => '🎯',
                'text'  => "{$name} is on a free trial but hasn't created an event yet. Help them take that first step.",
                'color' => 'primary',
            ];
        }

        // Has events but still on trial — ready to upgrade
        $activeOnTrial = $orgs->filter(fn($org) => $org->status_key === 'active_event');
        if ($activeOnTrial->isNotEmpty()) {
            $name = $activeOnTrial->first()->name;
            return [
                'icon'  => '💡',
                'text'  => "{$name} is running events. This is the best time to introduce them to a paid package.",
                'color' => 'success',
            ];
        }

        return [
            'icon'  => '🌟',
            'text'  => 'Great work! All your organizations are active. Keep engaging and look for new partners to onboard.',
            'color' => 'success',
        ];
    }

    protected function getStats($agent, $orgs): array
    {
        if (!$agent) {
            return [
                'total_orgs'           => 0,
                'paid_orgs'            => 0,
                'total_earned'         => 0,
                'estimated_commission' => 0,
            ];
        }

        $paidOrgs = $orgs->filter(fn($org) => in_array($org->status_key, ['paid', 'active_event']))->count();

        $totalEarned = AgentEarning::where('agent_id', $agent->id)
            ->where('status', 'paid')
            ->sum('amount');

        $pendingEarnings = AgentEarning::where('agent_id', $agent->id)
            ->where('status', 'approved')
            ->sum('amount');

        return [
            'total_orgs'           => $orgs->count(),
            'paid_orgs'            => $paidOrgs,
            'total_earned'         => $totalEarned,
            'estimated_commission' => $pendingEarnings,
        ];
    }
}