<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Confirmed - {{ $event->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-4">
                @if($event->organization->logo)
                    <img src="{{ Storage::url($event->organization->logo) }}" alt="{{ $event->organization->name }}" class="h-12 w-auto">
                @endif
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $event->organization->name }}</h1>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-md p-8">
            <!-- Success Icon & Title -->
            <div class="text-center mb-8">
                <div class="mb-6">
                    @if($ticket->payment_status === 'completed')
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="fas fa-check text-green-600 text-4xl"></i>
                        </div>
                    @else
                        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto">
                            <i class="fas fa-clock text-yellow-600 text-4xl"></i>
                        </div>
                    @endif
                </div>

                @if($ticket->payment_status === 'completed')
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Registration Complete! 🎉</h2>
                    <p class="text-lg text-gray-600 mb-2">
                        @if(count($allTickets) > 1)
                            All {{ count($allTickets) }} tickets have been confirmed!
                        @else
                            Your ticket has been confirmed!
                        @endif
                    </p>
                @else
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Registration Received! ⏳</h2>
                    <p class="text-lg text-gray-600 mb-2">
                        @if(count($allTickets) > 1)
                            Registration for {{ count($allTickets) }} attendees is pending payment verification.
                        @else
                            Your registration is pending payment verification.
                        @endif
                    </p>
                @endif

                @if(count($allTickets) > 1)
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-users mr-1"></i>
                        Group registration - Each person will receive their own ticket
                    </p>
                @endif
            </div>

            <!-- All Tickets List -->
            @if(count($allTickets) > 1)
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-ticket-alt text-blue-600 mr-2"></i>
                            All Registered Attendees ({{ count($allTickets) }})
                        </h3>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $ticket->payment_status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($ticket->payment_status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($allTickets as $index => $singleTicket)
                            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-4 border-2 {{ $loop->first ? 'border-blue-300' : 'border-gray-200' }}">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            {{ $index + 1 }}
                                        </div>
                                        @if($loop->first)
                                            <span class="text-xs bg-blue-600 text-white px-2 py-1 rounded-full font-semibold">
                                                Primary
                                            </span>
                                        @else
                                            <span class="text-xs bg-gray-500 text-white px-2 py-1 rounded-full font-semibold">
                                                Companion
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="space-y-2 text-sm">
                                    <div>
                                        <p class="text-gray-600 text-xs">Name</p>
                                        <p class="font-semibold text-gray-900">{{ $singleTicket->client->full_name }}</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-gray-600 text-xs">Ticket Number</p>
                                            <p class="font-mono text-xs text-gray-900">{{ $singleTicket->ticket_number }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 text-xs">Amount</p>
                                            <p class="font-semibold text-gray-900">{{ number_format($singleTicket->amount) }} LSL</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Payment Summary for Group -->
                    <div class="mt-4 bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Amount</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ number_format($allTickets->sum('amount')) }} LSL
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Split Across</p>
                                <p class="text-2xl font-bold text-blue-600">
                                    {{ count($allTickets) }} Tickets
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Single Ticket Details -->
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-6 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900">Ticket Details</h3>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $ticket->payment_status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($ticket->payment_status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Ticket Number</p>
                            <p class="font-semibold text-gray-900">{{ $ticket->ticket_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Name</p>
                            <p class="font-semibold text-gray-900">{{ $ticket->client->full_name }}</p>
                        </div>
                        @if($ticket->client->email)
                        <div>
                            <p class="text-gray-600">Email</p>
                            <p class="font-semibold text-gray-900">{{ $ticket->client->email }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-gray-600">Phone</p>
                            <p class="font-semibold text-gray-900">{{ $ticket->client->phone }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Ticket Tier</p>
                            <p class="font-semibold text-gray-900">{{ $ticket->tier->tier_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Amount</p>
                            <p class="font-semibold text-gray-900">{{ number_format($ticket->amount) }} LSL</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ✅ PAYMENT APPROVED - Delivery Confirmation -->
            @if($ticket->payment_status === 'completed')
                <div class="mb-8">
                    <!-- Email Delivery Notice -->
                    @if($ticket->client->email)
                        <div class="bg-blue-50 border-2 border-blue-300 rounded-lg p-5 mb-4">
                            <div class="flex items-start">
                                <i class="fas fa-envelope text-blue-600 text-3xl mr-4"></i>
                                <div>
                                    <p class="font-semibold text-gray-900 text-lg mb-1">
                                        <i class="fas fa-check-circle text-green-600 mr-1"></i>
                                        Ticket Sent to Email
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        @if(count($allTickets) > 1)
                                            Each person will receive their ticket at their registered email address
                                        @else
                                            Your ticket has been sent to: <strong>{{ $ticket->client->email }}</strong>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-inbox mr-1"></i>
                                        Check your inbox (and spam folder just in case)
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- WhatsApp Delivery Notice -->
                    @if($ticket->has_whatsapp)
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-400 rounded-xl p-6 shadow-lg">
                            <div class="flex items-center mb-4">
                                <div class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center mr-4 shadow-md">
                                    <i class="fa-brands fa-whatsapp text-white text-3xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">
                                        Ticket Sent to WhatsApp! 🎫
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        Check your WhatsApp - your ticket is waiting!
                                    </p>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg p-5 border-2 border-green-200">
                                <p class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-check-double text-green-600 mr-2 text-xl"></i>
                                    Your ticket has been delivered:
                                </p>
                                <div class="space-y-2 text-sm text-gray-700">
                                    <div class="flex items-start">
                                        <i class="fas fa-envelope text-blue-600 mr-3 mt-1"></i>
                                        <div>
                                            <p class="font-semibold">Email sent</p>
                                            <p class="text-xs text-gray-600">Check your inbox for ticket download link</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <i class="fa-brands fa-whatsapp text-green-600 mr-3 mt-1 text-lg"></i>
                                        <div>
                                            <p class="font-semibold">WhatsApp sent</p>
                                            <p class="text-xs text-gray-600">Message with ticket link and QR code sent to {{ $ticket->client->phone }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 bg-yellow-50 border-2 border-yellow-300 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-yellow-600 text-xl mr-3 mt-0.5"></i>
                                    <div>
                                        <p class="font-semibold text-yellow-900 mb-1">Didn't receive WhatsApp?</p>
                                        <p class="text-sm text-yellow-800">
                                            Check your WhatsApp messages from our number. If you still don't see it, your ticket is available in your email, or you can download it below.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Download Buttons (Fallback) -->
                <div class="mb-8">
                    <div class="bg-gray-50 rounded-lg p-6 border-2 border-gray-200">
                        <p class="text-sm text-gray-600 mb-4 text-center">
                            <i class="fas fa-download mr-1"></i>
                            Or download your ticket directly:
                        </p>
                        @if(count($allTickets) > 1)
                            <div class="flex gap-3 justify-center flex-wrap">
                                @foreach($allTickets as $singleTicket)
                                    <a href="{{ route('ticket.download', $singleTicket->qr_code) }}" 
                                       target="_blank"
                                       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors text-sm">
                                        <i class="fas fa-download"></i>
                                        {{ $singleTicket->client->full_name }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center">
                                <a href="{{ route('ticket.download', $ticket->qr_code) }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors">
                                    <i class="fas fa-download"></i>
                                    Download Your Ticket
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- ⏳ PAYMENT PENDING - Instructions -->
            @if($ticket->payment_status !== 'completed')
                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                        Payment Instructions
                    </h3>

                    @if(count($allTickets) > 1)
                        <div class="bg-white rounded-lg p-4 mb-4 border border-yellow-300">
                            <p class="text-sm font-semibold text-gray-900 mb-2">
                                <i class="fas fa-users text-blue-600 mr-1"></i>
                                Group Payment Information
                            </p>
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <p class="text-xs text-gray-600">Total Tickets</p>
                                    <p class="text-lg font-bold text-gray-900">{{ count($allTickets) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Per Ticket</p>
                                    <p class="text-lg font-bold text-gray-900">{{ number_format($ticket->amount) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Total Amount</p>
                                    <p class="text-lg font-bold text-blue-600">{{ number_format($allTickets->sum('amount')) }} LSL</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($paymentMethodDetails)
                        <div class="space-y-3 text-sm">
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <p class="font-semibold text-gray-900 mb-2">
                                    <i class="fas {{ $paymentMethodDetails->icon ?? 'fa-money-bill' }} mr-2"></i>
                                    {{ $paymentMethodDetails->payment_method }}
                                </p>
                                
                                @if($paymentMethodDetails->account_number)
                                    <p class="text-gray-700 mb-1">
                                        <strong>Send to:</strong> 
                                        <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $paymentMethodDetails->account_number }}</span>
                                    </p>
                                @endif

                                <p class="text-gray-700 mb-1">
                                    <strong>Amount:</strong> 
                                    <span class="font-bold text-lg">{{ number_format($allTickets->sum('amount')) }} LSL</span>
                                </p>

                                <p class="text-gray-700">
                                    <strong>Reference:</strong> 
                                    <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $ticket->ticket_number }}</span>
                                </p>

                                @if($paymentMethodDetails->instructions)
                                    <div class="mt-3 p-3 bg-blue-50 rounded border border-blue-200">
                                        <p class="text-xs text-blue-900">
                                            <i class="fas fa-lightbulb mr-1"></i>
                                            {{ $paymentMethodDetails->instructions }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="bg-white rounded-lg p-3 border border-green-300">
                                <p class="text-xs text-gray-700">
                                    <i class="fas fa-check-circle text-green-600 mr-1"></i>
                                    <strong>Once payment is approved:</strong>
                                </p>
                                <ul class="text-xs text-gray-600 mt-2 space-y-1 ml-4">
                                    <li>• You'll receive an email with your ticket</li>
                                    @if($ticket->has_whatsapp)
                                    <li>• You'll receive a WhatsApp message with your ticket and QR code</li>
                                    @endif
                                    <li>• No need to do anything - we'll send it automatically!</li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- What's Next -->
            <div class="border-t pt-8">
                <h3 class="text-xl font-bold text-gray-900 mb-4">What Happens Next?</h3>
                
                <div class="space-y-4">
                    @if($ticket->payment_status === 'completed')
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-check text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Payment Confirmed ✅</p>
                                <p class="text-sm text-gray-600">Your ticket is ready to use</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-mobile-alt text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Check Your Messages</p>
                                <p class="text-sm text-gray-600">Ticket sent to your email{{ $ticket->has_whatsapp ? ' and WhatsApp' : '' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-qrcode text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">At The Event</p>
                                <p class="text-sm text-gray-600">Show your ticket QR code at the entrance</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-yellow-600 font-bold">1</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Make Payment</p>
                                <p class="text-sm text-gray-600">Follow the payment instructions above</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-yellow-600 font-bold">2</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Payment Verification</p>
                                <p class="text-sm text-gray-600">We'll verify within 24 hours</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-yellow-600 font-bold">3</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Automatic Delivery</p>
                                <p class="text-sm text-gray-600">We'll send your ticket to email{{ $ticket->has_whatsapp ? ' and WhatsApp' : '' }} automatically!</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 pt-6 border-t space-y-3 text-center">
                <a href="{{ route('public.event', ['orgSlug' => $event->organization->slug, 'eventSlug' => $event->slug]) }}" 
                   class="inline-block text-blue-600 hover:text-blue-800 font-semibold">
                    ← Back to Event Page
                </a>
                
                @if($event->organization->contact_email)
                    <p class="text-sm text-gray-600">
                        Questions? Contact us at <a href="mailto:{{ $event->organization->contact_email }}" class="text-blue-600 hover:text-blue-800">{{ $event->organization->contact_email }}</a>
                    </p>
                @endif
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} {{ $event->organization->name }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>