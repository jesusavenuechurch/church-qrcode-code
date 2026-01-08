@extends('layouts.app')

@section('content')
<div class="h-full w-full flex items-center justify-center relative p-4" 
     x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">
    
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

            <a href="https://wa.me/26662552155?text=Hello%2C%20I%E2%80%99d%20like%20to%20register%20my%20organization%20on%20VentiQ%20and%20try%20the%20free%20trial%20for%20hosting%20an%20event.%20Please%20let%20me%20know%20the%20next%20steps."
                target="_blank"
                rel="noopener noreferrer"
                class="group p-5 rounded-2xl bg-white border-2 border-[#F07F22]/10 hover:border-[#F07F22]/30 shadow-sm hover:shadow-md active:scale-95 transition-all relative">
                
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
            </a>

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
</div>
@endsection