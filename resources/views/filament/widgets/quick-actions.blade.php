<div class="grid gap-4 md:grid-cols-4">
    <a href="{{ route('filament.admin.resources.events.create') }}" 
       class="group flex items-center gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 transition hover:shadow-md dark:bg-gray-900 dark:ring-white/10">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#1D4069] text-white">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400">New Event</p>
            <p class="text-sm font-black text-gray-900 dark:text-white">Create Event</p>
        </div>
    </a>

    <a href="{{ route('filament.admin.resources.events.index') }}" 
       class="group flex items-center gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 transition hover:shadow-md dark:bg-gray-900 dark:ring-white/10">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-500 text-white">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400">View All</p>
            <p class="text-sm font-black text-gray-900 dark:text-white">My Events</p>
        </div>
    </a>

    <a href="{{ route('filament.admin.resources.payment-methods.index') }}" 
       class="group flex items-center gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 transition hover:shadow-md dark:bg-gray-900 dark:ring-white/10">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500 text-white">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400">Payments</p>
            <p class="text-sm font-black text-gray-900 dark:text-white">Payment Methods</p>
        </div>
    </a>

    <a href="{{ route('filament.admin.resources.package-purchases.index') }}" 
       class="group flex items-center gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 transition hover:shadow-md dark:bg-gray-900 dark:ring-white/10">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#F07F22] text-white">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400">Capacity</p>
            <p class="text-sm font-black text-gray-900 dark:text-white">View Package</p>
        </div>
    </a>
</div>