<div class="p-2">
    <div class="text-center mb-6">
        <div class="text-5xl mb-2">🚀</div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Select a Plan</h3>
        <p class="text-sm text-gray-500">Choose a package to unlock event creation.</p>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @php
            $org = auth()->user()->organization;
            $hasCreatedEvents = $org->events()->exists();
            $hasUsedTrial = $org->packages()->where('is_free_trial', true)->exists();
        @endphp

        {{-- Trial Section --}}
        @if(!$hasCreatedEvents && !$hasUsedTrial)
            <div class="rounded-xl border-2 border-dashed border-success-500 bg-success-50 p-4 dark:bg-success-900/10">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-bold text-success-800 dark:text-success-400">1-Event Free Trial</h4>
                        <p class="text-xs text-success-700">Perfect for testing</p>
                    </div>
                    <button type="button" wire:click="startFreeTrial" class="bg-success-600 hover:bg-success-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm">
                        Get Started
                    </button>
                </div>
            </div>
        @endif

        {{-- Paid Plans Section --}}
        @foreach($definitions as $key => $item)
            <div class="flex items-center justify-between rounded-xl border border-gray-200 p-4 bg-white dark:bg-gray-800 dark:border-gray-700">
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $item['name'] }}</h4>
                    <p class="text-xs text-primary-600 font-bold uppercase tracking-tight">
                        LSL {{ number_format($item['price'], 2) }}
                    </p>
                </div>
                
                <button 
                    type="button"
                    wire:click="selectPlan('{{ $key }}')"
                    class="ml-4 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm"
                >
                    Select Plan
                </button>
            </div>
        @endforeach
    </div>
</div>