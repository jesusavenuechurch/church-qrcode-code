<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Mail\TicketPendingMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTicketPendingEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 10;
    public $timeout = 120;

    protected int $ticketId;

    public function __construct(int $ticketId)
    {
        $this->ticketId = $ticketId;
    }

    public function handle(): void
    {
        $ticket = Ticket::with(['client', 'event', 'tier'])->find($this->ticketId);
        
        if (!$ticket) {
            Log::warning("Ticket ID {$this->ticketId} not found - skipping pending email");
            return;
        }

        // Only send if email is available (always send email if we have one)
        if (!$ticket->client->email) {
            Log::info("Ticket {$ticket->ticket_number} - No email address, skipping");
            return;
        }

        // Don't send if already completed
        if ($ticket->payment_status === 'completed') {
            Log::info("Ticket {$ticket->ticket_number} already completed - skipping pending email");
            return;
        }

        Log::info("📧 Sending pending email for ticket: {$ticket->ticket_number} to {$ticket->client->email}");
        
        try {
            Mail::to($ticket->client->email)->send(new TicketPendingMail($ticket));
            
            Log::info("✅ Pending email sent successfully for: {$ticket->ticket_number}");
            
            // Log email delivery
            $ticket->update([
                'delivery_log' => array_merge($ticket->delivery_log ?? [], [
                    [
                        'method' => 'email',
                        'status' => 'sent',
                        'type' => 'pending_notification',
                        'timestamp' => now()->toIso8601String(),
                        'email' => $ticket->client->email,
                    ]
                ]),
            ]);
            
        } catch (\Exception $e) {
            Log::error("❌ Pending email failed for {$ticket->ticket_number}: " . $e->getMessage());
            
            $ticket->logDeliveryFailure('email', 'Pending notification: ' . $e->getMessage());
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendTicketPendingEmail job permanently failed for Ticket ID: {$this->ticketId}");
        
        $ticket = Ticket::find($this->ticketId);
        if ($ticket) {
            $ticket->logDeliveryFailure(
                'email', 
                'Pending email job failed after ' . $this->tries . ' attempts: ' . substr($exception->getMessage(), 0, 200)
            );
        }
    }
}
