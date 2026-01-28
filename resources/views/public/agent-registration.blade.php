<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Onboarding | VENTIQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .ventiq-input:focus { box-shadow: 0 0 20px rgba(79, 70, 229, 0.1); }
    </style>
</head>
<body class="bg-[#0b0f1a] text-slate-300 min-h-screen selection:bg-indigo-500">
    
    <div class="max-w-2xl mx-auto px-6 py-16">
        
        <div class="text-center mb-12 animate-in fade-in slide-in-from-top-4 duration-700">
            <div class="inline-flex items-center gap-3 px-4 py-2 bg-indigo-500/10 border border-indigo-500/20 rounded-full mb-6">
                <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-400">Invite-Only Access</span>
            </div>
            
            <h1 class="text-5xl font-black text-white tracking-tighter italic uppercase mb-4">
                VENTIQ<span class="text-indigo-500">.</span>
            </h1>
            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">
                Referral via <span class="text-white decoration-2 underline decoration-indigo-500 underline-offset-8">{{ $agent->name }}</span>
            </p>
        </div>

        <div class="bg-slate-900/50 border border-slate-800 rounded-[3rem] shadow-2xl overflow-hidden backdrop-blur-xl">
            
            @if ($errors->any())
                <div class="bg-rose-500/10 border-b border-rose-500/20 px-10 py-6 text-rose-400">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-shield-halved text-xs"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest">Configuration Errors</p>
                    </div>
                    <ul class="text-xs font-bold space-y-1 ml-6 list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('agent.registration.submit', $agent->referral_token) }}" class="p-10 sm:p-12 space-y-12">
                @csrf

                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-600/10 border border-indigo-500/20 flex items-center justify-center text-indigo-500 shadow-inner shrink-0">
                            <i class="fas fa-building text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-xs font-black text-white uppercase tracking-[0.3em]">Organization Profile</h2>
                            <p class="text-[10px] text-slate-500 font-bold uppercase mt-1 leading-tight">Identify your business entity</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div class="group">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Business Name <span class="text-indigo-500">*</span></label>
                            <input type="text" name="org_name" value="{{ old('org_name') }}" required
                                class="box-border w-full bg-slate-950/50 border-2 border-slate-800 rounded-2xl px-4 sm:px-6 py-4 focus:bg-slate-950 focus:border-indigo-500 outline-none transition-all font-bold text-white ventiq-input"
                                placeholder="e.g. SKYLINE PRODUCTIONS">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="w-full">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Official Email</label>
                                <input type="email" name="org_email" value="{{ old('org_email') }}" required
                                    class="box-border w-full bg-slate-950/50 border-2 border-slate-800 rounded-2xl px-4 sm:px-6 py-4 focus:border-indigo-500 outline-none transition-all font-bold text-white ventiq-input">
                            </div>

                            <div class="w-full">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Phone Line</label>
                                <div class="flex w-full">
                                    <span class="inline-flex items-center px-3 sm:px-4 bg-slate-800 border-2 border-r-0 border-slate-800 rounded-l-2xl font-black text-slate-500 text-[10px] sm:text-xs shrink-0">
                                        +266
                                    </span>
                                    <input type="tel" name="org_phone" value="{{ old('org_phone') }}" required
                                        class="box-border flex-1 min-w-0 bg-slate-950/50 border-2 border-slate-800 rounded-r-2xl px-4 py-4 focus:border-indigo-500 outline-none transition-all font-bold text-white mono uppercase">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Description <span class="text-slate-700 font-medium">/ Optional</span></label>
                            <textarea name="org_description" rows="3"
                                    class="box-border w-full bg-slate-950/50 border-2 border-slate-800 rounded-2xl px-4 sm:px-6 py-4 focus:border-indigo-500 outline-none transition-all font-bold text-white ventiq-input"
                                    placeholder="Briefly describe your events..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-600/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 shadow-inner">
                            <i class="fas fa-user-shield text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-xs font-black text-white uppercase tracking-[0.3em]">Master Administrator</h2>
                            <p class="text-[10px] text-slate-500 font-bold uppercase mt-1">Credentials for the primary dashboard</p>
                        </div>
                    </div>
                    
                    <div class="grid gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Full Identity</label>
                            <input type="text" name="user_name" value="{{ old('user_name') }}" required
                                   class="w-full bg-slate-950/50 border-2 border-slate-800 rounded-2xl px-6 py-4 focus:border-emerald-500 outline-none transition-all font-bold text-white ventiq-input"
                                   placeholder="Your legal name">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Personal Login Email</label>
                            <input type="email" name="user_email" value="{{ old('user_email') }}" required
                                   class="w-full bg-slate-950/50 border-2 border-slate-800 rounded-2xl px-6 py-4 focus:border-emerald-500 outline-none transition-all font-bold text-white ventiq-input">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Passkey</label>
                                <input type="password" name="user_password" required
                                       class="w-full bg-slate-950/50 border-2 border-slate-800 rounded-2xl px-6 py-4 focus:border-emerald-500 outline-none transition-all font-bold text-white">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Confirm Passkey</label>
                                <input type="password" name="user_password_confirmation" required
                                       class="w-full bg-slate-950/50 border-2 border-slate-800 rounded-2xl px-6 py-4 focus:border-emerald-500 outline-none transition-all font-bold text-white">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit"
                            class="w-full bg-indigo-600 text-white font-black py-6 rounded-[1.5rem] shadow-2xl shadow-indigo-500/20 hover:bg-indigo-500 hover:-translate-y-1 active:scale-95 transition-all uppercase tracking-[0.3em] text-sm">
                        Initialize Organization <i class="fas fa-arrow-right ml-4 opacity-50"></i>
                    </button>
                    
                    <div class="mt-8 flex items-center justify-center gap-4 text-slate-600">
                        <i class="fas fa-lock text-[10px]"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest">End-to-End Encrypted Onboarding</p>
                    </div>
                </div>
            </form>
        </div>

        <p class="text-center text-[10px] text-slate-600 mt-12 uppercase tracking-[0.2em] font-bold">
            Already registered? <a href="/admin" class="text-indigo-400 hover:text-white transition-colors ml-2 decoration-1 underline underline-offset-4">Access Console</a>
        </p>
    </div>

</body>
</html>