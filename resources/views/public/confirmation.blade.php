<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Confirmed - {{ $event->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-gray-900">
    <header class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                @if($event->organization->logo)
                    <img src="{{ Storage::url($event->organization->logo) }}" alt="Logo" class="h-8 w-auto">
                @else
                    <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center">
                        <span class="text-white text-[10px] font-black uppercase tracking-tighter">{{ substr($event->organization->name, 0, 2) }}</span>
                    </div>
                @endif
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-400">{{ $event->organization->name }}</span>
            </div>
            <a href="{{ route('public.event', ['orgSlug' => $event->organization->slug, 'eventSlug' => $event->slug]) }}" 
               class="text-[10px] font-black uppercase tracking-widest text-blue-600 hover:text-blue-800 transition-colors">
                Event Details <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100">
            
            <div class="p-10 text-center border-b border-gray-50 bg-gradient-to-b from-slate-50/50 to-transparent">
                <div class="mb-6">
                    @if($ticket->payment_status === 'completed')
                        <div class="w-24 h-24 bg-emerald-500 rounded-3xl flex items-center justify-center mx-auto shadow-xl shadow-emerald-200 animate-bounce">
                            <i class="fas fa-check text-white text-4xl"></i>
                        </div>
                    @else
                        <div class="w-24 h-24 bg-amber-500 rounded-3xl flex items-center justify-center mx-auto shadow-xl shadow-amber-200 animate-pulse">
                            <i class="fas fa-clock text-white text-4xl"></i>
                        </div>
                    @endif
                </div>

                @if($ticket->payment_status === 'completed')
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight mb-2 uppercase">Confirmed! 🎉</h2>
                    <p class="text-sm font-bold text-emerald-600 uppercase tracking-widest">
                        @if(count($allTickets) > 1) {{ count($allTickets) }} Tickets Active @else Your Ticket is Active @endif
                    </p>
                @else
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight mb-2 uppercase">Pending Verification</h2>
                    <p class="text-sm font-bold text-amber-600 uppercase tracking-widest">
                        We're awaiting payment for {{ count($allTickets) }} registered attendees
                    </p>
                @endif
            </div>

            <div class="p-8 sm:p-12 space-y-12">
                
                <section>
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center text-white text-xs">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">Your Passes</h3>
                        </div>
                        <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $ticket->payment_status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ ucfirst($ticket->payment_status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($allTickets as $index => $singleTicket)
                            <div class="relative group">
                                <div class="bg-white border-2 border-gray-100 rounded-[2rem] p-6 hover:border-blue-600 transition-all duration-300 shadow-sm hover:shadow-xl">
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white text-sm font-black shadow-lg shadow-gray-200">
                                                {{ $index + 1 }}
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Attendee</p>
                                                <p class="font-black text-gray-900 uppercase text-sm leading-none">{{ $singleTicket->client->full_name }}</p>
                                            </div>
                                        </div>
                                        @if($loop->first)
                                            <span class="text-[9px] bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-black uppercase tracking-widest border border-blue-100">Primary</span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 border-t border-dashed border-gray-100 pt-6">
                                        <div>
                                            <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Ticket ID</p>
                                            <p class="font-mono text-[10px] font-black text-gray-900 mt-1">{{ $singleTicket->ticket_number }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Tier</p>
                                            <p class="text-[10px] font-black text-gray-900 mt-1 uppercase tracking-tight">{{ $singleTicket->tier->tier_name }}</p>
                                        </div>
                                    </div>

                                    @if($ticket->payment_status === 'completed')
                                        <a href="{{ route('ticket.download', $singleTicket->qr_code) }}" target="_blank"
                                           class="mt-6 flex items-center justify-center w-full bg-slate-50 hover:bg-blue-600 hover:text-white py-3 rounded-xl transition-all group/btn">
                                            <i class="fas fa-download mr-2 text-xs group-hover/btn:translate-y-0.5 transition-transform"></i>
                                            <span class="text-[10px] font-black uppercase tracking-[0.2em]">Get PDF Ticket</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                @if($ticket->payment_status !== 'completed')
                    <section class="bg-slate-900 rounded-[2.5rem] p-8 sm:p-10 text-white relative overflow-hidden shadow-2xl">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/10 rounded-full -mr-32 -mt-32"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center space-x-3 mb-8">
                                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white">
                                    <i class="fas fa-money-bill-transfer"></i>
                                </div>
                                <h3 class="text-xs font-black text-amber-400 uppercase tracking-[0.3em]">Payment Guide</h3>
                            </div>

                            @if($paymentMethodDetails)
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                    <div class="space-y-6">
                                        <div class="bg-white/5 backdrop-blur-md rounded-2xl p-6 border border-white/10">
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Method</p>
                                            <div class="flex items-center space-x-4">
                                                <i class="fas {{ $paymentMethodDetails->icon ?? 'fa-university' }} text-3xl text-amber-500"></i>
                                                <span class="text-xl font-black uppercase tracking-tight">{{ $paymentMethodDetails->payment_method }}</span>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Due</p>
                                                <p class="text-2xl font-black text-white">{{ number_format($allTickets->sum('amount')) }} <span class="text-xs">LSL</span></p>
                                            </div>
                                            <div class="bg-white/5 rounded-2xl p-6 border border-white/10">
                                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Ref ID</p>
                                                <p class="text-lg font-black text-amber-500 font-mono tracking-tighter">{{ $ticket->ticket_number }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-amber-500/10 rounded-2xl p-8 border-2 border-dashed border-amber-500/30">
                                        <h4 class="text-sm font-black uppercase tracking-widest text-amber-500 mb-4">Instructional Steps:</h4>
                                        <p class="text-sm font-bold text-gray-300 leading-relaxed italic mb-6">
                                            "{{ $paymentMethodDetails->instructions }}"
                                        </p>
                                        <div class="space-y-4">
                                            <div class="flex items-center space-x-3 text-xs font-black uppercase tracking-wider text-gray-400">
                                                <i class="fas fa-check-circle text-amber-500"></i>
                                                <span>We verify within 24 hours</span>
                                            </div>
                                            <div class="flex items-center space-x-3 text-xs font-black uppercase tracking-wider text-gray-400">
                                                <i class="fas fa-check-circle text-amber-500"></i>
                                                <span>Auto-delivery on confirmation</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

                @if($ticket->payment_status === 'completed')
                    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($ticket->client->email)
                            <div class="bg-blue-50/50 border-2 border-blue-100 rounded-[2rem] p-8 text-center">
                                <i class="fas fa-envelope text-blue-500 text-3xl mb-4"></i>
                                <h4 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-1">Sent to Email</h4>
                                <p class="text-[10px] font-bold text-blue-600 truncate">{{ $ticket->client->email }}</p>
                            </div>
                        @endif

                        @if($ticket->has_whatsapp)
                            <div class="bg-emerald-50/50 border-2 border-emerald-100 rounded-[2rem] p-8 text-center animate-in zoom-in duration-700">
                                <i class="fa-brands fa-whatsapp text-emerald-500 text-4xl mb-4"></i>
                                <h4 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-1">Sent to WhatsApp</h4>
                                <p class="text-[10px] font-bold text-emerald-600">+266 {{ $ticket->client->phone }}</p>
                            </div>
                        @endif
                    </section>
                @endif

                <div class="pt-8 border-t border-gray-50 flex flex-col items-center">
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <a href="{{ route('public.event', ['orgSlug' => $event->organization->slug, 'eventSlug' => $event->slug]) }}" 
                           class="px-8 py-4 bg-gray-900 text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-xl hover:bg-blue-600 transition-all shadow-xl shadow-gray-200">
                           <i class="fas fa-home mr-2"></i> Event Home
                        </a>
                        @if($event->organization->contact_email)
                            <a href="mailto:{{ $event->organization->contact_email }}" 
                               class="px-8 py-4 bg-white border-2 border-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-[0.3em] rounded-xl hover:border-gray-900 hover:text-gray-900 transition-all">
                               <i class="fas fa-question-circle mr-2"></i> Help Desk
                            </a>
                        @endif
                    </div>
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">
                        &copy; {{ date('Y') }} {{ $event->organization->name }}
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-12 text-center">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] opacity-50">Powered by the Registration Engine</p>
    </footer>
</body>
</html>