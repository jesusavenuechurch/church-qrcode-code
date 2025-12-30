<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->name }} - {{ $organization->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @if($organization->logo)
                        <img src="{{ Storage::url($organization->logo) }}" alt="{{ $organization->name }}" class="h-12 w-auto">
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $organization->name }}</h1>
                        <p class="text-sm text-gray-600">{{ $organization->tagline }}</p>
                    </div>
                </div>
                <a href="{{ route('public.events', $organization->slug) }}" class="text-blue-600 hover:text-blue-800">
                    View All Events
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Event Details (Left Column) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Event Banner -->
                @if($event->banner_image)
                <div class="rounded-lg overflow-hidden shadow-lg">
                    <img src="{{ Storage::url($event->banner_image) }}" alt="{{ $event->name }}" class="w-full h-96 object-cover">
                </div>
                @endif

                <!-- Event Info Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $event->name }}</h2>
                            @if($event->tagline)
                                <p class="text-lg text-gray-600">{{ $event->tagline }}</p>
                            @endif
                        </div>
                        @if(!$canRegister)
                            <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                Registration Closed
                            </span>
                        @elseif($event->event_date && $event->event_date->isPast())
                            <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                Event Ended
                            </span>
                        @endif
                    </div>

                    <!-- Event Meta Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        @if($event->event_date)
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-calendar-alt text-blue-600 mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-600">Date & Time</p>
                                <p class="font-semibold text-gray-900">
                                    {{ $event->event_date->format('l, F j, Y') }}
                                </p>
                                <p class="text-gray-700">{{ $event->event_date->format('g:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($event->venue)
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-red-600 mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-600">Venue</p>
                                <p class="font-semibold text-gray-900">{{ $event->venue }}</p>
                                @if($event->location)
                                    <p class="text-gray-700 text-sm">{{ $event->location }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($event->capacity)
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-users text-green-600 mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-600">Capacity</p>
                                <p class="font-semibold text-gray-900">{{ number_format($event->capacity) }} attendees</p>
                            </div>
                        </div>
                        @endif

                        @if($event->registration_deadline)
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-clock text-orange-600 mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-600">Registration Deadline</p>
                                <p class="font-semibold text-gray-900">
                                    {{ $event->registration_deadline->format('M j, Y g:i A') }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Event Description -->
                    @if($event->description)
                    <div class="border-t pt-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">About This Event</h3>
                        <div class="prose max-w-none text-gray-700">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Ticket Selection (Right Column) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Select Your Ticket</h3>

                    @if($canRegister && $event->tiers->count() > 0)
                        <!-- New Registration -->
                        <div class="space-y-4 mb-6">
                            <!-- Replace the ticket tier card section (around line 139-199) in your event page blade file -->

                            @foreach($event->tiers as $tier)
                                @php
                                    $availability = $tierAvailability[$tier->id];
                                    $isSoldOut = $availability['is_sold_out'];
                                @endphp

                                <div class="border-2 {{ $isSoldOut ? 'border-gray-200 bg-gray-50' : 'border-gray-300 hover:border-blue-500' }} rounded-lg p-4 transition-colors {{ $isSoldOut ? 'opacity-60' : '' }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-bold text-lg text-gray-900">{{ $tier->tier_name }}</h4>
                                            @if($tier->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $tier->description }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-2xl font-bold text-gray-900">
                                                {{ number_format($tier->price) }}
                                                <span class="text-sm text-gray-600">LSL</span>
                                            </p>
                                            
                                            <!-- Installment Badge -->
                                            @if($event->allow_installments && $tier->price > 0)
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                        <i class="fas fa-calendar-check mr-1"></i>
                                                        Installments Available
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Installment Details -->
                                    @if($event->allow_installments && $tier->price > 0 && !$isSoldOut)
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-3">
                                            <div class="flex items-start">
                                                <i class="fas fa-info-circle text-green-600 mt-0.5 mr-2"></i>
                                                <div class="flex-1">
                                                    <p class="text-sm font-semibold text-green-900 mb-1">
                                                        Pay in Installments
                                                    </p>
                                                    <p class="text-xs text-green-800 mb-2">
                                                        Minimum deposit: <strong>{{ number_format($tier->price * ($event->minimum_deposit_percentage / 100), 2) }} LSL</strong> 
                                                        ({{ $event->minimum_deposit_percentage }}%)
                                                    </p>
                                                    @if($event->installment_instructions)
                                                        <p class="text-xs text-green-700 leading-relaxed">
                                                            {{ $event->installment_instructions }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($tier->capacity)
                                        <div class="mb-3">
                                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                <span>{{ $availability['sold'] }} sold</span>
                                                @if(!$isSoldOut)
                                                    <span>{{ $availability['available'] }} remaining</span>
                                                @else
                                                    <span class="text-red-600 font-semibold">SOLD OUT</span>
                                                @endif
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($availability['sold'] / $tier->capacity) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($tier->benefits)
                                        <div class="mt-3 space-y-1">
                                            @foreach(explode("\n", $tier->benefits) as $benefit)
                                                @if(trim($benefit))
                                                    <div class="flex items-start text-sm text-gray-700">
                                                        <i class="fas fa-check text-green-600 mr-2 mt-0.5"></i>
                                                        <span>{{ trim($benefit) }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(!$isSoldOut)
                                        <button 
                                            onclick="selectTier({{ $tier->id }}, '{{ $tier->tier_name }}', {{ $tier->price }}, {{ $event->allow_installments ? 'true' : 'false' }})"
                                            class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center"
                                        >
                                            <i class="fas fa-ticket-alt mr-2"></i>
                                            Select This Ticket
                                            @if($event->allow_installments && $tier->price > 0)
                                                <span class="ml-2 text-xs bg-blue-500 px-2 py-1 rounded">Pay Later Option</span>
                                            @endif
                                        </button>
                                    @else
                                        <button 
                                            disabled
                                            class="w-full mt-4 bg-gray-300 text-gray-600 font-semibold py-3 px-4 rounded-lg cursor-not-allowed"
                                        >
                                            Sold Out
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Divider -->
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">OR</span>
                            </div>
                        </div>

                        <!-- Already Registered - Make Payment -->
                        <div class="border-2 border-orange-200 bg-orange-50 rounded-lg p-4">
                            <div class="text-center mb-3">
                                <i class="fas fa-money-bill-wave text-orange-600 text-3xl mb-2"></i>
                                <h4 class="font-bold text-gray-900 mb-1">Already Registered?</h4>
                                <p class="text-sm text-gray-600">Make an installment payment</p>
                            </div>
                            <a href="{{ route('installment.search') }}" 
                            class="block w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors text-center">
                                <i class="fas fa-search mr-2"></i>
                                Pay Installment
                            </a>
                            <p class="text-xs text-gray-600 text-center mt-2">
                                Search by phone number & ticket number
                            </p>
                        </div>

                    @elseif(!$canRegister)
                        <div class="text-center py-8">
                            <i class="fas fa-lock text-gray-400 text-5xl mb-4"></i>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Registration Closed</h4>
                            <p class="text-gray-600">Registration for this event is no longer available.</p>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-ticket-alt text-gray-400 text-5xl mb-4"></i>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">No Tickets Available</h4>
                            <p class="text-gray-600">Tickets for this event have not been set up yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} {{ $organization->name }}. All rights reserved.</p>
                @if($organization->contact_email)
                    <p class="mt-2 text-gray-400">
                        Contact: <a href="mailto:{{ $organization->contact_email }}" class="text-blue-400 hover:text-blue-300">{{ $organization->contact_email }}</a>
                    </p>
                @endif
            </div>
        </div>
    </footer>

    <script>
        function selectTier(tierId, tierName, price) {
            // Redirect to registration/checkout page with tier selected
            window.location.href = `/register/{{ $organization->slug }}/{{ $event->slug }}?tier=${tierId}`;
        }
    </script>
</body>
</html>