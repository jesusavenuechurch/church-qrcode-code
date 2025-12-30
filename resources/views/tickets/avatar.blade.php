<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket - {{ $ticket->ticket_number }}</title>
    <style>
        @page { 
            margin: 0; 
            size: A4 portrait;
        }
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 400px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 32px;
            font-weight: bold;
            color: white;
            margin-bottom: 8px;
        }
        
        .header .event-name {
            color: rgba(221, 214, 254, 1);
            font-size: 14px;
        }
        
        /* Main Card */
        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Ticket Info Section */
        .ticket-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .ticket-info .label {
            text-align: center;
            font-size: 11px;
            color: rgba(196, 181, 253, 1);
            margin-bottom: 8px;
        }
        
        .ticket-info .client-name {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: white;
            margin-bottom: 16px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 12px;
        }
        
        .info-label {
            color: rgba(196, 181, 253, 1);
        }
        
        .info-value {
            font-weight: 600;
            color: white;
            text-align: right;
            font-size: 11px;
        }
        
        /* QR Code Section */
        .qr-section {
            background: white;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            margin-bottom: 24px;
        }
        
        .qr-section img {
            display: block;
            width: 192px;
            height: 192px;
            margin: 0 auto;
        }
        
        .qr-placeholder {
            width: 192px;
            height: 192px;
            background: #f0f0f0;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        
        .qr-text {
            margin-top: 8px;
            font-size: 10px;
            color: #666;
        }
        
        /* Status Section */
        .status-section {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            font-size: 12px;
            color: rgba(196, 181, 253, 1);
        }
        
        .status-value {
            font-weight: 600;
            color: white;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: rgba(196, 181, 253, 1);
        }
        
        .footer .org-name {
            font-size: 13px;
            margin-bottom: 4px;
        }
        
        .footer .location {
            font-size: 10px;
            color: rgba(167, 139, 250, 1);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎫 Your Ticket</h1>
            <div class="event-name">{{ $event->name }}</div>
        </div>
        
        <!-- Main Card -->
        <div class="card">
            <!-- Ticket Info -->
            <div class="ticket-info">
                <div class="label">Ticket for</div>
                <div class="client-name">{{ $client->full_name }}</div>
                
                <div class="info-row">
                    <span class="info-label">Event</span>
                    <span class="info-value">{{ $event->name }}</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Tier</span>
                    <span class="info-value">{{ $tier->tier_name }}</span>
                </div>
                
                @if($event->event_date)
                <div class="info-row">
                    <span class="info-label">Date</span>
                    <span class="info-value">{{ $event->event_date->format('M d, Y') }}</span>
                </div>
                @endif
                
                <div class="info-row">
                    <span class="info-label">Ticket #</span>
                    <span class="info-value" style="font-family: 'Courier New', monospace; font-size: 9px;">
                        {{ $ticket->ticket_number }}
                    </span>
                </div>
            </div>
            
            <!-- QR Code -->
            <div class="qr-section">
                @if($ticket->qr_code_path && file_exists(public_path('storage/' . $ticket->qr_code_path)))
                    @php
                        $qrImagePath = public_path('storage/' . $ticket->qr_code_path);
                        $qrImageData = base64_encode(file_get_contents($qrImagePath));
                        $qrSrc = 'data:image/png;base64,' . $qrImageData;
                    @endphp
                    <img src="{{ $qrSrc }}" alt="QR Code">
                @else
                    <div class="qr-placeholder">
                        <div style="color: #999; font-size: 11px;">QR Code</div>
                    </div>
                @endif
                
                <div class="qr-text">Show this QR code at entry</div>
            </div>
            
            <!-- Status -->
            <div class="status-section">
                Ticket Status: 
                <span class="status-value">
                    @if($ticket->checked_in_at)
                        ✅ Checked In
                    @else
                        🎫 Active
                    @endif
                </span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="org-name">{{ $organization->name }}</div>
            @if($event->location)
                <div class="location">{{ $event->location }}</div>
            @endif
        </div>
    </div>
</body>
</html>