<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <!-- Primary Meta Tags -->
    <title>@yield('title', 'VENTIQ | Event Tickets & Live Streaming in Lesotho')</title>
    <meta name="title" content="@yield('meta_title', 'VENTIQ | Event Tickets & Live Streaming in Lesotho')">
    <meta name="description" content="@yield('meta_description', 'Buy event tickets, register for workshops, and stream events live in Lesotho. VENTIQ - Your intelligent ticketing and event platform. Simply Connected.')">
    <meta name="keywords" content="@yield('meta_keywords', 'event tickets Lesotho, buy tickets online, event registration Lesotho, live streaming events, workshops Lesotho, conferences Maseru, event management, online ticketing, VENTIQ, event platform Lesotho')">
    <meta name="author" content="VENTIQ">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Geographic Tags -->
    <meta name="geo.region" content="LS">
    <meta name="geo.placename" content="Maseru">
    <meta name="geo.position" content="-29.3167;27.4833">
    <meta name="ICBM" content="-29.3167, 27.4833">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'VENTIQ - Event Tickets & Live Streaming in Lesotho')">
    <meta property="og:description" content="@yield('og_description', 'Professional event ticketing, registration, and high-quality streaming for Lesotho. Register, Pay, and Stream - all in one place.')">
    <meta property="og:image" content="{{ asset('images/ventiq-og-share.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="VENTIQ">
    <meta property="og:locale" content="en_LS">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('twitter_title', 'VENTIQ | Intelligent Event Ticketing')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Buy tickets, register for events, and stream live in Lesotho. Simply Connected.')">
    <meta name="twitter:image" content="{{ asset('images/ventiq-og-share.png') }}">
    
    <!-- Mobile & Theme -->
    <meta name="theme-color" content="#1D4069">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="VENTIQ">
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon_io/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    
    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Structured Data - Organization -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "VENTIQ",
        "alternateName": "VENTIQ Intelligence",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/ventiq-noback.png') }}",
        "description": "Intelligent event ticketing, registration, and live streaming platform in Lesotho",
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "LS",
            "addressLocality": "Maseru"
        },
        "sameAs": [
            "@yield('social_facebook', '')",
            "@yield('social_twitter', '')",
            "@yield('social_instagram', '')"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "Customer Service",
            "availableLanguage": ["en", "st"]
        }
    }
    </script>
    
    <!-- Structured Data - Website -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "VENTIQ",
        "url": "{{ url('/') }}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/') }}/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    @stack('structured-data')
    
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .backdrop-blur-md { transition: background-color 0.3s ease; }
    </style>
    
    @stack('styles')
</head>

<body class="h-full overflow-hidden flex flex-col bg-[#F8FAFC] text-[#1D4069]">

    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-0 focus:left-0 bg-[#1D4069] text-white px-4 py-2 z-50">
        Skip to main content
    </a>

    <nav class="flex-none border-b border-gray-100 bg-white/90 backdrop-blur-md z-50" role="navigation" aria-label="Main navigation">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group transition-transform active:scale-95" aria-label="VENTIQ Home">
                <img src="{{ asset('images/ventiq-noback.png') }}" 
                    alt="VENTIQ Logo - Event Ticketing Platform" 
                    class="h-10 md:h-12 w-auto object-contain transition-transform group-hover:scale-105"
                    width="48"
                    height="48">
                
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
                   class="px-5 py-2 rounded-xl bg-[#1D4069]/5 text-[#1D4069] text-[10px] font-bold uppercase tracking-widest hover:bg-[#1D4069] hover:text-white transition-all duration-300"
                   aria-label="Login to VENTIQ">
                    Login
                </a>
            </div>
        </div>
    </nav>

    <main id="main-content" class="flex-grow relative overflow-y-auto no-scrollbar" role="main">
        @yield('content')
    </main>

    <footer class="flex-none bg-white border-t border-gray-100 py-3 px-6" role="contentinfo">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                © {{ date('Y') }} VENTIQ - Event Ticketing Lesotho
            </p>
            <div class="flex gap-4 text-[10px] font-bold uppercase tracking-tight text-gray-500">
                <a href="/terms" class="hover:text-orange-500">Terms</a>
                <a href="/support" class="hover:text-orange-500">Support</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>