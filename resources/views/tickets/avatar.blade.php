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
            font-family: 'Helvetica', Arial, sans-serif;
            background-color: #f8fafc;
            padding: 60px 40px;
        }
        
        .container {
            max-width: 500px;
            margin: 0 auto;
        }

        /* The Physical Ticket Look */
        .ticket-wrapper {
            background-color: #0f172a; /* Slate 900 */
            border-radius: 40px;
            overflow: hidden;
            position: relative;
            color: white;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        /* Top Section: Event Brand */
        .brand-section {
            padding: 40px 40px 20px 40px;
            text-align: center;
        }

        .org-tag {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: #94a3b8;
            margin-bottom: 15px;
        }

        .event-title {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -1px;
            color: #ffffff;
            line-height: 1.1;
        }

        /* Middle Section: Holder Info */
        .holder-section {
            padding: 0 40px;
            text-align: center;
        }

        .holder-name {
            font-size: 22px;
            font-weight: 700;
            color: #38bdf8; /* Sky 400 */
            margin: 15px 0;
            text-transform: uppercase;
        }

        /* Detail Grid */
        .details-table {
            width: 100%;
            margin: 20px 0;
            border-top: 1px solid #1e293b;
            border-bottom: 1px solid #1e293b;
            padding: 20px 0;
        }

        .details-table td {
            padding: 8px 0;
        }

        .label {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #64748b;
        }

        .value {
            font-size: 12px;
            font-weight: 700;
            color: #f1f5f9;
        }

        /* QR Section: The "Stub" */
        .qr-section {
            background-color: #ffffff;
            margin: 20px 40px 40px 40px;
            padding: 30px;
            border-radius: 25px;
            text-align: center;
        }

        .qr-image {
            width: 200px;
            height: 200px;
            margin-bottom: 15px;
        }

        .ticket-id-footer {
            font-family: 'Courier', monospace;
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 1px;
        }

        /* Stub Perforation Effect */
        .perforation {
            height: 2px;
            border-top: 2px dashed #1e293b;
            margin: 0 20px;
            position: relative;
        }

        .perforation:before, .perforation:after {
            content: "";
            position: absolute;
            top: -11px;
            width: 20px;
            height: 20px;
            background-color: #f8fafc;
            border-radius: 50%;
        }
        .perforation:before { left: -31px; }
        .perforation:after { right: -31px; }

        .footer-note {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="ticket-wrapper">
            <div class="brand-section">
                <div class="org-tag">{{ $organization->name }}</div>
                <h1 class="event-title">{{ $event->name }}</h1>
            </div>

            <div class="holder-section">
                <div class="label" style="color: #38bdf8;">Official Guest</div>
                <div class="holder-name">{{ $client->full_name }}</div>

                <table class="details-table">
                    <tr>
                        <td width="50%" align="left">
                            <div class="label">Tier</div>
                            <div class="value">{{ $tier->tier_name }}</div>
                        </td>
                        <td width="50%" align="right">
                            <div class="label">Date</div>
                            <div class="value">{{ $event->event_date ? $event->event_date->format('d M Y') : 'TBA' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" align="left">
                            <div class="label">Location</div>
                            <div class="value">{{ $event->location ?? 'Announced Soon' }}</div>
                        </td>
                        <td width="50%" align="right">
                            <div class="label">Status</div>
                            <div class="value">ACTIVE PASS</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="perforation"></div>

            <div class="qr-section">
                @if($ticket->qr_code_path && file_exists(public_path('storage/' . $ticket->qr_code_path)))
                    @php
                        $qrImagePath = public_path('storage/' . $ticket->qr_code_path);
                        $qrImageData = base64_encode(file_get_contents($qrImagePath));
                        $qrSrc = 'data:image/png;base64,' . $qrImageData;
                    @endphp
                    <img src="{{ $qrSrc }}" class="qr-image" alt="QR Code">
                @else
                    <div style="width: 200px; height: 200px; background: #f1f5f9; margin: 0 auto 15px auto; line-height: 200px; color: #cbd5e1; font-weight: bold; border-radius: 15px;">QR CODE</div>
                @endif
                
                <div class="ticket-id-footer">#{{ $ticket->ticket_number }}</div>
                <div class="label" style="color: #94a3b8; margin-top: 5px; letter-spacing: 1px;">Scan at Entrance</div>
            </div>
        </div>

        <div class="footer-note">
            Please present this digital or printed pass for entry
        </div>
    </div>
</body>
</html>