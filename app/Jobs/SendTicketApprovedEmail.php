<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Mail\TicketApprovedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTicketApprovedEmail implements ShouldQueue
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
            Log::warning("Ticket ID {$this->ticketId} not found - skipping approved email");
            return;
        }

        // Only send if email is available and delivery method includes email
        if (!$ticket->client->email || !in_array($ticket->preferred_delivery, ['email', 'both'])) {
            Log::info("Ticket {$ticket->ticket_number} - Email not configured, skipping");
            return;
        }

        // Only send if payment is completed
        if ($ticket->payment_status !== 'completed') {
            Log::info("Ticket {$ticket->ticket_number} not completed - skipping approved email");
            return;
        }

        Log::info("📧 Sending approved email for ticket: {$ticket->ticket_number} to {$ticket->client->email}");
        
        try {
            Mail::to($ticket->client->email)->send(new TicketApprovedMail($ticket));
            
            Log::info("✅ Approved email sent successfully for: {$ticket->ticket_number}");
            
            // Mark as delivered via email
            $ticket->markAsDeliveredViaEmail();
            
        } catch (\Exception $e) {
            Log::error("❌ Approved email failed for {$ticket->ticket_number}: " . $e->getMessage());
            
            $ticket->logDeliveryFailure('email', 'Approved notification: ' . $e->getMessage());
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendTicketApprovedEmail job permanently failed for Ticket ID: {$this->ticketId}");
        
        $ticket = Ticket::find($this->ticketId);
        if ($ticket) {
            $ticket->logDeliveryFailure(
                'email', 
                'Approved email job failed after ' . $this->tries . ' attempts: ' . substr($exception->getMessage(), 0, 200)
            );
        }
    }
}