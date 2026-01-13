<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Hub - {{ $ticket->event->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { 
            font-family: 'Inter', sans-serif;
            background: #0f172a; 
        }
        .glass-card { 
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-800 via-slate-900 to-black">
    
    <div class="w-full max-w-md animate-in fade-in zoom-in duration-700">
        
        <div class="text-center mb-10">
            <div class="inline-flex items-center space-x-2 bg-white/5 border border-white/10 px-4 py-2 rounded-full mb-4">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-400">Live Access Pass</span>
            </div>
            <h1 class="text-2xl font-black text-white uppercase tracking-tighter">{{ $ticket->event->name }}</h1>
        </div>

        <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl">
            
            <div id="ticket-capture" class="p-8 pb-0">
                <div class="bg-white rounded-[2rem] overflow-hidden shadow-xl">
                    <div class="bg-gray-900 px-6 py-4 flex justify-between items-center">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ $ticket->event->organization->name }}</span>
                        <span class="text-[9px] font-black text-white uppercase tracking-widest">{{ $ticket->tier->tier_name }}</span>
                    </div>
                    
                    <div class="p-8 text-center bg-white">
                        <div class="relative inline-block group">
                            @if($ticket->qr_code_path)
                                <img src="{{ Storage::url($ticket->qr_code_path) }}" crossorigin="anonymous" alt="QR Code" class="w-48 h-48 mx-auto">
                            @else
                                <div class="w-48 h-48 flex items-center justify-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-100">
                                    <i class="fas fa-qrcode text-gray-200 text-4xl"></i>
                                </div>
                            @endif
                            <div class="absolute -top-2 -left-2 w-6 h-6 border-t-4 border-l-4 border-gray-900 rounded-tl-lg"></div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 border-t-4 border-right-4 border-gray-900 rounded-tr-lg"></div>
                            <div class="absolute -bottom-2 -left-2 w-6 h-6 border-b-4 border-l-4 border-gray-900 rounded-bl-lg"></div>
                            <div class="absolute -bottom-2 -right-2 w-6 h-6 border-b-4 border-r-4 border-gray-900 rounded-br-lg"></div>
                        </div>
                        
                        <div class="mt-6">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Guest Name</p>
                            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">{{ $ticket->client->full_name }}</h2>
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-gray-50 border-t border-dashed border-gray-200 relative">
                        <div class="absolute -left-3 top-[-12px] w-6 h-6 bg-[#1a2438] rounded-full"></div>
                        <div class="absolute -right-3 top-[-12px] w-6 h-6 bg-[#1a2438] rounded-full"></div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Ticket ID</p>
                                <p class="text-xs font-bold text-gray-900 font-mono">{{ $ticket->ticket_number }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Entry Date</p>
                                <p class="text-xs font-bold text-gray-900 uppercase">{{ $ticket->event->event_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 space-y-6">
                
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Fulfillment Preference</label>
                    
                    <div class="grid grid-cols-1 gap-3">
                        <button type="button" onclick="selectPreference('digital')" 
                            class="group relative overflow-hidden p-4 rounded-2xl border-2 transition-all text-left {{ $ticket->ticket_preference === 'digital' ? 'border-emerald-500 bg-emerald-500/10' : 'border-white/5 bg-white/5 hover:bg-white/10' }}"
                            {{ $ticket->isPrinted() ? 'disabled' : '' }}>
                            <div class="flex items-center justify-between pointer-events-none">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $ticket->ticket_preference === 'digital' ? 'bg-emerald-500 text-white' : 'bg-white/10 text-gray-400' }}">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div>
                                        <div class="text-[11px] font-black text-white uppercase tracking-tight">Digital Pass</div>
                                        <div class="text-[9px] font-bold text-gray-500 uppercase tracking-tighter">Smartphone Ready</div>
                                    </div>
                                </div>
                                @if($ticket->ticket_preference === 'digital')
                                    <i class="fas fa-check-circle text-emerald-500"></i>
                                @endif
                            </div>
                        </button>

                        {{-- <button type="button" onclick="selectPreference('print')" 
                            class="group relative overflow-hidden p-4 rounded-2xl border-2 transition-all text-left {{ $ticket->ticket_preference === 'print' ? 'border-amber-500 bg-amber-500/10' : 'border-white/5 bg-white/5 hover:bg-white/10' }}"
                            {{ $ticket->isPrinted() ? 'disabled' : '' }}>
                            <div class="flex items-center justify-between pointer-events-none">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $ticket->ticket_preference === 'print' ? 'bg-amber-500 text-white' : 'bg-white/10 text-gray-400' }}">
                                        <i class="fas fa-print"></i>
                                    </div>
                                    <div>
                                        <div class="text-[11px] font-black text-white uppercase tracking-tight">Physical Print</div>
                                        <div class="text-[9px] font-bold text-gray-500 uppercase tracking-tighter">Collect from Admin</div>
                                    </div>
                                </div>
                                @if($ticket->ticket_preference === 'print')
                                    <i class="fas fa-circle-notch fa-spin text-amber-500"></i>
                                @endif
                            </div>
                        </button> --}}
                    </div>
                </div>

                <div class="pt-4 space-y-3">
                    <button id="download-png"
                            class="flex items-center justify-center w-full bg-emerald-600 text-white font-black py-4 rounded-[1.2rem] shadow-lg hover:bg-emerald-700 transition-all uppercase tracking-[0.2em] text-[10px]">
                        <i class="fas fa-camera mr-3"></i> Save to Gallery (PNG)
                    </button>

                    <a href="{{ route('ticket.avatar.download', $qrCode) }}"
                       class="flex items-center justify-center w-full bg-white/10 border border-white/20 text-white font-black py-4 rounded-[1.2rem] hover:bg-white/20 transition-all uppercase tracking-[0.2em] text-[10px]">
                        <i class="fas fa-file-pdf mr-3 text-red-400"></i> Download PDF Document
                    </a>
                </div>

            </div>
        </div>

        <div class="text-center mt-10">
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ $ticket->event->organization->name }}</p>
            <p class="text-[9px] font-bold text-gray-600 mt-1 uppercase tracking-tighter">{{ $ticket->event->location }}</p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // PNG Generation Logic
        document.getElementById('download-png').addEventListener('click', function() {
            const ticketArea = document.getElementById('ticket-capture');
            const btn = this;
            
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-3"></i> Generating...';
            btn.disabled = true;

            html2canvas(ticketArea, {
                scale: 3, 
                useCORS: true, 
                backgroundColor: null,
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Ticket-{{ $ticket->ticket_number }}.png';
                link.href = canvas.toDataURL('image/png', 1.0);
                link.click();
                
                btn.innerHTML = '<i class="fas fa-camera mr-3"></i> Save to Gallery (PNG)';
                btn.disabled = false;
            }).catch(err => {
                console.error('Capture failed:', err);
                btn.disabled = false;
            });
        });

        // Preference Update Logic
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
                        location.reload();
                    }
                });
            @endif
        }
    </script>
</body>
</html>