<x-filament-widgets::widget>
    <div class="space-y-6">
        {{-- Welcome Header --}}
        <div class="p-6 bg-gradient-to-br from-slate-900 to-slate-800 rounded-[2rem] border border-white/10 shadow-xl">
            <h2 class="text-2xl font-black text-white tracking-tight">Hello, {{ auth()->user()->name }} 👋</h2>
            <p class="text-slate-400 text-sm font-medium uppercase tracking-widest mt-1">Event Control Center</p>
        </div>

        {{-- The Icon Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            
            {{-- APPROVALS --}}
            <a href="{{ \App\Filament\Resources\TicketResource::getUrl('index', ['tableFilters[payment_status][value]' => 'pending']) }}" 
               class="group p-6 bg-emerald-600 rounded-[2rem] shadow-lg shadow-emerald-900/20 active:scale-95 transition-all text-center">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <x-heroicon-s-banknotes class="w-7 h-7 text-white" />
                </div>
                <span class="text-white font-black uppercase tracking-tighter text-[11px]">Approvals</span>
                {{-- Optional: Add a "Count" badge here --}}
            </a>

            {{-- CREATE EVENT --}}
            <a href="{{ \App\Filament\Resources\EventResource::getUrl('create') }}" 
               class="group p-6 bg-slate-800 border border-white/10 rounded-[2rem] active:scale-95 transition-all text-center">
                <div class="w-12 h-12 bg-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-3 text-blue-400">
                    <x-heroicon-s-plus-circle class="w-7 h-7" />
                </div>
                <span class="text-white font-black uppercase tracking-tighter text-[11px]">New Event</span>
            </a>

            {{-- TIERS --}}
            <a href="{{ \App\Filament\Resources\EventTierResource::getUrl('index') }}" 
               class="group p-6 bg-slate-800 border border-white/10 rounded-[2rem] active:scale-95 transition-all text-center">
                <div class="w-12 h-12 bg-amber-500/20 rounded-2xl flex items-center justify-center mx-auto mb-3 text-amber-400">
                    <x-heroicon-s-ticket class="w-7 h-7" />
                </div>
                <span class="text-white font-black uppercase tracking-tighter text-[11px]">Manage Tiers</span>
            </a>

            {{-- ANALYTICS --}}
            <a href="#" class="group p-6 bg-slate-800 border border-white/10 rounded-[2rem] active:scale-95 transition-all text-center">
                <div class="w-12 h-12 bg-indigo-500/20 rounded-2xl flex items-center justify-center mx-auto mb-3 text-indigo-400">
                    <x-heroicon-s-chart-bar class="w-7 h-7" />
                </div>
                <span class="text-white font-black uppercase tracking-tighter text-[11px]">Reports</span>
            </a>
            
            {{-- QUICK SETTINGS --}}
            <a href="#" class="group p-6 bg-slate-800 border border-white/10 rounded-[2rem] active:scale-95 transition-all text-center">
                <div class="w-12 h-12 bg-slate-500/20 rounded-2xl flex items-center justify-center mx-auto mb-3 text-slate-400">
                    <x-heroicon-s-cog-6-tooth class="w-7 h-7" />
                </div>
                <span class="text-white font-black uppercase tracking-tighter text-[11px]">Settings</span>
            </a>

        </div>
    </div>
</x-filament-widgets::widget>