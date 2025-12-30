<div class="space-y-3">
    <p class="text-sm text-gray-600">Common tier configurations:</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <button 
            type="button"
            onclick="applyBasicTemplate()"
            class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-left"
        >
            <div class="font-medium text-sm">🎫 Basic</div>
            <div class="text-xs text-gray-500 mt-1">General Admission</div>
        </button>

        <button 
            type="button"
            onclick="applyStandardTemplate()"
            class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-left"
        >
            <div class="font-medium text-sm">⭐ Standard</div>
            <div class="text-xs text-gray-500 mt-1">Regular + VIP</div>
        </button>

        <button 
            type="button"
            onclick="applyPremiumTemplate()"
            class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition text-left"
        >
            <div class="font-medium text-sm">💎 Premium</div>
            <div class="text-xs text-gray-500 mt-1">Early Bird + Regular + VIP</div>
        </button>
    </div>
</div>

<script>
    function applyBasicTemplate() {
        // This is a simplified example - you'll need to adapt to Filament's Repeater API
        console.log('Apply Basic Template');
        alert('Click "Add Another Tier" and create: General Admission');
    }

    function applyStandardTemplate() {
        console.log('Apply Standard Template');
        alert('Create two tiers: Regular and VIP');
    }

    function applyPremiumTemplate() {
        console.log('Apply Premium Template');
        alert('Create three tiers: Early Bird, Regular, and VIP');
    }
</script>