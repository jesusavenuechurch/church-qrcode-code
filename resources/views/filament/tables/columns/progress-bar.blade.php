@php
    $state = $getState();
    $color = $state['percentage'] >= 100 ? 'bg-danger-500' : ($state['percentage'] >= 80 ? 'bg-warning-500' : 'bg-success-500');
@endphp

<div class="px-4 py-2">
    <div class="flex justify-between mb-1 text-xs">
        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $state['label'] }}</span>
        <span class="text-gray-500">{{ number_format($state['percentage'], 0) }}%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700">
        <div class="{{ $color }} h-1.5 rounded-full transition-all duration-500" 
             style="width: {{ min($state['percentage'], 100) }}%">
        </div>
    </div>
</div>