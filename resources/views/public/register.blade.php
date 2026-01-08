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
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Link -->
        <a href="{{ route('public.event', ['orgSlug' => $organization->slug, 'eventSlug' => $event->slug]) }}"
           class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Event
        </a>

        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Register for {{ $event->name }}</h2>
            <p class="text-gray-600 mb-8">Fill in your details to complete registration</p>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Registration Form -->
            <form method="POST" action="{{ route('registration.submit', ['orgSlug' => $organization->slug, 'eventSlug' => $event->slug]) }}">
                @csrf

                <!-- Selected Tier Display -->
                @if($selectedTier)
                    <div class="mb-8 bg-blue-50 border-2 border-blue-200 rounded-lg p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $selectedTier->tier_name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">Selected Ticket Tier</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-900">
                                    {{ number_format($selectedTier->price) }}
                                    <span class="text-lg text-gray-600">LSL</span>
                                </p>
                                @if($selectedTier->price == 0)
                                    <span class="text-sm text-green-600 font-semibold">FREE</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="tier_id" value="{{ $selectedTier->id }}">
                @else
                    <!-- Tier Selection -->
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Select Ticket Tier <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            @foreach($event->tiers as $tier)
                                @php
                                    $isSoldOut = $tier->quantity_available && ($tier->quantity_sold >= $tier->quantity_available);
                                @endphp
                                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors {{ $isSoldOut ? 'bg-gray-50 border-gray-200 opacity-60' : 'border-gray-300 hover:border-blue-500' }}">
                                    <input type="radio"
                                           name="tier_id"
                                           value="{{ $tier->id }}"
                                           class="mr-4"
                                           {{ $isSoldOut ? 'disabled' : '' }}
                                           {{ old('tier_id') == $tier->id ? 'checked' : '' }}
                                           required>
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900">{{ $tier->tier_name }}</div>
                                        @if($isSoldOut)
                                            <span class="text-sm text-red-600 font-semibold">SOLD OUT</span>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-gray-900">{{ number_format($tier->price) }} LSL</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Personal Information -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Your Information</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="full_name"
                                   value="{{ old('full_name') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., John Doe"
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., john@example.com"
                                   required>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-envelope text-blue-500 mr-1"></i>
                                Your ticket will be sent to this email
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-4 py-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-700 font-medium">
                                    +266
                                </span>
                                <input type="tel"
                                       name="phone"
                                       id="phone_input"
                                       value="{{ old('phone') }}"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="5949 4756"
                                       pattern="[0-9]{4} [0-9]{4}"
                                       maxlength="9"
                                       required>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                Enter 8-digit phone number without country code
                            </p>
                        </div>

                        <!-- WhatsApp Opt-In (Optional Enhancement) -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-xl p-5">
                            <div class="flex items-start mb-4">
                                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                    <i class="fa-brands fa-whatsapp text-white text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-900 mb-1 text-lg">
                                        Get Your Ticket on WhatsApp! 🚀
                                    </h4>
                                    <p class="text-sm text-gray-700">
                                        Receive instant notifications and easy access to your ticket via WhatsApp
                                    </p>
                                </div>
                            </div>

                            <!-- WhatsApp Opt-In Checkbox -->
                            <label class="flex items-start p-4 bg-white border-2 border-green-200 rounded-lg cursor-pointer hover:bg-green-50 transition-all">
                                <input type="checkbox" 
                                       name="has_whatsapp" 
                                       id="has_whatsapp_checkbox"
                                       value="1" 
                                       {{ old('has_whatsapp') ? 'checked' : '' }}
                                       class="mt-1 mr-3 w-5 h-5 text-green-600 rounded focus:ring-green-500"
                                       onchange="toggleWhatsAppConfirmation()">
                                <div class="flex-1">
                                    <span class="font-semibold text-gray-900">
                                        Yes, send my ticket via WhatsApp too
                                    </span>
                                    <p class="text-sm text-gray-600 mt-1">
                                        ✅ Instant delivery when payment is approved<br>
                                        ✅ Easy access from your phone<br>
                                        ✅ You'll still get email confirmation
                                    </p>
                                </div>
                            </label>

                            <!-- WhatsApp Confirmation (shows when checkbox is checked) -->
                            <div id="whatsapp-confirmation" class="mt-3 hidden">
                                <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                                    <p class="text-sm text-yellow-900 mb-2">
                                        <i class="fas fa-check-circle text-green-600 mr-1"></i>
                                        <strong>WhatsApp enabled for <span id="phone-display-confirm">+266</span></strong>
                                    </p>
                                    <p class="text-xs text-yellow-800">
                                        After payment approval, you'll receive instructions to get your ticket on WhatsApp. 
                                        You'll send us a simple message and we'll reply with your ticket instantly!
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden field for backend -->
                        <input type="hidden" name="preferred_delivery" id="preferred_delivery_input" value="{{ old('has_whatsapp') ? 'both' : 'email' }}">
                    </div>
                </div>

                <!-- Group/Companion Registration -->
                @if($selectedTier && $selectedTier->quantity_per_purchase > 1)
                <div class="mb-8 bg-blue-50 border-2 border-blue-200 rounded-lg p-6">
                    <div class="flex items-start mb-4">
                        <i class="fas fa-users text-blue-600 text-2xl mr-3"></i>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-1">
                                Additional Attendees ({{ $selectedTier->quantity_per_purchase - 1 }} more)
                            </h3>
                            <p class="text-sm text-blue-800">
                                This ticket covers {{ $selectedTier->quantity_per_purchase }} people.
                                Please provide information for each attendee.
                            </p>
                        </div>
                    </div>

                    @for($i = 2; $i <= $selectedTier->quantity_per_purchase; $i++)
                    <div class="bg-white rounded-lg border border-blue-200 p-4 mb-4">
                        <h4 class="font-semibold text-gray-900 mb-3">
                            <i class="fas fa-user mr-2 text-blue-600"></i>
                            Person {{ $i }} of {{ $selectedTier->quantity_per_purchase }}
                        </h4>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="companion_{{ $i }}_name"
                                       value="{{ old('companion_' . $i . '_name') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="e.g., Jane Smith"
                                       required>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number <span class="text-gray-500">(Optional)</span>
                                    </label>
                                    <div class="flex">
                                        <span class="inline-flex items-center px-3 py-2 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-700 text-sm">
                                            +266
                                        </span>
                                        <input type="tel"
                                               name="companion_{{ $i }}_phone"
                                               value="{{ old('companion_' . $i . '_phone') }}"
                                               class="flex-1 px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm companion-phone"
                                               placeholder="5949 4756"
                                               pattern="[0-9]{4} [0-9]{4}"
                                               maxlength="9">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-gray-500">(Optional)</span>
                                    </label>
                                    <input type="email"
                                           name="companion_{{ $i }}_email"
                                           value="{{ old('companion_' . $i . '_email') }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                           placeholder="jane@example.com">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-4">
                        <p class="text-xs text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Note:</strong> Each person will receive their own ticket with a unique QR code for event entry.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Free Ticket Notice -->
                @if($selectedTier && $selectedTier->price == 0)
                <div class="mb-8">
                    <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6 text-center">
                        <div class="text-green-600 text-5xl mb-3">🎉</div>
                        <h3 class="text-xl font-bold text-green-900 mb-2">This is a Free Ticket!</h3>
                        <p class="text-green-700">No payment required. Just complete your registration below.</p>
                    </div>
                </div>
                @endif

                <!-- Payment Options -->
                @if($selectedTier && $selectedTier->price > 0)
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Payment Options</h3>

                    @if($paymentMethods->isNotEmpty())

                        <!-- STEP 1: Choose Payment Type (Full or Installment) -->
                        @if($event->allow_installments)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                How would you like to pay? <span class="text-red-500">*</span>
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Full Payment Option -->
                                <label class="payment-type-card cursor-pointer">
                                    <input type="radio"
                                           name="payment_type"
                                           value="full"
                                           class="peer sr-only"
                                           {{ old('payment_type', 'full') == 'full' ? 'checked' : '' }}
                                           required>

                                    <div class="relative p-5 border-2 border-gray-300 rounded-lg peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-gray-400 transition-all h-full">
                                        <div class="absolute top-3 right-3 hidden peer-checked:block">
                                            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                        </div>

                                        <div class="flex items-start mb-3">
                                            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0">
                                                <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 mb-1">Pay Full Amount</div>
                                                <div class="text-2xl font-bold text-blue-600">
                                                    {{ number_format($selectedTier->price) }} <span class="text-sm text-gray-600">LSL</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                                            <p class="text-xs text-gray-700">
                                                ✅ Instant ticket activation<br>
                                                ✅ No follow-up needed<br>
                                                ✅ Full access immediately
                                            </p>
                                        </div>
                                    </div>
                                </label>

                                <!-- Installment Option -->
                                <label class="payment-type-card cursor-pointer">
                                    <input type="radio"
                                           name="payment_type"
                                           value="deposit"
                                           class="peer sr-only"
                                           {{ old('payment_type') == 'deposit' ? 'checked' : '' }}>

                                    <div class="relative p-5 border-2 border-gray-300 rounded-lg peer-checked:border-green-600 peer-checked:bg-green-50 hover:border-gray-400 transition-all h-full">
                                        <div class="absolute top-3 right-3 hidden peer-checked:block">
                                            <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                        </div>

                                        <div class="flex items-start mb-3">
                                            <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center mr-3 flex-shrink-0">
                                                <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 mb-1">Pay Installments</div>
                                                <div class="text-2xl font-bold text-green-600">
                                                    {{ number_format($selectedTier->price * ($event->minimum_deposit_percentage / 100)) }} <span class="text-sm text-gray-600">LSL+</span>
                                                </div>
                                                <p class="text-xs text-gray-500">Minimum deposit</p>
                                            </div>
                                        </div>

                                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                                            <p class="text-xs text-gray-700">
                                                💰 Pay {{ number_format($event->minimum_deposit_percentage, 0) }}% now<br>
                                                📅 Complete before event<br>
                                                🔄 Flexible payment schedule
                                            </p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Custom Deposit Amount -->
                        <div id="deposit-amount-section" class="mb-6 hidden">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-3">
                                <p class="text-sm text-green-900 font-semibold mb-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Choose Your Initial Payment
                                </p>
                                <p class="text-xs text-green-800">
                                    Minimum: <strong>{{ number_format($selectedTier->price * ($event->minimum_deposit_percentage / 100), 2) }} LSL</strong>
                                    ({{ number_format($event->minimum_deposit_percentage, 0) }}% of {{ number_format($selectedTier->price) }} LSL)
                                </p>
                            </div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Initial Payment Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-600 font-semibold">LSL</span>
                                <input type="number"
                                       name="deposit_amount"
                                       id="deposit_amount"
                                       step="0.01"
                                       min="{{ $selectedTier->price * ($event->minimum_deposit_percentage / 100) }}"
                                       max="{{ $selectedTier->price }}"
                                       value="{{ old('deposit_amount', $selectedTier->price * ($event->minimum_deposit_percentage / 100)) }}"
                                       class="w-full pl-14 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-lg font-bold"
                                       placeholder="{{ number_format($selectedTier->price * ($event->minimum_deposit_percentage / 100), 2) }}">
                            </div>

                            <!-- Amount Suggestions -->
                            <div class="flex gap-2 mt-2">
                                @php
                                    $minDeposit = $selectedTier->price * ($event->minimum_deposit_percentage / 100);
                                    $halfAmount = $selectedTier->price / 2;
                                    $fullAmount = $selectedTier->price;
                                @endphp
                                <button type="button" onclick="setDepositAmount({{ $minDeposit }})"
                                        class="text-xs px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-full">
                                    Min ({{ number_format($minDeposit) }})
                                </button>
                                <button type="button" onclick="setDepositAmount({{ $halfAmount }})"
                                        class="text-xs px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-full">
                                    Half ({{ number_format($halfAmount) }})
                                </button>
                                <button type="button" onclick="setDepositAmount({{ $fullAmount }})"
                                        class="text-xs px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-full">
                                    Full ({{ number_format($fullAmount) }})
                                </button>
                            </div>
                        </div>
                        @else
                            <input type="hidden" name="payment_type" value="full">
                        @endif

                        <!-- STEP 2: Select Payment Method -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Select Payment Method <span class="text-red-500">*</span>
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($paymentMethods as $method)
                                    @php
                                        $config = config('constants.payment_methods.' . $method->payment_method, []);
                                        $icon = $config['icon'] ?? 'fa-money-bill';
                                        $color = $config['color'] ?? 'text-gray-600';
                                        $label = $config['label'] ?? ucfirst($method->payment_method);
                                    @endphp

                                    <label class="payment-card cursor-pointer">
                                        <input type="radio"
                                               name="payment_method_id"
                                               value="{{ $method->id }}"
                                               class="peer sr-only"
                                               data-instructions="{{ $method->instructions }}"
                                               data-is-cash="{{ $method->payment_method === 'cash' ? 'true' : 'false' }}"
                                               {{ old('payment_method_id') == $method->id ? 'checked' : '' }}
                                               required>

                                        <div class="relative p-4 border-2 border-gray-300 rounded-lg peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-gray-400 transition-all h-full">
                                            <div class="absolute top-3 right-3 hidden peer-checked:block">
                                                <div class="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                </div>
                                            </div>

                                            <div class="flex items-center mb-3 pr-8">
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center mr-3 flex-shrink-0">
                                                    <i class="fas {{ $icon }} {{ $color }} text-xl"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-semibold text-gray-900 text-sm">{{ $label }}</div>
                                                    @if($method->account_name)
                                                        <div class="text-xs text-gray-500 truncate">{{ $method->account_name }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($method->payment_method !== 'cash' && $method->account_number)
                                                <div class="bg-white border border-gray-200 rounded px-3 py-2">
                                                    <p class="text-xs text-gray-500 mb-1">
                                                        {{ $config['account_label'] ?? 'Send to' }}
                                                    </p>
                                                    <p class="text-sm font-bold text-gray-900 font-mono break-all leading-tight">
                                                        {{ $method->account_number }}
                                                    </p>
                                                </div>
                                            @else
                                                <div class="bg-gray-50 border border-gray-200 rounded px-3 py-2">
                                                    <p class="text-xs text-gray-600 text-center">
                                                        Pay in person
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Payment Instructions -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 hidden mb-6" id="payment-instructions">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 text-lg mr-3 mt-0.5 flex-shrink-0"></i>
                                <div class="flex-1">
                                    <p class="font-semibold text-blue-900 mb-1">Payment Instructions</p>
                                    <p class="text-sm text-blue-800" id="instruction-text"></p>
                                    <function_calls>
<invoke name="artifacts">
<parameter name="command">update</parameter>
<parameter name="id">complete_registration_form</parameter>
<parameter name="old_str">                                    <p class="text-sm text-blue-800" id="instruction-text"></p></parameter>
<parameter name="new_str">                                    <p class="text-sm text-blue-800" id="instruction-text"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Reference -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Reference <span class="text-gray-500">(Optional)</span>
                            </label>
                            <input type="text"
                                   name="payment_reference"
                                   value="{{ old('payment_reference') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Transaction ID or Receipt Number">
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                                If you've already paid, enter your transaction or receipt reference
                            </p>
                        </div>

                    @else
                        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mr-3"></i>
                                <div>
                                    <h4 class="font-bold text-yellow-900 mb-1">Payment Methods Not Configured</h4>
                                    <p class="text-sm text-yellow-800">
                                        The organization hasn't set up payment methods yet. Please contact them.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @else
                    <input type="hidden" name="payment_method_id" value="">
                    <input type="hidden" name="payment_type" value="full">
                @endif

                <!-- Terms & Conditions -->
                <div class="mb-8">
                    <label class="flex items-start">
                        <input type="checkbox" name="terms" class="mt-1 mr-3" required>
                        <span class="text-sm text-gray-700">
                            I agree to the terms and conditions
                            @if($selectedTier && $selectedTier->price > 0)
                                and understand that my registration is subject to payment verification and approval by the event organizers
                            @endif
                            .
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('public.event', ['orgSlug' => $organization->slug, 'eventSlug' => $event->slug]) }}"
                       class="text-gray-600 hover:text-gray-800">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors">
                        Complete Registration
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