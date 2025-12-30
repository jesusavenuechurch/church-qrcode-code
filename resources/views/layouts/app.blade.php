<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Welcome')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    @yield('head')
</head>

<body class="min-h-screen flex flex-col bg-[#f9faf9] text-gray-900">

    <!-- Top Navigation -->
    <nav class="border-b border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

            <!-- Placeholder Identity -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-green-600
                            flex items-center justify-center text-white font-semibold text-sm">
                    ●
                </div>

                <span class="text-sm font-medium tracking-wide text-gray-700">
                    Community Access
                </span>
            </div>

            <!-- Intentionally empty -->
            <div></div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col md:flex-row
                    items-center justify-between gap-4">

            <p class="text-xs text-gray-500">
                © {{ date('Y') }} Community Access
            </p>

            <div class="flex gap-6 text-xs text-gray-500">
                <span>Privacy</span>
                <span>Terms</span>
                <span>Support</span>
            </div>
        </div>
    </footer>

</body>
</html>
