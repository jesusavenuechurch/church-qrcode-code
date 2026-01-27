@extends('layouts.app')

@section('content')
<div class="h-full w-full flex items-center justify-center relative p-4" 
     x-data="{ 
         loaded: false,
         showChat: false,
         message: '',
         name: '',
         email: '',
         subject: 'General Inquiry'
     }" 
     x-init="setTimeout(() => loaded = true, 100)">
    
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute top-[-5%] left-[-10%] w-64 h-64 bg-[#1D4069]/10 rounded-full blur-[80px]"></div>
        <div class="absolute bottom-[5%] right-[-10%] w-64 h-64 bg-[#F07F22]/10 rounded-full blur-[80px]"></div>
    </div>
    
    <div class="w-full max-w-xl transition-all duration-700 transform" 
         :class="loaded ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
        
        <div class="text-center mb-8">
            <span class="inline-block px-3 py-1 rounded-full bg-[#1D4069]/5 text-[#1D4069] text-[10px] font-bold uppercase tracking-tighter mb-3">
                Event Intelligence
            </span>
            <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 tracking-tight leading-none mb-3">
                Simply <span class="text-[#F07F22]">Connected.</span>
            </h1>
            <p class="text-sm md:text-base text-gray-500 leading-relaxed px-4">
                The modern gateway for workshops, events, and seamless registrations.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-8">
            
            <a href="{{ route('events.browse') }}" 
               class="group p-5 rounded-2xl bg-[#1D4069] text-white shadow-lg shadow-blue-900/20 active:scale-95 transition-all relative overflow-hidden">
                <div class="flex justify-between items-center relative z-10">
                    <div>
                        <h2 class="text-lg font-bold">Find an Event</h2>
                        <p class="text-xs text-blue-100/70">Register and get your ticket</p>
                    </div>
                    <div class="bg-white/10 p-2 rounded-xl group-hover:bg-white/20 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </div>
            </a>

            <button @click="showChat = true"
                class="group p-5 rounded-2xl bg-white border-2 border-[#F07F22]/10 hover:border-[#F07F22]/30 shadow-sm hover:shadow-md active:scale-95 transition-all relative text-left">
                
                <div class="absolute -top-3 -right-3 bg-[#F07F22] text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm rotate-3 group-hover:rotate-6 transition-transform">
                    FREE TRIAL
                </div>

                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Host an Event</h2>
                        <p class="text-xs font-medium text-[#F07F22]">Try it for your event, no card required</p>
                    </div>
                    <div class="bg-[#F07F22]/10 p-2 rounded-xl group-hover:bg-[#F07F22]/20 transition-colors text-[#F07F22]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                </div>
            </button>

            <a href="/pricing" 
               class="group flex justify-between items-center p-4 rounded-2xl bg-gray-50 hover:bg-white border border-transparent hover:border-gray-200 hover:shadow-sm active:scale-95 transition-all">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-gray-200 text-gray-500 group-hover:text-[#1D4069] group-hover:bg-[#1D4069]/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-700 group-hover:text-gray-900">View Pricing</h2>
                        <p class="text-[10px] text-gray-400 font-medium">Compare plans & features</p>
                    </div>
                </div>
                <div class="text-gray-300 group-hover:text-gray-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>

        </div>

        <div class="text-center">
            <p class="text-xs text-gray-400 font-medium">
                Already using Ventiq? 
                <a href="{{ route('filament.admin.auth.login') }}" class="text-[#1D4069] font-bold hover:underline ml-1">
                    Log in here
                </a>
            </p>
        </div>

    </div>

    <!-- WhatsApp Chat Popup -->
    <div x-show="showChat" 
     x-data="{ submitted: false, loading: false }"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#1D4069]/40 backdrop-blur-sm"
     @click.self="showChat = false"
     x-cloak>
    
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-white/20 transform transition-all">
        <div class="h-1.5 w-full bg-gradient-to-r from-[#1D4069] via-[#F07F22] to-[#1D4069]"></div>

        <div class="p-8">
            <div x-show="!submitted" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-2xl font-black text-[#1D4069] tracking-tight">Direct Access</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">VENTIQ Intelligence Console</p>
                    </div>
                    <button @click="showChat = false" class="p-2 hover:bg-gray-50 rounded-full transition-colors">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1" x-data="{ open: false }">
                        <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">Nature of Inquiry</label>
                        <div class="relative">
                            <button @click="open = !open" type="button" class="w-full bg-gray-50 rounded-2xl px-4 py-3 text-sm text-left flex justify-between items-center text-gray-600 outline-none ring-[#F07F22]/20 focus:ring-2">
                                <span x-text="subject"></span>
                                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 w-full bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                                <template x-for="option in ['Host an Event (Free Trial)', 'Ticket Support', 'Partnership Inquiry', 'General Support']">
                                    <button @click="subject = option; open = false" type="button" class="w-full text-left px-4 py-3 text-sm hover:bg-gray-50 hover:text-[#F07F22] transition-colors text-gray-600" x-text="option"></button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">Name</label>
                            <input type="text" x-model="name" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#F07F22]/20 transition-all">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">Phone</label>
                            <input type="tel" x-model="phone" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#F07F22]/20 transition-all">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <div class="flex justify-between ml-1">
                            <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider">Email</label>
                            <span class="text-[9px] font-bold text-gray-300 uppercase">Optional</span>
                        </div>
                        <input type="email" x-model="email" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#F07F22]/20 transition-all">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">Message</label>
                        <textarea x-model="message" rows="3" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#F07F22]/20 transition-all resize-none"></textarea>
                    </div>

                    <button @click="
                        if(!name || !phone || !message) { alert('Please fill in the required fields.'); return; }
                        loading = true;
                        fetch('/contact', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ name, phone, email, subject, message })
                        }).then(response => {
                            if(response.ok) { submitted = true; setTimeout(() => { showChat = false; submitted = false; }, 3000); }
                            loading = false;
                        })" 
                        :disabled="loading"
                        class="w-full py-4 rounded-2xl bg-[#1D4069] text-white font-bold text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-[#F07F22] transition-all flex items-center justify-center">
                        <span x-show="!loading">Send Inquiry</span>
                        <svg x-show="loading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>

            <div x-show="submitted" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-90" class="py-12 text-center">
                <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-[#1D4069] tracking-tight">Transmission Complete</h3>
                <p class="text-sm text-gray-500 mt-2 px-6">Your inquiry has been synced with our team. We'll be in touch shortly.</p>
                <div class="mt-8 flex justify-center gap-1">
                    <div class="w-1 h-1 rounded-full bg-gray-200 animate-bounce"></div>
                    <div class="w-1 h-1 rounded-full bg-gray-200 animate-bounce [animation-delay:-.15s]"></div>
                    <div class="w-1 h-1 rounded-full bg-gray-200 animate-bounce [animation-delay:-.3s]"></div>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>
@endsection