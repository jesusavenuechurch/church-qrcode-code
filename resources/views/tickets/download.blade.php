<!-- resources/views/tickets/download.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Ticket - {{ $ticket->event->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glass { backdrop-filter: blur(10px); background: rgba(255,255,255,0.1); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">🎫 Your Ticket</h1>
            <p class="text-purple-100">{{ $ticket->event->name }}</p>
        </div>

        <!-- Main Card -->
        <div class="glass rounded-2xl p-8 border border-white/20 backdrop-blur-md">
            <!-- Ticket Info -->
            <div class="bg-white/10 rounded-lg p-6 mb-6 border border-white/20">
                <div class="text-center mb-4">
                    <div class="text-sm text-purple-200 mb-2">Ticket for</div>
                    <h2 class="text-2xl font-bold text-white">{{ $ticket->client->full_name }}</h2>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-purple-100">
                        <span>Event</span>
                        <span class="font-semibold text-white">{{ $ticket->event->name }}</span>
                    </div>
                    <div class="flex justify-between text-purple-100">
                        <span>Tier</span>
                        <span class="font-semibold text-white">{{ $ticket->tier->tier_name }}</span>
                    </div>
                    <div class="flex justify-between text-purple-100">
                        <span>Date</span>
                        <span class="font-semibold text-white">{{ $ticket->event->event_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between text-purple-100">
                        <span>Ticket #</span>
                        <span class="font-semibold text-white font-mono text-xs">{{ $ticket->ticket_number }}</span>
                    </div>
                </div>
            </div>

            <!-- QR Code Preview -->
            <div class="bg-white rounded-lg p-4 mb-6 text-center">
                @if($ticket->qr_code_path)
                    <img src="{{ Storage::url($ticket->qr_code_path) }}" alt="QR Code" class="w-48 h-48 mx-auto">
                    <p class="text-xs text-gray-600 mt-2">Show this QR code at entry</p>
                @else
                    <div class="h-48 flex items-center justify-center text-gray-400">QR Code Loading...</div>
                @endif
            </div>

            <!-- Preference Selection -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-white mb-3">How do you want your ticket?</label>

                <!-- Already Printed Badge -->
                @if($ticket->isPrinted())
                    <div class="bg-green-500/20 border border-green-400 rounded-lg p-4 mb-4">
                        <div class="text-sm text-green-100">
                            <div class="font-semibold">✅ Ticket Printed</div>
                            <div class="text-xs mt-1">Printed on {{ $ticket->printed_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                @endif

                <!-- Options -->
                <div class="space-y-3">
                    <!-- Digital Option -->
                    <button 
                        type="button"
                        onclick="selectPreference('digital')"
                        class="w-full preference-btn digital-btn p-4 rounded-lg border-2 transition-all text-left {{ $ticket->ticket_preference === 'digital' ? 'border-blue-400 bg-blue-500/20' : 'border-white/30 hover:border-white/50' }}"
                        :disabled="$ticket->isPrinted()"
                    >
                        <div class="font-semibold text-white">📱 Digital Only</div>
                        <div class="text-sm text-purple-100 mt-1">Get QR code on your phone, no paper needed</div>
                    </button>

                    <!-- Print Option -->
                    <button 
                        type="button"
                        onclick="selectPreference('print')"
                        class="w-full preference-btn print-btn p-4 rounded-lg border-2 transition-all text-left {{ $ticket->ticket_preference === 'print' ? 'border-yellow-400 bg-yellow-500/20' : 'border-white/30 hover:border-white/50' }}"
                        :disabled="$ticket->isPrinted()"
                    >
                        <div class="font-semibold text-white">🖨️ Print Physical Ticket</div>
                        <div class="text-sm text-purple-100 mt-1">Admin will print and give you a physical ticket</div>
                    </button>
                </div>

                <!-- Disabled Message if Printed -->
                @if($ticket->isPrinted())
                    <p class="text-xs text-yellow-200 mt-3">⚠️ Preference cannot be changed (ticket already printed)</p>
                @endif
            </div>

            <!-- Download Button -->
            <a 
                href="{{ route('ticket.avatar.download', $qrCode) }}"
                class="w-full bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-bold py-3 rounded-lg text-center block transition-all mb-4"
            >
                📥 Download Ticket (PDF)
            </a>

            <!-- Info Messages -->
            <div class="space-y-2 text-xs text-purple-200">
                @if($ticket->ticket_preference === 'digital')
                    <div class="bg-blue-500/10 border border-blue-400 rounded p-3">
                        📱 You selected digital. Show the QR code on your phone at entry.
                    </div>
                @elseif($ticket->ticket_preference === 'print')
                    @if($ticket->isPrinted())
                        <div class="bg-green-500/10 border border-green-400 rounded p-3">
                            ✅ Your ticket has been printed and is ready to pick up!
                        </div>
                    @else
                        <div class="bg-yellow-500/10 border border-yellow-400 rounded p-3">
                            ⏳ You selected print. Admin will print and notify you when ready.
                        </div>
                    @endif
                @endif
            </div>

            <!-- Status -->
            <div class="mt-6 pt-6 border-t border-white/20 text-center text-sm text-purple-200">
                <p>Ticket Status: 
                    <span class="font-semibold text-white">
                        @if($ticket->checked_in_at)
                            ✅ Checked In
                        @else
                            🎫 Active
                        @endif
                    </span>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-purple-100 text-sm">
            <p>{{ $ticket->event->organization->name }}</p>
            <p class="text-xs text-purple-300">{{ $ticket->event->location }}</p>
        </div>
    </div>

    <script>
        function selectPreference(preference) {
            @if(!$ticket->isPrinted())
                fetch("{{ route('ticket.update-preference', $qrCode) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ preference: preference })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        document.querySelectorAll('.preference-btn').forEach(btn => {
                            btn.classList.remove('border-blue-400', 'border-yellow-400', 'bg-blue-500/20', 'bg-yellow-500/20');
                            btn.classList.add('border-white/30');
                        });
                        
                        if (preference === 'digital') {
                            document.querySelector('.digital-btn').classList.add('border-blue-400', 'bg-blue-500/20');
                        } else {
                            document.querySelector('.print-btn').classList.add('border-yellow-400', 'bg-yellow-500/20');
                        }

                        // Show message
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            @endif
        }
    </script>
</body>
</html>