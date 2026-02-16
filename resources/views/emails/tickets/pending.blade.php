<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .ticket-box { background: white; border: 2px solid #667eea; border-radius: 10px; padding: 20px; margin: 20px 0; }
        .payment-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
        .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Registration Received!</h1>
            <p>{{ $event->name }}</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $client->full_name }},</p>
            
            <p>Thank you for registering for <strong>{{ $event->name }}</strong>! We've received your registration and it's now pending payment verification.</p>

            <div class="ticket-box">
                <h3>Your Ticket Details</h3>
                <p><strong>Ticket Number:</strong> {{ $ticket->ticket_number }}</p>
                <p><strong>Event:</strong> {{ $event->name }}</p>
                <p><strong>Tier:</strong> {{ $tier->tier_name }}</p>
                <p><strong>Amount:</strong> {{ number_format($ticket->amount) }} LSL</p>
                <p><strong>Status:</strong> <span style="color: #ffc107;">Pending Payment Verification</span></p>
            </div>

            <div class="payment-box">
                <h3>⚠️ Payment Required</h3>
                <p>Please complete your payment of <strong>{{ number_format($ticket->amount) }} LSL</strong> using one of the following methods:</p>

                @foreach($paymentMethods as $method)
                    <div style="background: white; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin: 12px 0;">
                        <h4 style="margin: 0 0 8px 0;">{{ $method->label }}</h4>

                        @if($method->payment_method !== 'cash')
                            <p style="margin: 4px 0;">
                                <strong>{{ $method->getAccountFieldLabel() }}:</strong> 
                                {{ $method->account_number }}
                            </p>
                            @if($method->account_name)
                            <p style="margin: 4px 0;">
                                <strong>Account Name:</strong> 
                                {{ $method->account_name }}
                            </p>
                            @endif
                        @else
                            <p style="margin: 4px 0; color: #666;">Pay in person at the venue.</p>
                        @endif

                        @if($method->instructions)
                            <p style="margin: 8px 0 0 0; color: #555; font-size: 13px;">
                                {{ $method->instructions }}
                            </p>
                        @endif
                    </div>
                @endforeach

                <p style="margin-top: 15px;">
                    <strong>Important:</strong> Always use your ticket number 
                    <strong>{{ $ticket->ticket_number }}</strong> as the payment reference.
                </p>
            </div>

            <h3>What Happens Next?</h3>
            <ol>
                <li>Make your payment using the instructions above</li>
                <li>Our team will verify your payment within 24 hours</li>
                <li>You'll receive an email with your ticket once approved</li>
                <li>Bring your ticket to the event entrance</li>
            </ol>

            <p>If you have any questions, please contact us at {{ $organization->contact_email ?? $organization->email }}.</p>

            <p>We look forward to seeing you at the event!</p>

            <p>Best regards,<br>{{ $organization->name }}</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $organization->name }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>