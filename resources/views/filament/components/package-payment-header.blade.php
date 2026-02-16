<div class="mb-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-primary-500 shadow-sm">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ $plan['name'] }}</h3>
            <p class="text-xs text-gray-500">{{ $plan['description'] }}</p>
            <div class="mt-1 flex gap-2 text-[10px] font-bold text-primary-600 uppercase">
                <span>{{ $plan['events'] }} Event</span>
                <span>•</span>
                <span>{{ $plan['tickets'] }} Tickets</span>
            </div>
        </div>
        <div class="text-right">
            <span class="text-xl font-black text-primary-600">
                LSL {{ number_format($plan['price'], 2) }}
            </span>
        </div>
    </div>
    
    <button type="button" 
            wire:click="$set('selectedPlanKey', null)" 
            class="text-[10px] font-bold text-gray-400 hover:text-primary-600 underline mt-3 uppercase tracking-tighter">
        ← Change Plan / Go Back
    </button>
</div>

<hr class="border-gray-200 dark:border-gray-700 my-4">

<p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
    Please provide your payment reference to activate this package:
</p>