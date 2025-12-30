<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
        .ticket-box { background: white; border: 2px solid #10b981; border-radius: 10px; padding: 20px; margin: 20px 0; text-align: center; }
        .button { display: inline-block; padding: 15px 40px; background: #10b981; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
        .info-box { background: #e0f2fe; border-left: 4px solid #0284c7; padding: 15px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Your Ticket is Ready!</h1>
            <p>{{ $event->name }}</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $client->full_name }},</p>
            
            <p>Great news! Your ticket for <strong>{{ $event->name }}</strong> has been confirmed and is ready to use.</p>

            <div class="ticket-box">
                <h2 style="color: #10b981; margin-top: 0;">✓ Ticket Confirmed</h2>
                <p><strong>Ticket Number:</strong> {{ $ticket->ticket_number }}</p>
                <p><strong>Name:</strong> {{ $client->full_name }}</p>
                <p><strong>Tier:</strong> {{ $tier->tier_name }}</p>
                <p><strong>Amount:</strong> {{ number_format($ticket->amount) }} LSL</p>
                
                <a href="{{ $downloadLink }}" class="button">Download Your Ticket</a>
            </div>

            <div class="info-box">
                <h3>📅 Event Details</h3>
                <p><strong>Event:</strong> {{ $event->name }}</p>
                @if($event->event_date)
                    <p><strong>Date:</strong> {{ $event->event_date->format('l, F j, Y') }}</p>
                    <p><strong>Time:</strong> {{ $event->event_date->format('g:i A') }}</p>
                @endif
                @if($event->venue)
                    <p><strong>Venue:</strong> {{ $event->venue }}</p>
                @endif
                @if($event->location)
                    <p><strong>Address:</strong> {{ $event->location }}</p>
                @endif
            </div>

            <h3>Important Instructions:</h3>
            <ol>
                <li><strong>Download your ticket</strong> using the button above</li>
                <li><strong>Save it to your phone</strong> or print it out</li>
                <li><strong>Bring your ticket</strong> (digital or printed) to the event</li>
                <li><strong>Show your QR code</strong> at the entrance for check-in</li>
            </ol>

            <p><strong>Pro tip:</strong> Take a screenshot of your ticket or save it offline in case you don't have internet at the venue.</p>

            <p>If you have any questions or need assistance, please contact us at {{ $organization->contact_email ?? $organization->email }}.</p>

            <p>We're excited to see you at the event!</p>

            <p>Best regards,<br>{{ $organization->name }}</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $organization->name }}. All rights reserved.</p>
            <p>This email was sent to {{ $client->email }}</p>
        </div>
    </div>
</body>
</html>