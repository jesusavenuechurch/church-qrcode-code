<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for {{ $event->name }} - {{ $organization->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-4">
                @if($organization->logo)
                    <img src="{{ Storage::url($organization->logo) }}" alt="{{ $organization->name }}" class="h-12 w-auto">
                @endif
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $organization->name }}</h1>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <a href="{{ route('public.event', ['orgSlug' => $organization->slug, 'eventSlug' => $event->slug]) }}"
       class="inline-flex items-center text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-blue-600 transition-all mb-8 group">
        <i class="fas fa-arrow-left mr-2 transition-transform group-hover:-translate-x-1"></i>
        Back to Event Details
    </a>

    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 overflow-hidden border border-gray-100">
        
        <div class="p-8 sm:p-10 border-b border-gray-50">
            <h2 class="text-4xl font-black text-gray-900 tracking-tight mb-2">Register</h2>
            <p class="text-gray-500 font-medium">Fill in your details to secure your spot for <span class="text-blue-600 font-bold">{{ $event->name }}</span></p>
        </div>

        @if ($errors->any() || session('error'))
            <div class="mx-8 mt-6 bg-rose-50 border border-rose-100 text-rose-800 px-6 py-4 rounded-2xl flex items-start animate-pulse">
                <i class="fas fa-exclamation-circle mt-1 mr-3 text-rose-500"></i>
                <div class="text-sm font-bold">
                    @if(session('error')) 
                        {{ session('error') }} 
                    @else
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('registration.submit', ['orgSlug' => $organization->slug, 'eventSlug' => $event->slug]) }}" class="p-8 sm:p-10">
            @csrf

            @if($selectedTier)
                <div class="mb-10 bg-gray-900 rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-xl">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-center sm:text-left">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400">Selected Ticket</span>
                            <h3 class="text-2xl font-black mt-1">{{ $selectedTier->tier_name }}</h3>
                        </div>
                        <div class="text-center sm:text-right">
                            <p class="text-3xl font-black">{{ number_format($selectedTier->price) }}<span class="text-sm ml-1 text-gray-400 uppercase">LSL</span></p>
                            @if($selectedTier->price == 0)
                                <span class="inline-block mt-1 px-3 py-0.5 bg-emerald-500/20 text-emerald-400 text-[10px] font-bold rounded-full uppercase">Complimentary</span>
                            @endif
                        </div>
                    </div>
                </div>
                <input type="hidden" name="tier_id" value="{{ $selectedTier->id }}">
            @else
                <div class="mb-10">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-1">
                        Select Ticket Tier <span class="text-rose-500">*</span>
                    </label>
                    <div class="space-y-3">
                        @foreach($event->tiers as $tier)
                            @php
                                $isSoldOut = $tier->quantity_available && ($tier->quantity_sold >= $tier->quantity_available);
                            @endphp
                            <label class="flex items-center p-5 border-2 rounded-2xl cursor-pointer transition-all {{ $isSoldOut ? 'bg-gray-50 border-gray-100 opacity-60' : 'border-slate-100 hover:border-blue-500 hover:bg-blue-50/30' }}">
                                <input type="radio" name="tier_id" value="{{ $tier->id }}" class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500" {{ $isSoldOut ? 'disabled' : '' }} {{ old('tier_id') == $tier->id ? 'checked' : '' }} required>
                                <div class="flex-1 ml-4">
                                    <div class="font-black text-gray-900 uppercase text-sm tracking-tight">{{ $tier->tier_name }}</div>
                                    @if($isSoldOut)
                                        <span class="text-[10px] font-black text-rose-600 uppercase">Sold Out</span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-xl font-black text-gray-900">{{ number_format($tier->price) }} <span class="text-xs text-gray-400">LSL</span></div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-10 space-y-8">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">Your Information</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-full">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required
                               class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl px-6 py-4 focus:bg-white focus:border-blue-600 focus:ring-0 transition-all outline-none font-bold text-gray-900"
                               placeholder="e.g., John Doe">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl px-6 py-4 focus:bg-white focus:border-blue-600 focus:ring-0 transition-all outline-none font-bold text-gray-900"
                               placeholder="john@example.com">
                        <p class="text-[10px] font-bold text-blue-500/60 uppercase mt-2 ml-1 tracking-wider">
                            <i class="fas fa-envelope mr-1"></i> Tickets are sent here
                        </p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Phone Number <span class="text-rose-500">*</span></label>
                        <div class="flex">
                            <span class="inline-flex items-center px-5 bg-slate-100 border-2 border-r-0 border-slate-100 rounded-l-2xl font-black text-gray-400 text-sm">
                                +266
                            </span>
                            <input type="tel" name="phone" id="phone_input" value="{{ old('phone') }}" required
                                   class="flex-1 bg-slate-50 border-2 border-slate-50 rounded-r-2xl px-6 py-4 focus:bg-white focus:border-blue-600 focus:ring-0 transition-all outline-none font-bold text-gray-900"
                                   placeholder="5949 4756" pattern="[0-9]{4} [0-9]{4}" maxlength="9">
                        </div>
                    </div>

                    <div class="col-span-full mt-4 bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-[2rem] p-6 sm:p-8">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm mr-4">
                                <i class="fa-brands fa-whatsapp text-emerald-500 text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-emerald-900 text-lg">WhatsApp Delivery</h4>
                                <p class="text-xs text-emerald-700 font-medium">Instant ticket access on your phone</p>
                            </div>
                        </div>

                        <label class="flex items-start p-5 bg-white/60 backdrop-blur-sm border-2 border-emerald-200 rounded-2xl cursor-pointer hover:bg-white hover:border-emerald-400 transition-all group">
                            <input type="checkbox" name="has_whatsapp" id="has_whatsapp_checkbox" value="1" {{ old('has_whatsapp') ? 'checked' : '' }}
                                   class="mt-1 w-5 h-5 text-emerald-600 rounded-lg border-emerald-300 focus:ring-emerald-500" onchange="toggleWhatsAppConfirmation()">
                            <div class="ml-4">
                                <span class="font-black text-emerald-900 text-sm uppercase">Send via WhatsApp</span>
                                <p class="text-[11px] text-emerald-600 mt-1 font-bold uppercase tracking-tight">✅ Instant delivery & easy access</p>
                            </div>
                        </label>

                        <div id="whatsapp-confirmation" class="mt-4 hidden animate-in slide-in-from-top-2">
                            <div class="bg-emerald-600 text-white rounded-xl p-3 px-5 flex items-center shadow-lg shadow-emerald-200">
                                <i class="fas fa-check-circle mr-3"></i>
                                <p class="text-[10px] font-black uppercase tracking-widest">WhatsApp Enabled for +266</p>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="preferred_delivery" id="preferred_delivery_input" value="{{ old('has_whatsapp') ? 'both' : 'email' }}">
            </div>

            @if($selectedTier && $selectedTier->quantity_per_purchase > 1)
            <div class="mb-10 space-y-8">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                        <i class="fas fa-users text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">Additional Attendees</h3>
                        <p class="text-[10px] font-bold text-indigo-500 uppercase mt-1">This ticket covers {{ $selectedTier->quantity_per_purchase }} guests</p>
                    </div>
                </div>

                <div class="space-y-6">
                    @for($i = 2; $i <= $selectedTier->quantity_per_purchase; $i++)
                    <div class="bg-slate-50 border-2 border-slate-50 rounded-[2rem] p-6 sm:p-8 relative group hover:border-indigo-100 hover:bg-white transition-all shadow-sm">
                        <div class="absolute -top-3 left-8 px-4 py-1 bg-indigo-600 text-white text-[10px] font-black rounded-full uppercase tracking-widest shadow-lg shadow-indigo-100">
                            Guest #{{ $i }}
                        </div>

                        <div class="space-y-4 mt-2">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Full Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="companion_{{ $i }}_name" value="{{ old('companion_' . $i . '_name') }}" required
                                       class="w-full bg-white border border-gray-100 rounded-xl px-5 py-3 font-bold text-gray-900 focus:border-indigo-500 outline-none transition-all"
                                       placeholder="e.g., Jane Smith">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Phone <span class="lowercase text-gray-300">(optional)</span></label>
                                    <div class="flex">
                                        <span class="inline-flex items-center px-4 bg-gray-50 border border-r-0 border-gray-100 rounded-l-xl font-bold text-gray-400 text-xs">
                                            +266
                                        </span>
                                        <input type="tel" name="companion_{{ $i }}_phone" value="{{ old('companion_' . $i . '_phone') }}"
                                               class="flex-1 bg-white border border-gray-100 rounded-r-xl px-4 py-3 text-sm font-bold focus:border-indigo-500 outline-none companion-phone"
                                               placeholder="5949 4756" pattern="[0-9]{4} [0-9]{4}" maxlength="9">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Email <span class="lowercase text-gray-300">(optional)</span></label>
                                    <input type="email" name="companion_{{ $i }}_email" value="{{ old('companion_' . $i . '_email') }}"
                                           class="w-full bg-white border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:border-indigo-500 outline-none"
                                           placeholder="jane@example.com">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                    <div class="bg-amber-50 rounded-2xl p-4 border border-amber-100 flex items-start mt-4 transition-all">
                    <i class="fas fa-info-circle text-amber-500 mt-1 mr-3"></i>
                    <p class="text-[11px] font-black text-amber-800 uppercase tracking-tight leading-relaxed">
                        Each person will receive their own ticket with a unique QR code for event entry.
                    </p>
                </div>
            </div>
            @endif

            @if($selectedTier && $selectedTier->price == 0)
            <div class="mb-10 animate-in fade-in zoom-in duration-500">
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-100 rounded-[2rem] p-8 text-center shadow-sm">
                    <div class="text-4xl mb-4">🎉</div>
                    <h3 class="text-xl font-black text-emerald-900 mb-2 uppercase tracking-tight">This is a Free Ticket!</h3>
                    <p class="text-sm font-bold text-emerald-700 uppercase tracking-widest opacity-80">No payment required. Just complete your registration below.</p>
                </div>
            </div>
            @endif

            @if($selectedTier && $selectedTier->price > 0)
            <div class="mb-10 space-y-8">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600">
                        <i class="fas fa-wallet text-sm"></i>
                    </div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">Payment Setup</h3>
                </div>

                @if($paymentMethods->isNotEmpty())

                    @if($event->allow_installments)
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Payment Plan</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="payment_type" value="full" class="peer sr-only" {{ old('payment_type', 'full') == 'full' ? 'checked' : '' }} required>
                                <div class="h-full p-6 bg-slate-50 border-2 border-slate-50 rounded-[2rem] transition-all duration-300 peer-checked:border-blue-600 peer-checked:bg-white peer-checked:shadow-xl peer-checked:shadow-blue-500/10">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600">
                                            <i class="fas fa-money-bill-wave text-xl"></i>
                                        </div>
                                        <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center scale-0 peer-checked:scale-100 transition-transform">
                                            <i class="fas fa-check text-white text-[10px]"></i>
                                        </div>
                                    </div>
                                    <h4 class="text-lg font-black text-gray-900 uppercase tracking-tight">Full Amount</h4>
                                    <p class="text-2xl font-black text-blue-600 mt-1">{{ number_format($selectedTier->price) }} <span class="text-xs text-gray-400">LSL</span></p>
                                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase"><i class="fas fa-check text-emerald-500 mr-2"></i>Instant Activation</p>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase"><i class="fas fa-check text-emerald-500 mr-2"></i>Full Access</p>
                                    </div>
                                </div>
                            </label>

                            <label class="relative cursor-pointer group">
                                <input type="radio" name="payment_type" value="deposit" class="peer sr-only" {{ old('payment_type') == 'deposit' ? 'checked' : '' }}>
                                <div class="h-full p-6 bg-slate-50 border-2 border-slate-50 rounded-[2rem] transition-all duration-300 peer-checked:border-emerald-600 peer-checked:bg-white peer-checked:shadow-xl peer-checked:shadow-emerald-500/10">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600">
                                            <i class="fas fa-calendar-check text-xl"></i>
                                        </div>
                                        <div class="w-6 h-6 bg-emerald-600 rounded-full flex items-center justify-center scale-0 peer-checked:scale-100 transition-transform">
                                            <i class="fas fa-check text-white text-[10px]"></i>
                                        </div>
                                    </div>
                                    <h4 class="text-lg font-black text-gray-900 uppercase tracking-tight">Installments</h4>
                                    <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($selectedTier->price * ($event->minimum_deposit_percentage / 100)) }} <span class="text-xs text-gray-400">LSL+</span></p>
                                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                                        <p class="text-[10px] font-bold text-gray-500 uppercase"><i class="fas fa-percent text-emerald-500 mr-2"></i>{{ number_format($event->minimum_deposit_percentage, 0) }}% Min Deposit</p>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase"><i class="fas fa-calendar-alt text-emerald-500 mr-2"></i>Flexible Schedule</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="deposit-amount-section" class="hidden animate-in slide-in-from-top-4 duration-300">
                        <div class="bg-emerald-50 border border-emerald-100 rounded-[2rem] p-8">
                            <label class="block text-[10px] font-black text-emerald-800 uppercase tracking-[0.2em] mb-4 text-center">Initial Payment Amount</label>
                            
                            <div class="relative max-w-xs mx-auto">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-emerald-400 font-black">LSL</span>
                                <input type="number" name="deposit_amount" id="deposit_amount" step="0.01"
                                       min="{{ $selectedTier->price * ($event->minimum_deposit_percentage / 100) }}"
                                       max="{{ $selectedTier->price }}"
                                       value="{{ old('deposit_amount', $selectedTier->price * ($event->minimum_deposit_percentage / 100)) }}"
                                       class="w-full pl-16 pr-6 py-5 bg-white border-2 border-emerald-200 rounded-2xl focus:border-emerald-500 outline-none text-2xl font-black text-emerald-900 shadow-inner">
                            </div>

                            <div class="flex flex-wrap justify-center gap-2 mt-6">
                                @php
                                    $minDeposit = $selectedTier->price * ($event->minimum_deposit_percentage / 100);
                                    $halfAmount = $selectedTier->price / 2;
                                    $fullAmount = $selectedTier->price;
                                @endphp
                                <button type="button" onclick="setDepositAmount({{ $minDeposit }})" class="text-[10px] font-black uppercase tracking-widest px-4 py-2 bg-white text-emerald-700 border border-emerald-200 rounded-full hover:bg-emerald-600 hover:text-white transition-all">Min</button>
                                <button type="button" onclick="setDepositAmount({{ $halfAmount }})" class="text-[10px] font-black uppercase tracking-widest px-4 py-2 bg-white text-emerald-700 border border-emerald-200 rounded-full hover:bg-emerald-600 hover:text-white transition-all">Half</button>
                                <button type="button" onclick="setDepositAmount({{ $fullAmount }})" class="text-[10px] font-black uppercase tracking-widest px-4 py-2 bg-white text-emerald-700 border border-emerald-200 rounded-full hover:bg-emerald-600 hover:text-white transition-all">Full</button>
                            </div>
                        </div>
                    </div>
                    @else
                        <input type="hidden" name="payment_type" value="full">
                    @endif

                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Payment Provider</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($paymentMethods as $method)
                                @php
                                    $config = config('constants.payment_methods.' . $method->payment_method, []);
                                    $icon = $config['icon'] ?? 'fa-money-bill';
                                    $color = $config['color'] ?? 'text-gray-600';
                                    $label = $config['label'] ?? ucfirst($method->payment_method);
                                @endphp

                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="payment_method_id" value="{{ $method->id }}" class="peer sr-only"
                                           data-instructions="{{ $method->instructions }}"
                                           data-is-cash="{{ $method->payment_method === 'cash' ? 'true' : 'false' }}"
                                           {{ old('payment_method_id') == $method->id ? 'checked' : '' }} required>
                                    
                                    <div class="p-4 border-2 border-slate-50 bg-slate-50 rounded-2xl transition-all duration-200 peer-checked:border-blue-600 peer-checked:bg-white peer-checked:shadow-lg h-full flex flex-col">
                                        <div class="flex items-center mb-3">
                                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center mr-3 shadow-sm {{ $color }}">
                                                <i class="fas {{ $icon }} text-lg"></i>
                                            </div>
                                            <span class="text-xs font-black text-gray-900 uppercase tracking-tight truncate">{{ $label }}</span>
                                        </div>

                                        @if($method->payment_method !== 'cash' && $method->account_number)
                                            <div class="mt-auto bg-gray-50 rounded-lg p-2 border border-gray-100">
                                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-1">{{ $config['account_label'] ?? 'Send to' }}</p>
                                                <p class="text-[11px] font-mono font-bold text-gray-900 break-all leading-none">{{ $method->account_number }}</p>
                                            </div>
                                        @else
                                            <div class="mt-auto py-2">
                                                <p class="text-[10px] font-bold text-gray-400 uppercase text-center italic tracking-wider">Pay in person</p>
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-blue-600 border border-blue-600 rounded-2xl p-5 hidden animate-in slide-in-from-top-2" id="payment-instructions">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-white text-lg mr-4 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-[10px] font-black text-blue-200 uppercase tracking-[0.2em] mb-1">Payment Instructions</p>
                                <p class="text-sm font-bold text-white leading-relaxed" id="instruction-text"></p>
                            </div>
                        </div>
                    </div>

                    <div class="group">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Payment Reference <span class="lowercase text-gray-300">(optional)</span></label>
                        <input type="text" name="payment_reference" value="{{ old('payment_reference') }}"
                               class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl px-6 py-4 focus:bg-white focus:border-blue-600 outline-none transition-all font-bold text-gray-900"
                               placeholder="e.g., Transaction ID or Receipt Number">
                    </div>

                @else
                    <div class="bg-amber-50 border-2 border-amber-100 rounded-[2rem] p-8 text-center">
                        <i class="fas fa-exclamation-triangle text-amber-500 text-3xl mb-4"></i>
                        <h4 class="font-black text-amber-900 uppercase tracking-tight">Payments Not Configured</h4>
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mt-2">Please contact the organizers to finalize registration.</p>
                    </div>
                @endif
            </div>
            @else
                <input type="hidden" name="payment_method_id" value="">
                <input type="hidden" name="payment_type" value="full">
            @endif

            <div class="mb-10 py-6 border-t border-gray-50">
                <label class="flex items-start cursor-pointer group">
                    <input type="checkbox" name="terms" class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer" required>
                    <span class="ml-4 text-[11px] font-bold text-gray-500 uppercase tracking-wide leading-relaxed group-hover:text-gray-700 transition-colors">
                        I agree to the terms and conditions 
                        @if($selectedTier && $selectedTier->price > 0)
                            and understand that my registration is subject to payment verification by the organizers
                        @endif
                        .
                    </span>
                </label>
            </div>

            <div class="flex flex-col-reverse sm:flex-row justify-between items-center gap-6">
                <a href="{{ route('public.event', ['orgSlug' => $organization->slug, 'eventSlug' => $event->slug]) }}"
                   class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-gray-900 transition-colors">
                    <i class="fas fa-times mr-2 text-[8px]"></i> Cancel Registration
                </a>
                
                <button type="submit"
                        class="w-full sm:w-auto px-12 py-5 bg-gray-900 text-white font-black rounded-2xl shadow-2xl hover:bg-blue-600 hover:-translate-y-1 active:scale-95 transition-all uppercase tracking-[0.2em] text-sm">
                    Complete Registration <i class="fas fa-chevron-right ml-3 text-xs opacity-50"></i>
                </button>
            </div>
        </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} {{ $organization->name }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // WhatsApp functionality
        const phoneInput = document.getElementById('phone_input');
        const phoneDisplays = document.querySelectorAll('#phone-display-confirm');
        
        // Update phone display dynamically
        phoneInput?.addEventListener('input', function(e) {
            phoneDisplays.forEach(display => {
                const value = e.target.value.trim();
                display.textContent = value ? '+266 ' + value : '+266';
            });
        });

        // Show/hide WhatsApp confirmation
        window.toggleWhatsAppConfirmation = function() {
            const checkbox = document.getElementById('has_whatsapp_checkbox');
            const confirmation = document.getElementById('whatsapp-confirmation');
            const hiddenInput = document.getElementById('preferred_delivery_input');
            
            if (checkbox && checkbox.checked) {
                confirmation?.classList.remove('hidden');
                if (hiddenInput) hiddenInput.value = 'both';
            } else {
                confirmation?.classList.add('hidden');
                if (hiddenInput) hiddenInput.value = 'email';
            }
        };

        // Initialize WhatsApp confirmation state
        toggleWhatsAppConfirmation();

        // Auto-format phone number
        phoneInput?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 8) {
                value = value.substring(0, 8);
            }
            if (value.length > 4) {
                value = value.substring(0, 4) + ' ' + value.substring(4);
            }
            e.target.value = value;
        });

        // Auto-format companion phone numbers
        document.querySelectorAll('.companion-phone').forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 8) {
                    value = value.substring(0, 8);
                }
                if (value.length > 4) {
                    value = value.substring(0, 4) + ' ' + value.substring(4);
                }
                e.target.value = value;
            });
        });

        // On form submit, clean and prepend +266 to phone
        document.querySelector('form')?.addEventListener('submit', function(e) {
            if (phoneInput) {
                let cleanPhone = phoneInput.value.replace(/\D/g, '');
                phoneInput.value = '+266' + cleanPhone;
            }
        });

        // Payment type radio functionality
        const paymentTypeRadios = document.querySelectorAll('input[name="payment_type"]');
        const depositSection = document.getElementById('deposit-amount-section');
        const depositInput = document.getElementById('deposit_amount');

        function updateDepositSection() {
            const selectedType = document.querySelector('input[name="payment_type"]:checked')?.value;
            if (selectedType === 'deposit') {
                depositSection?.classList.remove('hidden');
                if (depositInput) depositInput.required = true;
            } else {
                depositSection?.classList.add('hidden');
                if (depositInput) depositInput.required = false;
            }
        }

        paymentTypeRadios.forEach(radio => {
            radio.addEventListener('change', updateDepositSection);
        });

        updateDepositSection();

        // Payment method instructions
        const methodRadios = document.querySelectorAll('input[name="payment_method_id"]');
        const instructionsBox = document.getElementById('payment-instructions');
        const instructionText = document.getElementById('instruction-text');

        methodRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const instructions = this.dataset.instructions;
                const isCash = this.dataset.isCash === 'true';

                if (instructions) {
                    instructionText.textContent = instructions;
                    instructionsBox.classList.remove('hidden');
                } else if (isCash) {
                    instructionText.textContent = 'Pay in person at the venue or designated location. Your ticket will be activated upon payment confirmation.';
                    instructionsBox.classList.remove('hidden');
                } else {
                    instructionsBox.classList.add('hidden');
                }
            });
        });

        const checkedMethod = document.querySelector('input[name="payment_method_id"]:checked');
        if (checkedMethod) {
            checkedMethod.dispatchEvent(new Event('change'));
        }

        // Deposit amount helper function
        window.setDepositAmount = function(amount) {
            const input = document.getElementById('deposit_amount');
            if (input) input.value = amount.toFixed(2);
        };
    });
    </script>

    <style>
    .payment-card input:checked ~ div,
    .payment-type-card input:checked ~ div {
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    </style>
</body>
</html>