<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <title>@yield('title', 'VENTIQ | Intelligent Ticketing & Event Streaming')</title>
    <meta name="description" content="The modern gateway for workshops, events, and seamless registrations in Lesotho. Simply Connected.">
    <meta name="author" content="VENTIQ">
    
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:title" content="VENTIQ - Simply Connected">
    <meta property="og:description" content="Professional event ticketing and high-quality streaming for Lesotho.">
    <meta property="og:image" content="{{ asset('images/ventiq-og-share.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="VENTIQ">
    <meta property="og:locale" content="en_LS">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="VENTIQ | Event Intelligence">
    <meta name="twitter:description" content="Register, Pay, and Stream. All in one place.">
    <meta name="twitter:image" content="{{ asset('images/ventiq-og-share.png') }}">
    
    <meta name="theme-color" content="#1D4069">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="VENTIQ">
    
    <link rel="icon" type="image/png" href="{{ asset('images/favicon_io/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        /* Smooth transitions for navigation */
        .backdrop-blur-md { transition: background-color 0.3s ease; }
    </style>
</head>

<body class="h-full overflow-hidden flex flex-col bg-[#F8FAFC] text-[#1D4069]">

    <nav class="flex-none border-b border-gray-100 bg-white/90 backdrop-blur-md z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group transition-transform active:scale-95">
                <img src="{{ asset('images/ventiq-noback.png') }}" 
                    alt="VENTIQ Logo" 
                    class="h-10 md:h-12 w-auto object-contain transition-transform group-hover:scale-105">
                
                <div class="flex flex-col leading-none">
                    <span class="text-lg md:text-xl font-black tracking-tighter uppercase text-[#1D4069]">
                        VENTI<span class="text-[#F07F22]">Q</span>
                    </span>
                    <span class="text-[8px] font-bold tracking-[0.2em] text-gray-400 uppercase">
                        Intelligence
                    </span>
                </div>
            </a>

            <div class="flex items-center gap-4 md:gap-8">
                <a href="/pricing" class="hidden sm:block text-[10px] font-bold uppercase tracking-widest text-gray-500 hover:text-[#F07F22] transition-colors">
                    Pricing
                </a>
                <a href="{{ route('filament.admin.auth.login') }}" 
                class="px-5 py-2 rounded-xl bg-[#1D4069]/5 text-[#1D4069] text-[10px] font-bold uppercase tracking-widest hover:bg-[#1D4069] hover:text-white transition-all duration-300">
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
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                © {{ date('Y') }} VENTIQ
            </p>
            <div class="flex gap-4 text-[10px] font-bold uppercase tracking-tight text-gray-500">
                <a href="#" class="hover:text-orange-500">Terms</a>
                <a href="#" class="hover:text-orange-500">Support</a>
            </div>
        </div>
    </footer>

</body>
</html>