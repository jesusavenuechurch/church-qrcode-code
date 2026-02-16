<x-filament-widgets::widget>
<div class="v-breakout-wrapper">
    <div class="v-glass-panel" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
        
        <div class="v-mesh"></div>
        <div class="v-scanner"></div>

        <div class="v-content" x-show="loaded" x-cloak>
            <header class="v-header">
                <div class="v-sys-info">
                    <span class="v-glitch">READY</span>
                    <span class="v-divider">|</span>
                    <span>ORG: {{ strtoupper(substr($org_name, 0, 3)) }} • {{ date('Y') }}</span>
                </div>
                <div class="v-package-badge">{{ $package?->display_name ?? 'STANDARD' }}</div>
            </header>

            <section class="v-hero">
                <h1 class="v-title">Welcome, {{ $org_name }}</h1>
                <p class="v-lead">You're all set to start selling tickets.  
Just complete the steps below and your first event will be live.</p>
            </section>

            <div class="v-grid">
                @foreach($steps as $step)
                <a href="{{ route($step['route'], $step['params']) }}" class="v-card">
                    <div class="v-icon-frame" style="--icon-color: {{ $step['color'] }}">
                        <x-filament::icon :icon="$step['icon']" class="h-6 w-6" />
                    </div>
                    <div class="v-card-body">
                        <h3 class="v-card-label">{{ $step['title'] }}</h3>
                        <p class="v-card-desc">{{ $step['desc'] }}</p>
                    </div>
                    <div class="v-status {{ $steps_completed[$step['id']] ? 'is-complete' : 'is-pending' }}">
                        {{ $steps_completed[$step['id']] ? '✓ DONE' : '○ PENDING' }}
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>


</div>
</x-filament-widgets::widget>