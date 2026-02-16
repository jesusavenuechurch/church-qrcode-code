<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    {{-- Pending Approvals Card --}}
    <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                <x-heroicon-o-clock class="w-6 h-6 text-orange-600 dark:text-orange-400" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Admission</p>
                <h4 class="text-2xl font-bold dark:text-white">{{ $pending_count }}</h4>
            </div>
        </div>
    </div>

    {{-- Active Tickets --}}
    <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                <x-heroicon-o-ticket class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Tickets</p>
                <h4 class="text-2xl font-bold dark:text-white">{{ $active_count }}</h4>
            </div>
        </div>
    </div>

    {{-- Revenue --}}
    <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-primary-100 dark:bg-primary-900/30 rounded-lg">
                <x-heroicon-o-banknotes class="w-6 h-6 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
                <h4 class="text-2xl font-bold dark:text-white">{{ number_format($total_revenue, 2) }} LSL</h4>
            </div>
        </div>
    </div>

    {{-- Today --}}
    <div class="p-5 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                <x-heroicon-o-arrow-trending-up class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">New Today</p>
                <h4 class="text-2xl font-bold dark:text-white">{{ $today_count }}</h4>
            </div>
        </div>
    </div>
</div>