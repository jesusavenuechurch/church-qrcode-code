<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initialize Agent Account | VENTIQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 antialiased text-gray-900">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl border border-white p-8 md:p-12">
            <div class="text-center mb-8">
                <span class="inline-block px-3 py-1 rounded-full bg-[#1D4069]/5 text-[#1D4069] text-[10px] font-black uppercase tracking-[0.2em] mb-3">
                    Security Protocol
                </span>
                <h1 class="text-2xl font-black tracking-tighter text-gray-900 uppercase">
                    Initialize <span class="text-[#F07F22]">Console</span>
                </h1>
                <p class="text-xs text-gray-400 mt-2">Set your secure access password to begin.</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ request()->email }}">

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">New Password</label>
                    <input type="password" name="password" required 
                        class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#F07F22]/20 transition-all font-medium">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">Confirm Access Key</label>
                    <input type="password" name="password_confirmation" required 
                        class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#F07F22]/20 transition-all font-medium">
                </div>

                <button type="submit" class="w-full py-4 rounded-2xl bg-[#1D4069] text-white font-black text-[10px] uppercase tracking-[0.3em] shadow-lg hover:bg-[#F07F22] transition-all active:scale-[0.98]">
                    Activate Console Access
                </button>
            </form>
        </div>
    </div>
</body>
</html>