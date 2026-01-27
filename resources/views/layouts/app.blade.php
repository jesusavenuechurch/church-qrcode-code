<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'VENTIQ | Intelligent Ticketing & Event Streaming')</title>
    <meta name="description" content="The modern gateway for workshops, events, and seamless registrations in Lesotho. Simply Connected.">
    <meta name="keywords" content="event tickets Lesotho, Maseru ticketing, QR tickets, VENTIQ">
    
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon_io/android-chrome-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="VENTIQ - Simply Connected">
    <meta property="og:description" content="Professional event ticketing and high-quality streaming for Lesotho.">
    <meta property="og:image" content="{{ asset('images/ventiq-og-share.png') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .custom-blur { backdrop-filter: blur(12px); background-color: rgba(255, 255, 255, 0.9); }
    </style>
</head>

<body class="h-full overflow-hidden flex flex-col bg-[#F8FAFC] text-[#1D4069]" 
      x-data="{ 
        showChat: false, 
        submitted: false, 
        loading: false,
        name: '', 
        phone: '', 
        email: '', 
        subject: 'General Inquiry', 
        message: '' 
      }">

    <nav class="flex-none border-b border-gray-100 bg-white/90 custom-blur z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group transition-transform active:scale-95">
                <img src="{{ asset('images/ventiq-noback.png') }}" alt="VENTIQ" class="h-10 md:h-12 w-auto object-contain">
                <div class="flex flex-col leading-none">
                    <span class="text-lg md:text-xl font-black tracking-tighter uppercase text-[#1D4069]">
                        VENTI<span class="text-[#F07F22]">Q</span>
                    </span>
                    <span class="text-[8px] font-bold tracking-[0.2em] text-gray-400 uppercase">Intelligence</span>
                </div>
            </a>

            <div class="flex items-center gap-4 md:gap-8">
                <a href="/pricing" class="hidden sm:block text-[10px] font-bold uppercase tracking-widest text-gray-500 hover:text-[#F07F22] transition-colors">Pricing</a>
                <button @click="showChat = true" class="text-[10px] font-bold uppercase tracking-widest text-gray-500 hover:text-[#F07F22]">Contact</button>
                <a href="{{ route('filament.admin.auth.login') }}" 
                   class="px-5 py-2 rounded-xl bg-[#1D4069]/5 text-[#1D4069] text-[10px] font-bold uppercase tracking-widest hover:bg-[#1D4069] hover:text-white transition-all">
                    Login
                </a>
            </div>
        </div>
    </nav>

    <main class="flex-grow relative overflow-y-auto no-scrollbar">
        @yield('content')
    </main>

    <footer class="flex-none bg-white border-t border-gray-100 py-3 px-6">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">© {{ date('Y') }} VENTIQ LESOTHO</p>
            <div class="flex gap-4 text-[10px] font-bold uppercase tracking-tight text-gray-500">
                <a href="#" class="hover:text-[#F07F22]">Terms</a>
                <button @click="showChat = true" class="hover:text-[#F07F22]">Support</button>
            </div>
        </div>
    </footer>

    <div x-show="showChat" 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-[#1D4069]/40 backdrop-blur-sm"
         @click.self="showChat = false" x-cloak>
        
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-white/20 transform transition-all">
            <div class="h-1.5 w-full bg-gradient-to-r from-[#1D4069] via-[#F07F22] to-[#1D4069]"></div>

            <div class="p-8">
                <div x-show="!submitted">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-black text-[#1D4069] tracking-tight">Direct Access</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-1">Intelligence Console</p>
                        </div>
                        <button @click="showChat = false" class="p-2 hover:bg-gray-50 rounded-full transition-colors">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1" x-data="{ open: false }">
                            <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">Inquiry Type</label>
                            <div class="relative">
                                <button @click="open = !open" type="button" class="w-full bg-gray-50 rounded-2xl px-4 py-3 text-sm text-left flex justify-between items-center text-gray-600 focus:ring-2 focus:ring-[#F07F22]/20 outline-none">
                                    <span x-text="subject"></span>
                                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
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
                                <input type="text" x-model="name" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#F07F22]/20 outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">WhatsApp/Phone</label>
                                <input type="tel" x-model="phone" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#F07F22]/20 outline-none">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <div class="flex justify-between ml-1">
                                <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider">Email Address</label>
                                <span class="text-[9px] font-bold text-gray-300 uppercase">Optional</span>
                            </div>
                            <input type="email" x-model="email" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#F07F22]/20 outline-none">
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">Message</label>
                            <textarea x-model="message" rows="3" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#F07F22]/20 outline-none resize-none"></textarea>
                        </div>

                        <button @click="
                            if(!name || !phone || !message) { alert('Name, Phone, and Message are required.'); return; }
                            loading = true;
                            fetch('/contact', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                                body: JSON.stringify({ name, phone, email, subject, message })
                            }).then(res => { if(res.ok) { submitted = true; setTimeout(() => { showChat = false; submitted = false; }, 3000); } })
                            .finally(() => loading = false);" 
                            class="w-full py-4 rounded-2xl bg-[#1D4069] text-white font-bold text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-[#F07F22] transition-all flex items-center justify-center">
                            <span x-show="!loading text-white">Send Inquiry</span>
                            <i x-show="loading" class="fas fa-circle-notch animate-spin text-lg"></i>
                        </button>
                    </div>
                </div>

                <div x-show="submitted" class="py-12 text-center" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-90">
                    <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-check text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-[#1D4069] tracking-tight">Sent Successfully</h3>
                    <p class="text-sm text-gray-500 mt-2 px-6">Our team will be in touch via WhatsApp or Email shortly.</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>