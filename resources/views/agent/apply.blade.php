<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Partnership | VENTIQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .ventiq-navy { background-color: #1D4069; }
        .ventiq-orange-text { color: #F07F22; }
        .ventiq-orange-bg { background-color: #F07F22; }
        
        /* Custom Scrollbar for Textarea */
        textarea::-webkit-scrollbar { width: 4px; }
        textarea::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900">

    <div class="min-h-screen flex flex-col items-center justify-center p-4 relative overflow-hidden" 
         x-data="{ 
            step: 1, 
            totalSteps: 3,
            loading: false,
            submitted: false,
            errorMessage: '',
            
            // Form Data
            name: '', 
            rawPhone: '', 
            email: '', 
            city: '',
            access_types: [],
            motivation: '',
            
            // Magic Phone Formatter
            get displayPhone() {
                let v = this.rawPhone.replace(/\D/g, '');
                if (v.length > 4) return v.substring(0, 4) + ' ' + v.substring(4, 8);
                return v;
            },

            nextStep() { if(this.step < this.totalSteps) this.step++ },
            prevStep() { if(this.step > 1) this.step-- },

            async submitForm() {
                if(this.motivation.length < 50) return;
                
                this.loading = true;
                this.errorMessage = '';
                
                try {
                    const response = await fetch('{{ route('agent.submit') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            name: this.name,
                            phone: '+266 ' + this.displayPhone,
                            email: this.email,
                            city_district: this.city,
                            access_types: this.access_types,
                            motivation: this.motivation
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.submitted = true;
                    } else {
                        this.errorMessage = data.errors ? Object.values(data.errors).flat().join(' ') : 'Submission failed.';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.errorMessage = 'Network error. Please check your connection.';
                } finally {
                    this.loading = false;
                }
            }
         }">
        
        <div class="absolute inset-0 -z-10 overflow-hidden">
            <div class="absolute top-[-5%] left-[-10%] w-96 h-96 bg-[#1D4069]/5 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-[5%] right-[-10%] w-96 h-96 bg-[#F07F22]/5 rounded-full blur-[100px]"></div>
        </div>

        <div class="text-center mb-8" x-show="!submitted" x-transition:leave="transition ease-in duration-200 opacity-0">
            <span class="inline-block px-3 py-1 rounded-full bg-[#1D4069]/5 text-[#1D4069] text-[10px] font-black uppercase tracking-[0.2em] mb-3">
                VENTIQ Agent Application
            </span>
            <h1 class="text-3xl md:text-4xl font-black tracking-tighter text-[#1D4069]">
                BECOME A
                        VENTI<span class="text-[#F07F22]">Q </span>
                     PARTNER
            </h1>
        </div>

        <div class="w-full max-w-xl bg-white rounded-[2.5rem] shadow-2xl shadow-blue-900/10 border border-white p-8 md:p-12 relative" x-cloak>
            
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gray-50 rounded-t-full overflow-hidden" x-show="!submitted">
                <div class="h-full bg-gradient-to-r from-[#1D4069] to-[#F07F22] transition-all duration-700 ease-out" 
                     :style="`width: ${(step / totalSteps) * 100}%`"></div>
            </div>

            <!-- Error Message -->
            <div x-show="errorMessage" x-transition class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl">
                <p class="text-xs text-red-600 font-medium" x-text="errorMessage"></p>
            </div>

            <!-- STEP 1: Identity -->
            <div x-show="step === 1 && !submitted" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" class="space-y-6">
                <div class="flex justify-between items-end">
                    <div>
                        <h2 class="text-xl font-black text-[#1D4069] tracking-tight">01. Identity</h2>
                        <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase mt-1">Basic Contact Details</p>
                    </div>
                    <span class="text-xs font-black text-gray-200">STEP 1/3</span>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">Full Name</label>
                        <input type="text" x-model="name" placeholder="Enter Your full name" 
                            class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#F07F22]/20 transition-all font-medium">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">WhatsApp Line</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 font-bold text-sm">+266</div>
                                <input type="tel" 
                                    @input="rawPhone = $event.target.value.replace(/\D/g, '').substring(0, 8)"
                                    :value="displayPhone"
                                    placeholder="5... ...." 
                                    class="w-full bg-gray-50 border-none rounded-2xl pl-16 pr-5 py-4 focus:ring-2 focus:ring-[#F07F22]/20 transition-all font-mono font-bold tracking-[0.2em] text-gray-700">
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">Email Address</label>
                            <input type="email" x-model="email" placeholder="name@domain.com" 
                                class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#F07F22]/20 transition-all font-medium">
                        </div>
                    </div>
                </div>

                <button @click="if(name && rawPhone.length === 8 && email.includes('@')) nextStep()" 
                    class="w-full py-4 rounded-2xl bg-[#1D4069] text-white font-bold text-xs uppercase tracking-[0.3em] shadow-lg hover:bg-[#1D4069]/90 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    Next: Market Access <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>

            <!-- STEP 2: Territory -->
            <div x-show="step === 2 && !submitted" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" class="space-y-6">
                <div class="flex justify-between items-end">
                    <div>
                        <h2 class="text-xl font-black text-[#1D4069] tracking-tight">02. Territory</h2>
                        <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase mt-1">Current Location</p>
                    </div>
                    <span class="text-xs font-black text-gray-200">STEP 2/3</span>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">Primary District</label>
                        <input type="text" x-model="city" placeholder="e.g. Maseru, Leribe, etc." 
                            class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-[#F07F22]/20 transition-all font-medium">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1 block">Organizations You Can Easily Reach</label>
                        <div class="grid grid-cols-2 gap-3">
                            <template x-for="type in ['Churches', 'Schools', 'Businesses', 'Event Planners']">
                                <label class="flex items-center p-4 bg-gray-50 rounded-2xl border-2 border-transparent transition-all cursor-pointer" 
                                    :class="access_types.includes(type) ? 'border-[#F07F22]/40 bg-white shadow-sm' : 'hover:bg-gray-100'">
                                    <input type="checkbox" :value="type" x-model="access_types" class="hidden">
                                    <span class="text-[10px] font-black uppercase tracking-tight" 
                                        :class="access_types.includes(type) ? 'text-[#F07F22]' : 'text-gray-400'" 
                                        x-text="type"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button @click="prevStep()" class="w-1/3 py-4 rounded-2xl bg-gray-100 text-gray-400 font-bold text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">Back</button>
                    <button @click="if(city && access_types.length) nextStep()" 
                        class="w-2/3 py-4 rounded-2xl bg-[#1D4069] text-white font-bold text-xs uppercase tracking-[0.3em] shadow-lg transition-all flex items-center justify-center gap-2">
                        Vetting <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>

            <!-- STEP 3: Intelligence -->
            <div x-show="step === 3 && !submitted" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-4" class="space-y-6">
                <div class="flex justify-between items-end">
                    <div>
                        <h2 class="text-xl font-black text-[#1D4069] tracking-tight">03. Motivation</h2>
                        <p class="text-[10px] text-gray-400 font-bold tracking-widest uppercase mt-1">Final Review</p>
                    </div>
                    <span class="text-xs font-black text-gray-200">STEP 3/3</span>
                </div>

                <div class="space-y-4">
                    <div class="p-5 bg-[#1D4069]/5 rounded-3xl border border-[#1D4069]/5">
                        <p class="text-[11px] text-[#1D4069] leading-relaxed font-bold italic uppercase tracking-tighter">
                            “VENTIQ partners help organizations move events online. Tell us how you would onboard your first few organizations.”
                        </p>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-[#1D4069] uppercase tracking-wider ml-1">Why do you want to be a VENTIQ agent?</label>
                        <textarea x-model="motivation" rows="5" 
                            class="w-full bg-gray-50 border-none rounded-[2rem] px-6 py-5 text-sm focus:ring-2 focus:ring-[#F07F22]/20 transition-all resize-none"
                            placeholder="Explain how you will onboard your first 5 organizations..."></textarea>
                        <div class="flex justify-between px-2 mt-1">
                             <span class="text-[9px] font-black uppercase tracking-tighter" :class="motivation.length >= 50 ? 'text-green-500' : 'text-gray-300'">
                                 Min. 50 characters required
                             </span>
                             <span class="text-[9px] font-mono text-gray-400" x-text="motivation.length"></span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button @click="prevStep()" class="w-1/3 py-4 rounded-2xl bg-gray-100 text-gray-400 font-bold text-[10px] uppercase tracking-widest transition-all">Back</button>
                    <button @click="submitForm()" :disabled="loading || motivation.length < 50" 
                        :class="motivation.length < 50 ? 'opacity-50 cursor-not-allowed' : 'hover:ventiq-orange-bg'"
                        class="w-2/3 py-4 rounded-2xl bg-[#1D4069] text-white font-black text-[10px] uppercase tracking-[0.3em] shadow-2xl transition-all flex items-center justify-center active:scale-[0.98]">
                        <span x-show="!loading">Send Application</span>
                        <svg x-show="loading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>

            <!-- SUCCESS STATE -->
            <div x-show="submitted" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 scale-95" class="py-12 text-center">
                <div class="w-24 h-24 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-3xl font-black text-[#1D4069] tracking-tighter uppercase">Application Submitted</h3>
                <p class="text-sm text-gray-400 mt-3 px-8 leading-relaxed">
                    Your application is being processed by the VENTIQ vetting core. We’ve sent a confirmation email to you with your application status.
                    Our team will review your submission and respond within 24–48 hours.
                </p>
                <div class="mt-10">
                    <a href="/" class="text-[10px] font-black uppercase tracking-[0.3em] text-[#F07F22] hover:text-[#1D4069] transition-colors border-b-2 border-[#F07F22]/20 pb-1">Return to Home</a>
                </div>
            </div>

        </div>

        <div class="mt-12 text-center" x-show="!submitted">
            <p class="text-[9px] text-gray-300 font-black uppercase tracking-[0.4em]">
                System Status: Active | Ventiq Intelligence Core
            </p>
        </div>
    </div>

</body>
</html>