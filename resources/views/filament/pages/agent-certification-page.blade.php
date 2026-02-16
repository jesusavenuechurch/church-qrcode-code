<x-filament-panels::page>
<div>
    @php
        $cert = $this->getCertification();
        $alreadyPassed = $cert?->passed;
    @endphp

    {{-- ===== ALREADY CERTIFIED ===== --}}
    @if($alreadyPassed)
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="text-6xl mb-4">🎓</div>
            <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">
                You're Certified
            </h2>
            <p class="text-sm text-gray-500 font-medium mb-1">
                Score: {{ $cert->score }}/{{ $cert->total_questions }} ({{ $cert->score_percentage }}%)
            </p>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-8">
                Completed {{ $cert->completed_at->format('M j, Y') }}
            </p>
            <div class="bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 rounded-2xl px-8 py-5 max-w-sm w-full">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Your Referral Link</p>
                @php $agent = $this->getAgent(); @endphp
                <p class="text-sm font-bold text-indigo-600 break-all">{{ $agent?->registration_url }}</p>
            </div>
        </div>

    {{-- ===== FAILED — show score and allow retake ===== --}}
    @elseif($cert && !$alreadyPassed && $cert->attempts > 0)
        <div class="flex flex-col items-center py-8 text-center mb-8">
            <div class="text-4xl mb-3">📋</div>
            <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-1">
                Score: {{ $cert->score }}/{{ $cert->total_questions }} ({{ $cert->score_percentage }}%)
            </h2>
            <p class="text-sm text-gray-500 font-medium">75% required to pass. Review the modules below and try again.</p>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Attempt {{ $cert->attempts }} of unlimited</p>
        </div>
    @endif

    {{-- ===== COURSE — always shown unless already passed ===== --}}
    @if(!$alreadyPassed)
    <form wire:submit.prevent="submit" class="space-y-8">

        {{-- ===========================
             MODULE 1
        =========================== --}}
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] border border-gray-100 dark:border-white/10 overflow-hidden shadow-sm">
            <div class="h-1 bg-gradient-to-r from-[#1D4069] to-indigo-400"></div>
            <div class="p-8">
                <div class="flex items-center gap-3 mb-5">
                    <span class="px-3 py-1 bg-[#1D4069] text-white text-[10px] font-black uppercase tracking-widest rounded-full">Module 1</span>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">What is VENTIQ?</h2>
                </div>

                <div class="prose prose-sm dark:prose-invert max-w-none mb-8 text-gray-600 dark:text-gray-300 leading-relaxed space-y-3">
                    <p>VENTIQ is an <strong>Event Access & Participation Intelligence</strong> platform.</p>
                    <p>It helps organizations:</p>
                    <ul class="list-none space-y-1 pl-2">
                        <li>• Manage event registrations</li>
                        <li>• Control event access</li>
                        <li>• Track attendance</li>
                        <li>• Capture structured event data</li>
                    </ul>
                    <p>VENTIQ is <strong>not limited to entertainment events</strong>. It is built for workshops, corporate trainings, seminars, private events, and public events.</p>
                    <p>Organizations purchase <strong>packages per event</strong> — not monthly subscriptions.</p>
                </div>

                <div class="space-y-6">
                    {{-- Q1 --}}
                    <div>
                        <p class="text-sm font-black text-gray-800 dark:text-white mb-3">1. VENTIQ operates on:</p>
                        <div class="space-y-2">
                            @foreach(['A' => 'Monthly subscriptions', 'B' => 'Per-event packages'] as $val => $label)
                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                {{ $q1 === $val ? 'border-[#1D4069] bg-[#1D4069]/5' : 'border-gray-100 dark:border-white/10 hover:border-gray-200' }}">
                                <input type="radio" wire:model="q1" value="{{ $val }}" class="w-4 h-4 text-[#1D4069]">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $val }}) {{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Q2 --}}
                    <div>
                        <p class="text-sm font-black text-gray-800 dark:text-white mb-3">2. VENTIQ is designed for:</p>
                        <div class="space-y-2">
                            @foreach(['A' => 'Only concerts', 'B' => 'Corporate, private & entertainment events'] as $val => $label)
                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                {{ $q2 === $val ? 'border-[#1D4069] bg-[#1D4069]/5' : 'border-gray-100 dark:border-white/10 hover:border-gray-200' }}">
                                <input type="radio" wire:model="q2" value="{{ $val }}" class="w-4 h-4 text-[#1D4069]">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $val }}) {{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Q3 --}}
                    <div>
                        <p class="text-sm font-black text-gray-800 dark:text-white mb-3">3. What is VENTIQ primarily focused on?</p>
                        <div class="space-y-2">
                            @foreach(['A' => 'Selling tickets only', 'B' => 'Event access, registration & participation tracking'] as $val => $label)
                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                {{ $q3 === $val ? 'border-[#1D4069] bg-[#1D4069]/5' : 'border-gray-100 dark:border-white/10 hover:border-gray-200' }}">
                                <input type="radio" wire:model="q3" value="{{ $val }}" class="w-4 h-4 text-[#1D4069]">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $val }}) {{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===========================
             MODULE 2
        =========================== --}}
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] border border-gray-100 dark:border-white/10 overflow-hidden shadow-sm">
            <div class="h-1 bg-gradient-to-r from-indigo-400 to-purple-400"></div>
            <div class="p-8">
                <div class="flex items-center gap-3 mb-5">
                    <span class="px-3 py-1 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-full">Module 2</span>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Who You Should Target</h2>
                </div>

                <div class="prose prose-sm dark:prose-invert max-w-none mb-8 text-gray-600 dark:text-gray-300 leading-relaxed space-y-3">
                    <p>VENTIQ works best with organizations that host structured events, need attendance tracking, want better access control, and require event data reporting.</p>
                    <p><strong>Primary Targets:</strong> Corporate HR departments, NGOs, Training institutions, Universities, Conference organizers.</p>
                    <p><strong>Secondary:</strong> Entertainment promoters.</p>
                    <p>Focus on organizations that host <strong>recurring or structured events</strong>.</p>
                </div>

                <div class="space-y-6">
                    {{-- Q4 --}}
                    <div>
                        <p class="text-sm font-black text-gray-800 dark:text-white mb-3">4. Which is a strong primary target?</p>
                        <div class="space-y-2">
                            @foreach(['A' => 'Corporate HR department', 'B' => 'Random social gatherings'] as $val => $label)
                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                {{ $q4 === $val ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-100 dark:border-white/10 hover:border-gray-200' }}">
                                <input type="radio" wire:model="q4" value="{{ $val }}" class="w-4 h-4 text-indigo-600">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $val }}) {{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Q5 --}}
                    <div>
                        <p class="text-sm font-black text-gray-800 dark:text-white mb-3">5. Why is targeting structured organizations important?</p>
                        <div class="space-y-2">
                            @foreach(['A' => 'They are more likely to convert to paid packages', 'B' => "It doesn't matter who you target"] as $val => $label)
                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                {{ $q5 === $val ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-100 dark:border-white/10 hover:border-gray-200' }}">
                                <input type="radio" wire:model="q5" value="{{ $val }}" class="w-4 h-4 text-indigo-600">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $val }}) {{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===========================
             MODULE 3
        =========================== --}}
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] border border-gray-100 dark:border-white/10 overflow-hidden shadow-sm">
            <div class="h-1 bg-gradient-to-r from-purple-400 to-[#F07F22]"></div>
            <div class="p-8">
                <div class="flex items-center gap-3 mb-5">
                    <span class="px-3 py-1 bg-[#F07F22] text-white text-[10px] font-black uppercase tracking-widest rounded-full">Module 3</span>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Commission & Bonus Structure</h2>
                </div>

                <div class="prose prose-sm dark:prose-invert max-w-none mb-6 text-gray-600 dark:text-gray-300 leading-relaxed space-y-3">
                    <p>You earn <strong>20% commission</strong> once per organization's first paid package.</p>
                    <p>Commission applies only to new organizations registered through your unique referral link.</p>
                    <p>Payments are processed at the end of each month.</p>
                    <p><strong>Milestone Bonuses:</strong> 5 Paying Orgs → M300 &nbsp;|&nbsp; 10 Total → Additional M320.</p>
                    <p>Commission is <strong>not recurring</strong>.</p>
                </div>

                {{-- Commission breakdown --}}
                <div class="grid grid-cols-3 gap-3 mb-8">
                    @foreach([['Starter', 'M250', 'M50'], ['Growth', 'M700', 'M140'], ['Pro', 'M1500', 'M300']] as $tier)
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800 rounded-2xl p-4 text-center">
                        <p class="text-[10px] font-black text-orange-400 uppercase tracking-widest">{{ $tier[0] }}</p>
                        <p class="text-xs text-gray-400 line-through mt-1">{{ $tier[1] }}</p>
                        <p class="text-xl font-black text-[#F07F22] mt-1">{{ $tier[2] }}</p>
                        <p class="text-[10px] text-gray-400 font-bold mt-0.5">You earn</p>
                    </div>
                    @endforeach
                </div>

                <div class="space-y-6">
                    {{-- Q6 --}}
                    <div>
                        <p class="text-sm font-black text-gray-800 dark:text-white mb-3">6. When do you earn commission?</p>
                        <div class="space-y-2">
                            @foreach(['A' => 'Every time the organization hosts an event', 'B' => "Once on their first paid package"] as $val => $label)
                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                {{ $q6 === $val ? 'border-[#F07F22] bg-orange-50 dark:bg-orange-900/20' : 'border-gray-100 dark:border-white/10 hover:border-gray-200' }}">
                                <input type="radio" wire:model="q6" value="{{ $val }}" class="w-4 h-4 text-[#F07F22]">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $val }}) {{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Q7 --}}
                    <div>
                        <p class="text-sm font-black text-gray-800 dark:text-white mb-3">7. Commission is paid:</p>
                        <div class="space-y-2">
                            @foreach(['A' => 'Immediately after registration', 'B' => 'After payment verification and month-end processing'] as $val => $label)
                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                {{ $q7 === $val ? 'border-[#F07F22] bg-orange-50 dark:bg-orange-900/20' : 'border-gray-100 dark:border-white/10 hover:border-gray-200' }}">
                                <input type="radio" wire:model="q7" value="{{ $val }}" class="w-4 h-4 text-[#F07F22]">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $val }}) {{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Q8 --}}
                    <div>
                        <p class="text-sm font-black text-gray-800 dark:text-white mb-3">8. Does commission recur monthly?</p>
                        <div class="space-y-2">
                            @foreach(['A' => 'Yes', 'B' => 'No'] as $val => $label)
                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all
                                {{ $q8 === $val ? 'border-[#F07F22] bg-orange-50 dark:bg-orange-900/20' : 'border-gray-100 dark:border-white/10 hover:border-gray-200' }}">
                                <input type="radio" wire:model="q8" value="{{ $val }}" class="w-4 h-4 text-[#F07F22]">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ $val }}) {{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===========================
             MODULE 4
        =========================== --}}
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] border border-gray-100 dark:border-white/10 overflow-hidden shadow-sm">
            <div class="h-1 bg-gradient-to-r from-[#F07F22] to-emerald-400"></div>
            <div class="p-8">
                <div class="flex items-center gap-3 mb-5">
                    <span class="px-3 py-1 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-full">Module 4</span>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">The 30-Second Pitch</h2>
                </div>

                <div class="prose prose-sm dark:prose-invert max-w-none mb-8 text-gray-600 dark:text-gray-300 leading-relaxed space-y-3">
                    <div class="bg-gray-50 dark:bg-white/5 border-l-4 border-[#1D4069] rounded-r-2xl p-5 italic font-medium text-gray-700 dark:text-gray-200">
                        "We help organizations manage event access, registrations, and attendance tracking without manual lists or traditional ticket systems."
                    </div>
                    <ul class="list-none space-y-1 pl-2 not-italic">
                        <li>✅ Keep it simple</li>
                        <li>✅ Do not overpromise</li>
                        <li>✅ Do not modify pricing</li>
                        <li>✅ Do not misrepresent commission</li>
                    </ul>
                    <p>Your role is to <strong>introduce organizations to VENTIQ</strong>.</p>
                </div>
            </div>
        </div>

        {{-- ===========================
             FINAL ACKNOWLEDGMENT
        =========================== --}}
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] border-2 border-[#1D4069]/20 overflow-hidden shadow-sm">
            <div class="h-1 bg-[#1D4069]"></div>
            <div class="p-8">
                <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">Final Acknowledgment</h2>
                <p class="text-sm text-gray-500 font-medium mb-6">You must confirm all four statements to submit.</p>

                <div class="space-y-3 mb-8">
                    @foreach([
                        ['ack1', 'I understand commission is paid once per organization\'s first paid package.'],
                        ['ack2', 'I understand commission applies only to organizations registered through my referral link.'],
                        ['ack3', 'I understand VENTIQ reserves the right to audit suspicious referrals.'],
                        ['ack4', 'I agree to represent VENTIQ professionally.'],
                    ] as [$field, $text])
                    <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all
                        {{ $this->$field ? 'border-[#1D4069] bg-[#1D4069]/5' : 'border-gray-100 dark:border-white/10 hover:border-gray-200' }}">
                        <input type="checkbox" wire:model="{{ $field }}" class="mt-0.5 w-5 h-5 text-[#1D4069] rounded">
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200 leading-snug">{{ $text }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Submit --}}
                <button type="submit"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-not-allowed"
                        class="w-full py-5 rounded-2xl bg-[#1D4069] hover:bg-[#F07F22] text-white font-black text-xs uppercase tracking-[0.2em] shadow-xl transition-all">
                    <span wire:loading.remove>Submit & Get Certified</span>
                    <span wire:loading>Grading...</span>
                </button>

                <p class="text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-4">
                    75% pass rate required · {{ count($this->getAnswerKey()) }} questions total
                </p>
            </div>
        </div>

    </form>
    @endif

</div>
</x-filament-panels::page>