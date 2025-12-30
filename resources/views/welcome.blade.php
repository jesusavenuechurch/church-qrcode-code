@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="min-h-[calc(100vh-120px)] flex items-center">
    
    <div class="max-w-4xl mx-auto px-6 w-full py-12">

        <!-- Soft Introduction -->
        <div class="text-center mb-12">

            <p class="text-sm text-green-600 mb-3 font-medium">
                Welcome
            </p>

            <h1 class="text-3xl md:text-4xl font-semibold text-gray-900 leading-tight mb-6">
                You're in the right place.
            </h1>

            <p class="text-gray-600 max-w-xl mx-auto text-lg leading-relaxed">
                This space helps people register, attend, and be welcomed —
                simply and with confidence.
            </p>
        </div>

        <!-- Primary Actions - Card Style with Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto mb-10">

            <!-- Attend -->
            <div class="group rounded-2xl border border-gray-200 bg-white p-8 text-center hover:border-gray-300 hover:shadow-sm transition">

                <div class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-2">
                        I want to attend an event
                    </h2>

                    <p class="text-sm text-gray-600 leading-relaxed">
                        Find an event, register once, and receive what you need to enter.
                    </p>
                </div>

                <a href="{{ route('events.browse') }}"
                   class="inline-flex items-center justify-center w-full
                          rounded-xl bg-green-600
                          px-6 py-3 text-white font-medium
                          hover:bg-green-700 transition">
                    Browse Events
                </a>
            </div>

            <!-- Organize -->
            <div class="group rounded-2xl border border-gray-200 bg-gray-50 p-8 text-center hover:border-gray-300 hover:shadow-sm transition">

                <div class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-2">
                        I'm organizing an event
                    </h2>

                    <p class="text-sm text-gray-600 leading-relaxed">
                        Manage registrations, confirm attendance, and welcome guests.
                    </p>
                </div>

                <a href="{{ route('filament.admin.auth.login') }}"
                   class="inline-flex items-center justify-center w-full
                          rounded-xl border border-gray-300
                          px-6 py-3 text-gray-800 font-medium
                          hover:border-green-400 hover:bg-white transition">
                    Sign In
                </a>
            </div>
        </div>

        <!-- Reassurance -->
        <div class="text-center">
            <p class="text-xs text-gray-500 max-w-md mx-auto leading-relaxed">
                You don't need technical skills to use this platform.
                Each step is guided, and nothing happens without your confirmation.
            </p>
        </div>

    </div>

</div>
@endsection