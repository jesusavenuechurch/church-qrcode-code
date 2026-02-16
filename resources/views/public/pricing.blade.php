@extends('layouts.app')

@section('title', 'Pricing | Event Ticketing Plans in Lesotho - VENTIQ')

@section('content')
<div class="bg-slate-50 antialiased text-slate-900 pb-20">
    <section class="bg-white border-b border-slate-200">
        <div class="max-w-4xl mx-auto px-4 py-16 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-[#1D4069]/5 text-[#1D4069] text-[10px] font-black uppercase tracking-widest mb-4">
                Simple & Transparent
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-[#1D4069] mb-6 tracking-tight leading-none">
                Pricing based on event size,<br class="hidden md:block"> not complicated features.
            </h1>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto">
                No subscriptions. No hidden fees. Just a one-time payment per event. 
                Experience the power of digital QR ticketing in Lesotho.
            </p>
        </div>
    </section>

    <section class="max-w-5xl mx-auto px-4 -mt-8 mb-16 relative z-10">
        <div class="bg-gradient-to-r from-[#1D4069] to-indigo-700 rounded-3xl p-1 shadow-2xl transform hover:scale-[1.01] transition-transform duration-300">
            <div class="bg-white rounded-[1.3rem] p-8 md:p-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="bg-[#F07F22] text-white text-[10px] font-black px-2 py-1 rounded uppercase tracking-wide">Limited Offer</span>
                        <h3 class="text-2xl font-extrabold text-slate-900">Try VENTIQ Risk-Free 🎁</h3>
                    </div>
                    <p class="text-slate-600 text-lg mb-4">
                        Every organisation gets <span class="font-bold text-slate-900">1 Standard Event (300 Tickets)</span> completely <span class="text-green-600 font-black">FREE</span>.
                    </p>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Test online registration setup</li>
                        <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> Experience real-time QR scanning</li>
                    </ul>
                </div>
                <div class="flex-shrink-0">
                    <a href="/org/register" 
                    class="inline-block bg-[#1D4069] hover:bg-[#F07F22] text-white text-lg font-bold py-4 px-8 rounded-xl shadow-lg transition-all hover:-translate-y-1">
                        Claim Your Free Event
                    </a>
                </div>
            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
            
            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 relative group">
                <div class="absolute top-0 left-0 w-full h-2 bg-green-500 rounded-t-2xl"></div>
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Starter Event</h3>
                    <p class="text-sm text-slate-500 h-10">Best for workshops, trainings, and small gatherings.</p>
                </div>
                <div class="mb-8">
                    <span class="text-4xl font-extrabold text-slate-900">M250</span>
                    <span class="text-slate-400 font-medium">/ event</span>
                </div>
                
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start text-sm text-slate-600">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Up to <strong class="text-slate-900">50 tickets</strong></span>
                    </li>
                    <li class="flex items-start text-sm text-slate-600">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Online self-registration link</span>
                    </li>
                    <li class="flex items-start text-sm text-slate-600">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>WhatsApp & Email delivery</span>
                    </li>
                    <li class="flex items-start text-sm text-slate-600">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Live attendance list</span>
                    </li>
                </ul>
                <a href="/org/register" class="block w-full py-3 px-4 bg-slate-50 hover:bg-green-50 text-slate-700 hover:text-green-700 font-bold text-center rounded-xl border border-slate-200 transition-colors">
                    Choose Starter
                </a>
            </div>

            <div class="bg-white rounded-2xl p-8 border-2 border-blue-100 shadow-xl relative transform md:-translate-y-4 z-10">
                <div class="absolute top-0 right-0 bg-[#1D4069] text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl rounded-tr-xl uppercase tracking-widest">
                    Most Popular
                </div>
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Standard Event</h3>
                    <p class="text-sm text-slate-500 h-10">Conferences, seminars & large workshops.</p>
                </div>
                <div class="mb-8">
                    <span class="text-4xl font-extrabold text-slate-900">M700</span>
                    <span class="text-slate-400 font-medium">/ event</span>
                </div>
                
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start text-sm text-slate-600">
                        <i class="fas fa-check text-[#1D4069] mt-1 mr-3"></i>
                        <span>Up to <strong class="text-slate-900">300 tickets</strong></span>
                    </li>
                    <li class="flex items-start text-sm text-slate-600">
                        <i class="fas fa-check text-[#1D4069] mt-1 mr-3"></i>
                        <span>All Starter features included</span>
                    </li>
                    <li class="flex items-start text-sm text-slate-600">
                        <i class="fas fa-check text-[#1D4069] mt-1 mr-3"></i>
                        <span>Real-time attendance tracking</span>
                    </li>
                    <li class="flex items-start text-sm text-slate-600">
                        <i class="fas fa-check text-[#1D4069] mt-1 mr-3"></i>
                        <span>Android phone scanning app</span>
                    </li>
                </ul>
                <a href="/org/register" class="block w-full py-4 px-4 bg-[#1D4069] hover:bg-[#F07F22] text-white font-bold text-center rounded-xl shadow-lg transition-colors">
                    Get Standard Access
                </a>
                <p class="text-[10px] text-center text-slate-400 mt-3 font-bold uppercase tracking-widest">Free trial applies to this tier</p>
            </div>

            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-purple-500 rounded-t-2xl"></div>
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Multi-Event Pack</h3>
                    <p class="text-sm text-slate-500 h-10">For organisations running multiple events throughout the year.</p>
                </div>
                <div class="mb-8">
                    <span class="text-4xl font-extrabold text-slate-900">M1,500</span>
                    <span class="text-slate-400 font-medium">/ pack</span>
                </div>
                
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start text-sm text-slate-600">
                        <i class="fas fa-check text-purple-500 mt-1 mr-3"></i>
                        <span>Up to <strong class="text-slate-900">3 events</strong></span>
                    </li>
                    <li class="flex items-start text-sm text-slate-600">
                        <i class="fas fa-check text-purple-500 mt-1 mr-3"></i>
                        <span>1,000 tickets total (shared)</span>
                    </li>
                    <li class="flex items-start text-sm text-slate-600">
                        <i class="fas fa-check text-purple-500 mt-1 mr-3"></i>
                        <span>Central attendance reports</span>
                    </li>
                    <li class="flex items-start text-sm text-slate-600">
                        <i class="fas fa-plus text-purple-500 mt-1 mr-3"></i>
                        <span class="text-slate-500 italic">Scalable above 1,000 tix</span>
                    </li>
                </ul>
                <button @click="showChat = true; subject = 'Multi-Event Pack Inquiry'" class="block w-full py-3 px-4 bg-slate-50 hover:bg-purple-50 text-slate-700 hover:text-purple-700 font-bold text-center rounded-xl border border-slate-200 transition-colors">
                    Contact Sales
                </button>
            </div>
        </div>

        <div class="mt-20 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 flex items-start gap-4">
                <div class="bg-blue-50 p-3 rounded-xl text-[#1D4069]"><i class="fas fa-mobile-alt text-xl"></i></div>
                <div>
                    <h4 class="font-bold text-[#1D4069]">No Scanners Required</h4>
                    <p class="text-sm text-slate-500 mt-1">Don't buy hardware. Use any Android phone to scan tickets securely at the door.</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-slate-200 flex items-start gap-4">
                <div class="bg-green-50 p-3 rounded-xl text-green-600"><i class="fas fa-leaf text-xl"></i></div>
                <div>
                    <h4 class="font-bold text-slate-900">100% Paperless</h4>
                    <p class="text-sm text-slate-500 mt-1">Attendees register online and receive a digital QR code instantly. No printing needed.</p>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection