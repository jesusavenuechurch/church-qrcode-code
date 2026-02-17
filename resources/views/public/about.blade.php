@extends('layouts.app')

@section('title', 'About VENTIQ | Scalable Infrastructure. Proven in Lesotho.')

@section('content')
<div class="bg-[#F8FAFC] text-gray-900 overflow-x-hidden" x-data="{ visible: false }" x-init="setTimeout(() => visible = true, 100)">

    {{-- ============================================================
         HERO
    ============================================================ --}}
    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">

        {{-- Background atmosphere --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-[-10%] left-[-5%] w-[600px] h-[600px] bg-[#1D4069]/6 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-5%] right-[-5%] w-[500px] h-[500px] bg-[#F07F22]/6 rounded-full blur-[120px]"></div>
            {{-- Dot grid --}}
            <div class="absolute inset-0 opacity-[0.025]" style="background-image: radial-gradient(#1D4069 1px, transparent 1px); background-size: 28px 28px;"></div>
        </div>

        <div class="relative z-10 max-w-5xl mx-auto px-6 py-32 text-center"
             :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
             style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);">

            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#1D4069]/8 border border-[#1D4069]/10 mb-8">
                <div class="w-1.5 h-1.5 rounded-full bg-[#F07F22] animate-pulse"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-[#1D4069]">Live & Operational</span>
            </div>

            <h1 class="text-5xl md:text-8xl font-black italic tracking-tighter leading-[0.88] uppercase mb-8">
                The Missing<br>
                <span class="text-[#F07F22]">Middle.</span>
            </h1>

            <p class="text-base md:text-xl text-gray-500 font-medium max-w-2xl mx-auto leading-relaxed mb-12">
                In Lesotho, informal and formal markets both default to the same manual systems. 
                VENTIQ is the calibrated infrastructure layer built to bridge that gap.
            </p>

            {{-- Scroll indicator --}}
            <div class="flex flex-col items-center gap-2 opacity-40">
                <span class="text-[9px] font-black uppercase tracking-[0.3em] text-gray-400">Scroll</span>
                <div class="w-px h-12 bg-gradient-to-b from-gray-400 to-transparent"></div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         THE STRUCTURAL OBSERVATION
    ============================================================ --}}
    <section class="bg-white border-y border-gray-100 py-20 md:py-32">
        <div class="max-w-5xl mx-auto px-6">

            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-[#F07F22] mb-4 block">01 — The Observation</span>
                    <h2 class="text-3xl md:text-5xl font-black italic uppercase tracking-tight leading-tight mb-6">
                        Same problem.<br>Opposite ends.
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-4 font-medium">
                        Informal markets are flexible but fragmented. Formal institutions are structured but still rely on manual coordination. Despite being at opposite ends of structure, both default to the same outcome — clipboards, WhatsApp groups, and spreadsheets.
                    </p>
                    <p class="text-gray-900 font-black text-sm uppercase tracking-wide">
                        That reveals a missing middle-layer. That's VENTIQ.
                    </p>
                </div>

                {{-- Contrast Visual --}}
                <div class="bg-[#F8FAFC] rounded-[2rem] border border-gray-100 p-8 shadow-sm">
                    <div class="grid grid-cols-3 gap-4 items-center">
                        {{-- Informal --}}
                        <div class="space-y-2 text-center">
                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-3">Informal</p>
                            @foreach(['Flexible', 'Manual', 'Fragmented', 'No Data'] as $item)
                            <div class="px-3 py-2 bg-amber-50 border border-amber-100 rounded-xl text-[10px] font-bold text-amber-700 uppercase tracking-tight">{{ $item }}</div>
                            @endforeach
                        </div>

                        {{-- VENTIQ Center --}}
                        <div class="text-center">
                            <div class="w-14 h-14 bg-[#1D4069] rounded-2xl flex items-center justify-center mx-auto mb-3 rotate-3">
                                <span class="text-white font-black text-xs italic">VQ</span>
                            </div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-[#1D4069]">Calibrated</p>
                            <div class="mt-3 space-y-1">
                                <div class="w-full h-px bg-gradient-to-r from-transparent via-[#F07F22] to-transparent"></div>
                                <div class="w-full h-px bg-gradient-to-r from-transparent via-[#1D4069]/30 to-transparent"></div>
                            </div>
                        </div>

                        {{-- Formal --}}
                        <div class="space-y-2 text-center">
                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-3">Formal</p>
                            @foreach(['Accountable', 'Structured', 'Oversized', 'Complex'] as $item)
                            <div class="px-3 py-2 bg-blue-50 border border-blue-100 rounded-xl text-[10px] font-bold text-blue-700 uppercase tracking-tight">{{ $item }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         THE WEDGE
    ============================================================ --}}
    <section class="py-20 md:py-32">
        <div class="max-w-5xl mx-auto px-6">
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-[#F07F22] mb-4 block text-center">02 — The Strategy</span>
            <h2 class="text-3xl md:text-5xl font-black italic uppercase tracking-tight text-center mb-16 leading-tight">
                Events are the wedge.<br>
                <span class="text-[#1D4069]">Infrastructure is the endgame.</span>
            </h2>

            {{-- Progression --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-16">
                @foreach([
                    ['01', 'Event', 'Structured access control for a single activity. One registration link. One check-in flow. One clean dataset.', '#F07F22'],
                    ['02', 'Repeat', 'Multiple events create repeatable operational structure. Organizations stop rebuilding from scratch each time.', '#1D4069'],
                    ['03', 'Infrastructure', 'Broader participation tracking. VENTIQ becomes the access layer for all organizational interaction.', '#1D4069'],
                ] as [$num, $title, $desc, $color])
                <div class="group relative bg-white rounded-[1.75rem] border border-gray-100 p-7 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-0.5" style="background: {{ $color }};"></div>
                    <p class="text-[10px] font-black uppercase tracking-widest mb-3" style="color: {{ $color }};">Step {{ $num }}</p>
                    <h3 class="text-xl font-black uppercase italic tracking-tight mb-3 text-gray-900">{{ $title }}</h3>
                    <p class="text-sm text-gray-500 font-medium leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>

            {{-- Quote --}}
            <div class="bg-[#1D4069] rounded-[2rem] p-10 md:p-14 text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 bottom-0 opacity-5" style="background-image: radial-gradient(white 1px, transparent 1px); background-size: 20px 20px;"></div>
                <p class="text-white text-xl md:text-3xl font-black italic uppercase tracking-tight leading-tight relative z-10">
                    "Scalable infrastructure<br>
                    <span class="text-[#F07F22]">being proven in Lesotho.</span>"
                </p>
                <p class="text-blue-200/60 text-xs font-bold uppercase tracking-widest mt-6 relative z-10">
                    What works here will work anywhere emerging markets face the same structural gap.
                </p>
            </div>
        </div>
    </section>

    {{-- ============================================================
         THREE LAYER SYSTEM
    ============================================================ --}}
    <section class="bg-white border-y border-gray-100 py-20 md:py-32">
        <div class="max-w-5xl mx-auto px-6">
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-[#F07F22] mb-4 block text-center">03 — The Architecture</span>
            <h2 class="text-3xl md:text-5xl font-black italic uppercase tracking-tight text-center mb-4 leading-tight">
                Three layers.<br>One ecosystem.
            </h2>
            <p class="text-center text-gray-500 font-medium mb-16 max-w-xl mx-auto">Not just software. Infrastructure. Each layer feeds the next.</p>

            <div class="space-y-4">
                @foreach([
                    ['01', 'Organization Management', 'Institutional-level tools for event creation, package tracking, payment management, and performance dashboards. The command center.', 'heroicon-o-building-office-2', '#1D4069'],
                    ['02', 'Access & Participation', 'The digital front door. Registration links, QR check-ins, attendance tracking, and structured event data. Participation becomes measurable.', 'heroicon-o-qr-code', '#F07F22'],
                    ['03', 'Agent-Driven Distribution', 'Individuals onboard organizations using unique referral links. Commissions tracked automatically. Local reach. Monitored performance. Built-in accountability.', 'heroicon-o-users', '#1D4069'],
                ] as [$num, $title, $desc, $icon, $color])
                <div class="group flex gap-6 md:gap-10 items-start p-7 md:p-9 bg-[#F8FAFC] hover:bg-white rounded-[1.75rem] border border-transparent hover:border-gray-100 hover:shadow-md transition-all duration-300">
                    <div class="flex-shrink-0 w-14 h-14 rounded-2xl flex items-center justify-center font-black text-lg text-white" style="background: {{ $color }};">
                        {{ $num }}
                    </div>
                    <div>
                        <h3 class="text-lg font-black uppercase italic tracking-tight mb-2 text-gray-900">{{ $title }}</h3>
                        <p class="text-sm text-gray-500 font-medium leading-relaxed max-w-xl">{{ $desc }}</p>
                    </div>
                    <div class="ml-auto flex-shrink-0 hidden md:flex w-8 h-8 rounded-full bg-gray-100 group-hover:bg-[#F07F22] items-center justify-center transition-colors duration-300">
                        <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================
         THE HUMAN ELEMENT
    ============================================================ --}}
    <section class="py-20 md:py-32">
        <div class="max-w-5xl mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-[#F07F22] mb-4 block">04 — Why We Built This</span>
                    <h2 class="text-3xl md:text-4xl font-black italic uppercase tracking-tight leading-tight mb-6">
                        Built in Maseru.<br>
                        <span class="text-[#1D4069]">Designed for scale.</span>
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-4 font-medium">
                        We saw the same clipboard at every structured event in Lesotho. The same WhatsApp group used as a registration system. The same exported Excel sheet that no one could find two weeks later.
                    </p>
                    <p class="text-gray-600 leading-relaxed mb-6 font-medium">
                        It wasn't a technology problem. It was a structure problem. Organizations weren't lacking ambition — they were lacking calibrated tools that matched their scale.
                    </p>
                    <p class="text-gray-900 font-black text-sm uppercase tracking-wide">
                        So we built VENTIQ. Not for Silicon Valley. For Maseru first — and wherever the same gap exists next.
                    </p>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                        ['Live', 'Platform Status', '#1D4069'],
                        ['3', 'System Layers', '#F07F22'],
                        ['20%', 'Agent Commission', '#1D4069'],
                        ['75%', 'Cert Pass Rate', '#F07F22'],
                    ] as [$val, $label, $color])
                    <div class="bg-white rounded-[1.5rem] border border-gray-100 p-6 shadow-sm text-center">
                        <p class="text-3xl md:text-4xl font-black italic mb-1" style="color: {{ $color }};">{{ $val }}</p>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $label }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         DESIGN PHILOSOPHY
    ============================================================ --}}
    <section class="bg-white border-y border-gray-100 py-20 md:py-24">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-[#F07F22] mb-4 block">05 — Design Philosophy</span>
            <h2 class="text-3xl md:text-5xl font-black italic uppercase tracking-tight mb-6 leading-tight">
                Simple at first glance.<br>
                <span class="text-[#1D4069]">High-end underneath.</span>
            </h2>
            <p class="text-gray-500 font-medium max-w-2xl mx-auto leading-relaxed mb-12">
                A user should enter VENTIQ and immediately know where to go — without training. That simplicity doesn't compromise the institutional-level power underneath. Both are intentional.
            </p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach(['Accessible', 'Professional', 'Structured', 'Scalable'] as $pillar)
                <div class="bg-[#F8FAFC] rounded-2xl py-5 px-4 border border-gray-100">
                    <p class="text-sm font-black uppercase italic tracking-tight text-gray-800">{{ $pillar }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================
         CLOSING CTA
    ============================================================ --}}
    <section class="bg-[#0D1B2A] text-white py-20 md:py-32 relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(white 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-[#F07F22]/10 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto px-6 text-center">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[#F07F22] mb-6">Structure Creates Opportunity</p>
            <h2 class="text-4xl md:text-6xl font-black italic uppercase tracking-tighter leading-tight mb-6">
                Ready to add<br>
                <span class="text-[#F07F22]">structure?</span>
            </h2>
            <p class="text-gray-400 font-medium mb-10 max-w-lg mx-auto leading-relaxed">
                Join the organizations across Lesotho replacing fragmented manual systems with calibrated digital infrastructure.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('org.register.direct') }}"
                   class="px-10 py-4 bg-[#F07F22] hover:bg-white hover:text-gray-900 text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl transition-all shadow-xl shadow-orange-900/20">
                    Start Free Trial
                </a>
                <a href="{{ route('events.browse') }}"
                   class="px-10 py-4 bg-white/10 hover:bg-white/20 text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl transition-all border border-white/10">
                    Browse Events
                </a>
            </div>
        </div>
    </section>

</div>
@endsection