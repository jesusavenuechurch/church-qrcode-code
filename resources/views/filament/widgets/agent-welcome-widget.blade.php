
<x-filament-widgets::widget>


<div class="ag-wrap" x-data="{ 
    copied: false,
    copyLink() {
        navigator.clipboard.writeText('{{ $referral_url }}');
        this.copied = true;
        setTimeout(() => this.copied = false, 2000);
    }
}">

    {{-- ── IDENTITY BAR ── --}}
    <div class="ag-identity">
        <div class="ag-identity-left">
            <h1>Welcome, {{ auth()->user()->name }}</h1>
            <p>You help organizations go digital with events. Share your link, follow up, and earn commissions when they upgrade.</p>
            <div class="ag-badges">
                <span class="ag-badge {{ $agent->status === 'approved' ? 'active' : 'pending' }}">
                    {{ $agent->status === 'approved' ? '● Active Agent' : '◌ Pending Approval' }}
                </span>
                @if($agent->city_district)
                    <span class="ag-badge">📍 {{ $agent->city_district }}</span>
                @endif
                <span class="ag-badge">Since {{ $agent->created_at->format('M Y') }}</span>
                <span class="ag-badge">ID: {{ $agent->referral_token }}</span>
            </div>
        </div>
    </div>

    {{-- ── MAIN CONTENT ── --}}
    <div class="ag-main">

        {{-- CERTIFICATION STATUS BANNER --}}
        @if(!($cert?->passed))
            <div class="rounded-[1.5rem] border-2 border-dashed border-[#1D4069]/20 bg-[#1D4069]/3 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#1D4069]/10 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">
                        🎓
                    </div>
                    <div>
                        <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">
                            Partner Certification Required
                        </p>
                        @if($cert && $cert->attempts > 0)
                            <p class="text-xs text-red-500 font-bold mt-0.5">
                                Last score: {{ $cert->score }}/{{ $cert->total_questions }} ({{ $cert->score_percentage }}%) — 75% needed
                            </p>
                        @else
                            <p class="text-xs text-gray-500 font-medium mt-0.5">
                                Complete the 4-module course — takes about 5 minutes
                            </p>
                        @endif
                    </div>
                </div>
                <a href="{{ \App\Filament\Pages\AgentCertificationPage::getUrl() }}"
                class="flex-shrink-0 px-6 py-3 bg-[#1D4069] hover:bg-[#F07F22] text-white font-black text-[10px] uppercase tracking-widest rounded-xl transition-all">
                    {{ $cert && $cert->attempts > 0 ? 'Retake Course' : 'Start Certification' }}
                </a>
            </div>
        @else
            <div class="rounded-[1.5rem] bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-800 rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">
                    🎓
                </div>
                <div>
                    <p class="text-sm font-black text-emerald-900 dark:text-emerald-100 uppercase tracking-tight">
                        Certified Partner
                    </p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-300 font-bold mt-0.5">
                        Score: {{ $cert->score }}/{{ $cert->total_questions }} ({{ $cert->score_percentage }}%) · {{ $cert->completed_at->format('M j, Y') }}
                    </p>
                </div>
            </div>
        @endif
        {{-- MISSION PANEL --}}
        <div class="ag-mission">
            <span class="ag-mission-icon">{{ $mission['icon'] }}</span>
            <div>
                <span class="ag-mission-label">Current Mission</span>
                <span class="ag-mission-text">{{ $mission['text'] }}</span>
            </div>
        </div>

        {{-- ── LEFT: ORG LIST ── --}}
        <div class="ag-orgs-section">
            <h2>Organizations You've Onboarded ({{ count($orgs) }})</h2>
            <div class="ag-org-list">
                @forelse($orgs as $org)
                    @php
                        $package = $org->activePackages->first();
                        $hasEvents = $org->events()->exists();

                        if ($hasEvents) {
                            $statusKey = 'active_event';
                            $statusLabel = 'Active Event';
                        } elseif ($package && !$package->is_free_trial) {
                            $statusKey = 'paid';
                            $statusLabel = 'Paid';
                        } elseif ($package && $package->is_free_trial) {
                            $statusKey = 'trial';
                            $statusLabel = 'Free Trial';
                        } else {
                            $statusKey = 'registered';
                            $statusLabel = 'Registered';
                        }

                        $lastActivity = $hasEvents
                            ? 'Running events'
                            : ($package ? 'Trial active — no events yet' : 'Registered — not activated');

                        $waMessage = urlencode("Hi 👋 This is {$agent->name} from VENTIQ. Just checking in — can I help you set up your first event?");
                        $waNumber = preg_replace('/\D/', '', $org->phone);
                        $waUrl = "https://wa.me/{$waNumber}?text={$waMessage}";
                    @endphp
                    <div class="ag-org-card">
                        <div>
                            <div class="ag-org-name">{{ $org->name }}</div>
                            <div class="ag-org-meta">
                                <span class="ag-org-status status-{{ $statusKey }}">{{ $statusLabel }}</span>
                                <span>{{ $org->created_at->format('M d, Y') }}</span>
                                <span>{{ $lastActivity }}</span>
                            </div>
                        </div>
                        <div class="ag-org-actions">
                            <a href="{{ $waUrl }}" 
                               target="_blank" 
                               class="ag-btn ag-btn-whatsapp">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                WhatsApp
                            </a>
                            <a href="tel:{{ $org->phone }}" 
                               class="ag-btn ag-btn-call">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.7A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                                Call
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="ag-empty">
                        <strong>No organizations yet</strong>
                        Share your agent link below to onboard your first partner. Once they register, they'll appear here.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ── RIGHT: SIDEBAR ── --}}
        <div class="ag-sidebar">

            {{-- Agent Link Card --}}
            <div class="ag-link-card">
                <div class="ag-link-card-label">📎 Your Agent Link</div>
                <div class="ag-link-url">{{ $referral_url }}</div>
                <div class="ag-link-actions">
                    <button class="ag-btn ag-btn-primary" @click="copyLink()">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                        <span x-text="copied ? 'Copied!' : 'Copy Link'"></span>
                    </button>
                    <a href="https://wa.me/?text={{ urlencode('Join VENTIQ for smart event ticketing in Lesotho. Register your organization here: ' . $referral_url) }}" 
                       target="_blank"
                       class="ag-btn ag-btn-wa-share">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        Share
                    </a>
                </div>
            </div>

            {{-- Earnings Card --}}
            <div class="ag-earnings-card">
                <div class="ag-earnings-label">💰 Earnings & Progress</div>
                <div class="ag-stat-row">
                    <span class="ag-stat-name">Orgs Onboarded</span>
                    <span class="ag-stat-value">{{ $stats['total_orgs'] }}</span>
                </div>
                <div class="ag-stat-row">
                    <span class="ag-stat-name">Paid Orgs</span>
                    <span class="ag-stat-value success">{{ $stats['paid_orgs'] }}</span>
                </div>
                <div class="ag-stat-row">
                    <span class="ag-stat-name">Est. Commission</span>
                    <span class="ag-stat-value highlight">M{{ number_format($stats['estimated_commission'], 2) }}</span>
                </div>
                <div class="ag-stat-row">
                    <span class="ag-stat-name">Total Earned</span>
                    <span class="ag-stat-value success">M{{ number_format($stats['total_earned'], 2) }}</span>
                </div>

                {{-- Milestone Progress --}}
                @php
                    $nextMilestone = 5;
                    $tiers = [5, 10, 15, 20, 25];
                    foreach ($tiers as $tier) {
                        if ($stats['paid_orgs'] < $tier) {
                            $nextMilestone = $tier;
                            break;
                        }
                    }
                    $milestoneProgress = $nextMilestone > 0 
                        ? min(($stats['paid_orgs'] / $nextMilestone) * 100, 100) 
                        : 100;
                    $remaining = max($nextMilestone - $stats['paid_orgs'], 0);
                @endphp

                <div class="ag-milestone">
                    <p class="ag-milestone-text">
                        @if($remaining > 0)
                            🎯 <strong>{{ $remaining }} more paid org{{ $remaining > 1 ? 's' : '' }}</strong> to unlock your next bonus (M{{ $nextMilestone >= 10 ? 320 : 300 }} milestone)
                        @else
                            🎉 Milestone reached! Bonus pending.
                        @endif
                    </p>
                    <div class="ag-milestone-bar">
                        <div class="ag-milestone-fill" style="width: {{ $milestoneProgress }}%"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</x-filament-widgets::widget>