<x-filament-panels::page>
    {{-- Main Wrapper with horizontal padding to prevent edge-hugging --}}
    <div class="px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- TOP SECTION: Selector & Stats --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- Event Selector: Only taking 4 columns and not stretching --}}
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 h-full">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Event Context</h3>
                    <div class="max-w-xs"> {{-- Forces the dropdown to stay small --}}
                        {{ $this->form }}
                    </div>
                </div>
            </div>

            {{-- Summary Stats: Taking the remaining 8 columns --}}
            <div class="lg:col-span-8">
                @if($selectedEventId)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-full">
                        {{-- Tickets Card --}}
                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 flex flex-col justify-center">
                            <p class="text-xs font-bold uppercase text-gray-400">Tickets Sold</p>
                            <p class="text-3xl font-black mt-1">{{ number_format($stats['total_tickets']) }}</p>
                            <div class="mt-3 w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                                <div class="bg-primary-600 h-1.5 rounded-full" style="width: {{ $stats['capacity_percentage'] }}%"></div>
                            </div>
                        </div>

                        {{-- Revenue Card --}}
                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 flex flex-col justify-center">
                            <p class="text-xs font-bold uppercase text-gray-400">Revenue ({{ config('constants.currency.code') }})</p>
                            <p class="text-2xl font-black mt-1">{{ config('constants.currency.symbol') }} {{ number_format($stats['total_revenue']) }}</p>
                            <p class="text-[10px] text-gray-500 mt-1 uppercase">Goal: {{ number_format($stats['revenue_goal']) }}</p>
                        </div>

                        {{-- Attendance Card --}}
                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 flex flex-col justify-center">
                            <p class="text-xs font-bold uppercase text-gray-400">Attendance</p>
                            <p class="text-3xl font-black mt-1">{{ number_format($stats['checked_in']) }}</p>
                            <p class="text-[10px] text-gray-500 mt-1 uppercase">{{ $stats['check_in_percentage'] }}% Arrived</p>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 h-full flex items-center justify-center">
                        <p class="text-sm text-gray-400 italic font-medium">Select an event to load data</p>
                    </div>
                @endif
            </div>
        </div>

        @if($selectedEventId)
            {{-- BOTTOM SECTION: 2-Column Main Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                {{-- LEFT: Performance & Activity (8 Columns) --}}
                <div class="lg:col-span-8 space-y-6">
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-6">Tier Breakdown</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                            @foreach($tierBreakdown as $tier)
                                <div class="space-y-2">
                                    <div class="flex justify-between items-end">
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $tier['name'] }}</span>
                                        <span class="text-xs font-bold text-gray-500">{{ $tier['sold'] }} / {{ $tier['capacity'] }}</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2.5">
                                        <div class="h-2.5 rounded-full" style="width: {{ $tier['percentage'] }}%; background-color: {{ $tier['color'] }}"></div>
                                    </div>
                                    <p class="text-[10px] font-bold text-green-600 uppercase tracking-tighter">{{ config('constants.currency.symbol') }} {{ number_format($tier['revenue']) }} Generated</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Activity Feed --}}
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">Recent Registrations</h3>
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($recentActivity as $activity)
                                <div class="py-4 flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-2 h-2 rounded-full bg-{{ $activity['color'] }}-500"></div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $activity['client_name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $activity['action'] }} • {{ $activity['time'] }}</p>
                                        </div>
                                    </div>
                                    <x-filament::link :href="route('filament.admin.resources.tickets.edit', $activity['id'])" size="sm">View</x-filament::link>
                                </div>
                            @empty
                                <p class="text-center py-4 text-sm text-gray-400">No activity yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Sidebar Actions (4 Columns) --}}
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-6">Delivery Performance</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-medium">WhatsApp Deliveries</span>
                                <span class="font-bold text-green-600">{{ number_format($deliveryStats['whatsapp']) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-medium">Email Deliveries</span>
                                <span class="font-bold text-blue-600">{{ number_format($deliveryStats['email']) }}</span>
                            </div>
                            
                            @if($deliveryStats['failed'] > 0)
                                <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-lg">
                                    <p class="text-xs font-bold text-red-700 dark:text-red-400 uppercase">{{ $deliveryStats['failed'] }} Failed Attempts</p>
                                    <x-filament::button :href="route('filament.admin.resources.tickets.index', ['tableFilters[delivery_status][value]' => 'failed'])" tag="a" color="danger" size="sm" class="mt-3" block>
                                        Fix Failures
                                    </x-filament::button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-4">Quick Actions</h3>
                        <div class="flex flex-col gap-3">
                            <x-filament::button wire:click="exportDatabase" color="gray" icon="heroicon-m-arrow-down-tray" block>
                                Export Data
                            </x-filament::button>
                            <x-filament::button :href="route('filament.admin.resources.tickets.index', ['tableFilters[event_id][value]' => $selectedEventId])" tag="a" color="gray" icon="heroicon-m-ticket" block>
                                Ticket List
                            </x-filament::button>
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>
</x-filament-panels::page>