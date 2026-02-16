{{-- @if(!auth()->user()->isSuperAdmin() && !auth()->user()->organization->events()->exists()) --}}
<script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tourCompleted = localStorage.getItem('ventiq_tour_completed');
    
    if (tourCompleted) {
        return;
    }

    // Wait longer for Filament to fully render
    setTimeout(() => {
        if (typeof window.driver !== 'undefined') {
            startVentiqTour();
        }
    }, 2000);
});

function startVentiqTour() {
    // FIX: Access the .driver() method inside the window.driver object
    const driver = window.driver.driver({
        showProgress: true,
        showButtons: ['next', 'previous', 'close'],
        popoverClass: 'ventiq-tour-popover',
        smoothScroll: false,
        allowClose: true,
        steps: [
            {
                popover: {
                    title: '👋 Welcome to VENTIQ!',
                    description: 'Your event management dashboard is ready. Here\'s what you can do:'
                }
            },
            {
                popover: {
                    title: '📅 Create Events',
                    description: 'Use the sidebar menu to create your first event. Add details, ticket tiers, and publish.'
                }
            },
            {
                popover: {
                    title: '💳 Setup Payments',
                    description: 'Add your M-Pesa or bank account in Payment Methods to receive ticket payments.'
                }
            },
            {
                popover: {
                    title: '📦 Monitor Usage',
                    description: 'Check your Package section to see ticket usage and upgrade when needed.'
                }
            },
            {
                popover: {
                    title: '✅ Ready!',
                    description: 'You\'re all set. Start by creating your first event from the sidebar.'
                }
            }
        ],
        
        onDestroyStarted: () => {
            localStorage.setItem('ventiq_tour_completed', 'true');
            driver.destroy();
        }
    });

    try {
        driver.drive();
    } catch (error) {
        console.error('Tour error:', error);
    }
}

// Global function to restart tour
window.restartVentiqTour = function() {
    console.log('Restarting tour...');
    localStorage.removeItem('ventiq_tour_completed');
    
    // Start immediately instead of reloading
    if (typeof window.driver !== 'undefined') {
        setTimeout(() => {
            startVentiqTour();
        }, 500);
    } else {
        location.reload();
    }
}
</script>
{{-- @endif --}}