<div class="p-4">
    <div class="space-y-4">
        <!-- Client Info -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $ticket->client->full_name }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $ticket->event->name }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Ticket Details -->
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Ticket #</span>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $ticket->ticket_number }}</p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Tier</span>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $ticket->tier->tier_name }}</p>
            </div>
        </div>

        <!-- Download Link -->
        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Download Link
            </label>
            <div class="flex gap-2">
                <input 
                    type="text" 
                    value="{{ $link }}" 
                    readonly
                    id="ticket-link-input"
                    class="flex-1 px-3 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                />
                <button 
                    type="button"
                    onclick="copyTicketLink(event)"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Copy
                </button>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-2 pt-2">
            <a 
                href="{{ $link }}" 
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Open in New Tab
            </a>

            @if($ticket->client->phone)
            <a 
                href="https://wa.me/{{ preg_replace('/\D/', '', $ticket->client->phone) }}?text={{ urlencode("Hi {$ticket->client->full_name}, here's your ticket for {$ticket->event->name}: {$link}") }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 dark:bg-green-900 hover:bg-green-200 dark:hover:bg-green-800 text-green-700 dark:text-green-200 rounded-lg text-sm font-medium transition-colors"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Send via WhatsApp
            </a>
            @endif
        </div>

        <!-- Info Note -->
        <div class="flex items-start gap-2 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm text-blue-800 dark:text-blue-200">
                This link allows the client to download their ticket as a PDF with QR code. Share it via WhatsApp, email, or SMS.
            </p>
        </div>
    </div>
</div>

<script>
    function copyTicketLink(event) {
        const input = document.getElementById('ticket-link-input');
        const button = event.currentTarget;
        
        // Copy to clipboard
        navigator.clipboard.writeText(input.value).then(() => {
            // Show success feedback
            const originalHTML = button.innerHTML;
            
            button.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Copied!
            `;
            
            button.classList.remove('bg-primary-600', 'hover:bg-primary-700');
            button.classList.add('bg-green-600', 'hover:bg-green-700');
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('bg-green-600', 'hover:bg-green-700');
                button.classList.add('bg-primary-600', 'hover:bg-primary-700');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy:', err);
            
            // Fallback: select the text
            input.select();
            input.setSelectionRange(0, 99999);
            
            try {
                document.execCommand('copy');
                alert('Link copied to clipboard!');
            } catch (e) {
                alert('Please manually copy the link.');
            }
        });
    }
</script>