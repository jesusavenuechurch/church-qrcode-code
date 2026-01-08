<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'VENTIQ')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>

<body class="h-full overflow-hidden flex flex-col bg-[#F8FAFC] text-[#1D4069]">

    <nav class="flex-none border-b border-gray-100 bg-white/80 backdrop-blur-md z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2">
                <img src="{{ asset('images/ventiq-noback.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                <span class="text-base font-bold tracking-tight uppercase text-[#1D4069]">VENTIQ</span>
            </a>

            <div class="flex items-center gap-6">
                <a href="/pricing" class="text-xs font-bold uppercase tracking-tight text-gray-500 hover:text-[#F07F22] transition-colors">
                    Pricing
                </a>
                <a href="{{ route('filament.admin.auth.login') }}" class="text-xs font-bold uppercase tracking-tight text-[#1D4069] border-l border-gray-200 pl-6 hover:text-orange-600 transition-colors">
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