@extends('layouts.app')

@section('title', 'Browse Events')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12">
    
    <!-- Page Header -->
    <div class="mb-8">
        <a href="{{ url('/') }}" class="text-sm text-gray-600 hover:text-green-600 mb-4 inline-block">
            ← Back to home
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Upcoming Events</h1>
        <p class="text-gray-600">Find events near you - free or paid, all welcome</p>
    </div>

    <!-- Filters (Optional - Simple version) -->
    <div class="mb-8 flex flex-wrap gap-3">
        <button class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium">
            All Events
        </button>
        <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:border-green-400">
            Free Events
        </button>
        <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:border-green-400">
            This Week
        </button>
    </div>

    @if($events->count() > 0)
        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow border border-gray-200">
                    
                    <!-- Event Image -->
                    @if($event->banner_image)
                        <div class="h-48 overflow-hidden">
                            <img src="{{ Storage::url($event->banner_image) }}" 
                                 alt="{{ $event->name }}" 
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-br from-green-500 to-blue-600 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif

                    <!-- Event Content -->
                    <div class="p-6">
                        
                        <!-- Organization -->
                        <div class="text-xs text-green-600 font-medium mb-2">
                            {{ $event->organization->name }}
                        </div>

                        <!-- Event Name -->
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                            {{ $event->name }}
                        </h3>
                        
                        @if($event->tagline)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-1">
                                {{ $event->tagline }}
                            </p>
                        @endif

                        <!-- Event Details -->
                        <div class="space-y-2 mb-4 text-sm">
                            @if($event->event_date)
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ $event->event_date->format('M j, Y') }}</span>
                                </div>
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $event->event_date->format('g:i A') }}</span>
                                </div>
                            @endif

                            @if($event->venue)
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-4 h-4 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ $event->venue }}</span>
                                </div>
                            @endif

                            <!-- Ticket Price Range -->
                            @php
                                $minPrice = $event->tiers->min('price');
                                $maxPrice = $event->tiers->max('price');
                            @endphp
                            @if($event->tiers->count() > 0)
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                    <span>
                                        @if($minPrice == 0)
                                            <span class="text-green-600 font-semibold">Free</span>
                                        @elseif($minPrice == $maxPrice)
                                            {{ number_format($minPrice) }} LSL
                                        @else
                                            {{ number_format($minPrice) }} - {{ number_format($maxPrice) }} LSL
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- View Event Button -->
                        <a href="{{ route('public.event', ['orgSlug' => $event->organization->slug, 'eventSlug' => $event->slug]) }}" 
                           class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors">
                            View Event & Register
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- No Events -->
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Upcoming Events</h3>
            <p class="text-gray-600 mb-6">Check back soon for new events!</p>
            <a href="{{ url('/') }}" 
               class="inline-block text-green-600 hover:text-green-700 font-medium">
                ← Back to Home
            </a>
        </div>
    @endif
</div>
@endsection