@extends('layouts.app')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center relative p-4 bg-gray-50/50" 
     x-data="setupForm()">
    
    <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[10%] left-[20%] w-96 h-96 bg-[#1D4069]/5 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[10%] right-[20%] w-96 h-96 bg-[#F07F22]/5 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl shadow-blue-900/5 overflow-hidden border border-gray-100 relative transition-all duration-500"
         :class="step === 3 ? 'max-w-md' : 'max-w-2xl'">
        
        <div class="absolute top-0 left-0 h-1 bg-gray-100 w-full">
            <div class="h-full bg-gradient-to-r from-[#1D4069] to-[#F07F22] transition-all duration-500 ease-out"
                 :style="'width: ' + ((step / 3) * 100) + '%'"></div>
        </div>

        <div class="px-8 pt-8 pb-4 text-center">
            <h2 class="text-2xl font-extrabold text-[#1D4069] tracking-tight mb-1" x-text="stepTitle"></h2>
            <p class="text-sm text-gray-500" x-text="stepDescription"></p>
        </div>

        <form action="#" method="POST" class="p-8 pt-2">
            @csrf

            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-5">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Organization Name</label>
                        <input type="text" x-model="form.org_name" @input="generateSlug()" placeholder="e.g. Acme Events Co."
                               class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#1D4069] focus:border-[#1D4069] block p-3 transition-colors placeholder-gray-300">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">System Slug</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400 text-sm font-mono">/</span>
                            <input type="text" x-model="form.slug" placeholder="acme-events"
                                   class="w-full bg-white border border-gray-200 text-gray-600 font-mono text-sm rounded-xl focus:ring-[#F07F22] focus:border-[#F07F22] block p-3 pl-6 transition-colors">
                        </div>
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Phone</label>
                        <input type="text" x-model="form.phone" placeholder="+266 5000 0000"
                               class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#1D4069] focus:border-[#1D4069] block p-3 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">General Email</label>
                        <input type="email" x-model="form.email" placeholder="info@demo.org"
                               class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#1D4069] focus:border-[#1D4069] block p-3 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Event Contact Email</label>
                        <input type="email" x-model="form.contact_email" placeholder="events@demo.org"
                               class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#1D4069] focus:border-[#1D4069] block p-3 transition-colors">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Website URL</label>
                        <input type="url" x-model="form.website" placeholder="https://..."
                               class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#1D4069] focus:border-[#1D4069] block p-3 transition-colors">
                    </div>
                </div>
            </div>

            <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-5">
                
                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 flex items-start gap-3">
                    <div class="bg-[#1D4069] text-white p-1.5 rounded-lg mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#1D4069]">Primary Administrator</h3>
                        <p class="text-xs text-blue-900/60">This user will have full access to manage the organization.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Full Name</label>
                        <input type="text" x-model="form.admin_name" placeholder="John Doe"
                               class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#1D4069] focus:border-[#1D4069] block p-3 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Admin Email</label>
                        <input type="email" x-model="form.admin_email" placeholder="admin@demo.org"
                               class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#1D4069] focus:border-[#1D4069] block p-3 transition-colors">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Password</label>
                            <input type="password" x-model="form.password" placeholder="••••••••"
                                   class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#1D4069] focus:border-[#1D4069] block p-3 transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Confirm</label>
                            <input type="password" placeholder="••••••••"
                                   class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-[#1D4069] focus:border-[#1D4069] block p-3 transition-colors">
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="text-center py-4">
                
                <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6 text-green-500 animate-pulse">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">You're all set!</h3>
                <p class="text-sm text-gray-500 mb-8 max-w-xs mx-auto">
                    We've gathered your details. Click below to create <span class="font-bold text-[#1D4069]" x-text="form.org_name"></span> and start hosting events.
                </p>

                <div class="bg-gray-50 p-4 rounded-xl text-left text-xs text-gray-500 mb-6 border border-gray-100">
                    <div class="flex justify-between mb-2"><span>Organization:</span> <span class="font-bold text-gray-700" x-text="form.org_name"></span></div>
                    <div class="flex justify-between mb-2"><span>Admin:</span> <span class="font-bold text-gray-700" x-text="form.admin_email"></span></div>
                    <div class="flex justify-between"><span>Plan:</span> <span class="font-bold text-[#F07F22]">Free Trial</span></div>
                </div>
            </div>

            <div class="pt-6 mt-6 border-t border-gray-100 flex justify-between items-center">
                
                <button type="button" @click="step--" x-show="step > 1" 
                        class="text-sm font-bold text-gray-400 hover:text-[#1D4069] transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Back
                </button>
                <div x-show="step === 1"></div> <button type="button" @click="nextStep()" 
                        class="px-8 py-3 rounded-xl bg-[#1D4069] text-white font-bold text-sm shadow-lg shadow-blue-900/20 hover:shadow-blue-900/30 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-2 group">
                    <span x-text="step === 3 ? 'Create Account' : 'Continue'"></span>
                    <svg x-show="step < 3" class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>

            </div>
        </form>
    </div>
</div>

<script>
    function setupForm() {
        return {
            step: 1,
            form: {
                org_name: '',
                slug: '',
                phone: '',
                email: '',
                contact_email: '',
                website: '',
                admin_name: '',
                admin_email: '',
                password: ''
            },
            get stepTitle() {
                if(this.step === 1) return 'Setup Organization';
                if(this.step === 2) return 'Admin Access';
                return 'Ready to Launch';
            },
            get stepDescription() {
                if(this.step === 1) return 'Tell us about your company or community.';
                if(this.step === 2) return 'Create your secure root account.';
                return 'Review your details and get started.';
            },
            generateSlug() {
                // Simple slug generator: lowercase, replace spaces with dashes
                this.form.slug = this.form.org_name
                    .toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
            },
            nextStep() {
                if (this.step < 3) {
                    this.step++;
                } else {
                    // Submit form logic here
                    alert('Submitting: ' + JSON.stringify(this.form));
                }
            }
        }
    }
</script>
@endsection