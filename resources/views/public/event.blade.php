<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->name }} - {{ $organization->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @if($organization->logo)
                        <img src="{{ Storage::url($organization->logo) }}" alt="{{ $organization->name }}" class="h-12 w-auto">
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $organization->name }}</h1>
                        <p class="text-sm text-gray-600">{{ $organization->tagline }}</p>
                    </div>
                </div>
                <a href="{{ route('public.events', $organization->slug) }}" class="text-blue-600 hover:text-blue-800">
                    View All Events
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                @if($event->banner_image)
                <div class="relative group rounded-3xl overflow-hidden shadow-2xl bg-gray-900 border border-gray-100 cursor-zoom-in h-[300px] sm:h-[400px]" 
                    onclick="openLightbox()">
                    
                    <img src="{{ Storage::url($event->banner_image) }}" 
                        id="eventPoster"
                        alt="{{ $event->name }}" 
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors duration-300"></div>
                    
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="bg-white/20 backdrop-blur-md text-white px-6 py-2 rounded-full text-sm font-bold border border-white/30 shadow-xl">
                            <i class="fas fa-expand-alt mr-2"></i> View Full Flyer
                        </div>
                    </div>
                </div>

                <div id="lightbox" class="fixed inset-0 z-[100] hidden bg-black/95 backdrop-blur-xl flex items-center justify-center p-4 cursor-zoom-out" onclick="closeLightbox()">
                    <button class="fixed top-6 right-6 text-white text-3xl hover:text-gray-300 transition-colors z-[110]">
                        <i class="fas fa-times"></i>
                    </button>
                    
                    <img src="{{ Storage::url($event->banner_image) }}" 
                        class="max-w-full max-h-[90vh] w-auto h-auto shadow-2xl rounded-lg animate-in zoom-in-95 duration-300"
                        onclick="event.stopPropagation()">
                </div>
                @endif
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 p-8 border border-gray-100">
                    <div class="mb-8 border-b border-gray-100 pb-8">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Official Event</span>
                            @if($event->registration_deadline && $event->registration_deadline->isFuture())
                                <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-widest rounded-lg">Registration Open</span>
                            @endif
                        </div>
                        <h2 class="text-4xl font-extrabold text-gray-900 leading-tight mb-2">{{ $event->name }}</h2>
                        @if($event->tagline)
                            <p class="text-xl text-gray-400 font-medium italic">"{{ $event->tagline }}"</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="flex items-center p-5 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 mr-4">
                                <i class="fas fa-calendar-alt text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date & Time</p>
                                <p class="font-bold text-gray-900 leading-tight">{{ $event->event_date->format('D, M d, Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $event->event_date->format('g:i A') }} Start</p>
                            </div>
                        </div>

                        <div class="flex items-center p-5 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="w-12 h-12 bg-rose-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-rose-200 mr-4">
                                <i class="fas fa-map-marker-alt text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Location</p>
                                <p class="font-bold text-gray-900 leading-tight">{{ $event->venue }}</p>
                                <p class="text-xs text-gray-500 truncate w-40">{{ $event->location }}</p>
                            </div>
                        </div>
                    </div>

                    @if($event->description)
                    <div class="mt-10 pt-10 border-t border-gray-100">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em] mb-6">About the Experience</h3>
                        <div class="prose prose-blue max-w-none text-gray-600 leading-relaxed">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-8 space-y-6">
                    <div class="bg-white rounded-3xl shadow-2xl shadow-gray-200/50 p-6 border border-gray-100">
                        <h3 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-ticket-alt text-blue-600 mr-3"></i> Select Tickets
                        </h3>

                        @if($canRegister && $event->tiers->count() > 0)
                            <div class="space-y-4">
                                @foreach($event->tiers as $tier)
                                    @php
                                        $availability = $tierAvailability[$tier->id];
                                        $isSoldOut = $availability['is_sold_out'];
                                    @endphp

                                    <div class="group relative bg-white border-2 rounded-2xl p-4 transition-all duration-300 {{ $isSoldOut ? 'opacity-50 border-gray-100' : 'border-gray-100 hover:border-blue-500 hover:shadow-lg hover:shadow-blue-500/10' }}">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $tier->tier_name }}</h4>
                                               @if($tier->description)
                                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed italic">
                                                        {{ $tier->description }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-right ml-2">
                                                <p class="text-xl font-black text-gray-900">{{ number_format($tier->price) }}<span class="text-xs ml-0.5 text-gray-400">LSL</span></p>
                                            </div>
                                        </div>

                                        @if($event->allow_installments && $tier->price > 0 && !$isSoldOut)
                                            <div class="mt-3 p-2.5 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center justify-between">
                                                <span class="text-[10px] font-black text-emerald-700 uppercase tracking-tight">Deposit Available</span>
                                                <span class="text-[10px] font-bold text-emerald-600 bg-white px-2 py-0.5 rounded shadow-sm">
                                                    From {{ number_format($tier->price * ($event->minimum_deposit_percentage / 100)) }} LSL
                                                </span>
                                            </div>
                                        @endif

                                        @if(!$isSoldOut)
                                            <button onclick="selectTier({{ $tier->id }})" 
                                                    class="w-full mt-4 py-3 bg-gray-900 group-hover:bg-blue-600 text-white font-bold rounded-xl text-sm transition-all transform active:scale-95 shadow-md shadow-gray-200">
                                                Get Started <i class="fas fa-arrow-right ml-2 text-xs opacity-50 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                                            </button>
                                        @else
                                            <div class="w-full mt-4 py-3 bg-gray-50 text-gray-400 font-bold rounded-xl text-sm text-center border border-gray-100 italic">
                                                Sold Out
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-8 pt-8 border-t border-gray-100">
                                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-5 border border-amber-100 text-center">
                                    <h4 class="text-sm font-black text-amber-900 uppercase mb-2 tracking-wide">Existing Registration?</h4>
                                    <p class="text-xs text-amber-700 mb-4 leading-tight">Complete your installment payment by searching your ticket.</p>
                                    <a href="{{ route('installment.search') }}" 
                                    class="inline-block w-full py-3 bg-white text-orange-600 font-bold rounded-xl text-sm border border-orange-200 hover:bg-orange-600 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-search mr-2"></i> Find My Ticket
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

<script>
    function openLightbox() {
        document.getElementById('lightbox').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    }
    
    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
        document.body.style.overflow = 'auto'; // Re-enable scrolling
    }

    // Close on 'Escape' key
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape") closeLightbox();
    });

    function selectTier(tierId) {
        window.location.href = `/register/{{ $organization->slug }}/{{ $event->slug }}?tier=${tierId}`;
    }
</script>
    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} {{ $organization->name }}. All rights reserved.</p>
                @if($organization->contact_email)
                    <p class="mt-2 text-gray-400">
                        Contact: <a href="mailto:{{ $organization->contact_email }}" class="text-blue-400 hover:text-blue-300">{{ $organization->contact_email }}</a>
                    </p>
                @endif
            </div>
        </div>
    </footer>

    <script>
        function selectTier(tierId, tierName, price) {
            // Redirect to registration/checkout page with tier selected
            window.location.href = `/register/{{ $organization->slug }}/{{ $event->slug }}?tier=${tierId}`;
        }
    </script>
</body>
</html>