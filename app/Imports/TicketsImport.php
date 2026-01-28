<?php
// app/Imports/TicketsImport.php

namespace App\Imports;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\EventTier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TicketsImport implements ToCollection, WithHeadingRow
{
    protected $eventId;
    protected $tierId;
    protected $isComplimentary;
    protected $reason;
    protected $organizationId;
    protected $createdBy;
    
    public $successCount = 0;
    public $errorCount = 0;
    public $errors = [];

    public function __construct(
        int $eventId,
        int $tierId,
        int $organizationId,
        int $createdBy,
        bool $isComplimentary = false,
        ?string $reason = null
    ) {
        $this->eventId = $eventId;
        $this->tierId = $tierId;
        $this->organizationId = $organizationId;
        $this->createdBy = $createdBy;
        $this->isComplimentary = $isComplimentary;
        $this->reason = $reason;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because: 1 for header, 1 for 0-based index
            
            try {
                DB::beginTransaction();

                // Convert row to array for easier access
                $rowData = $row->toArray();
                
                // Try different possible column name variations
                $fullName = $rowData['full_name'] ?? $rowData['fullname'] ?? $rowData['name'] ?? null;
                $phone = $rowData['phone'] ?? $rowData['phone_number'] ?? $rowData['mobile'] ?? null;
                $email = $rowData['email'] ?? $rowData['email_address'] ?? null;
                $hasWhatsApp = $rowData['has_whatsapp'] ?? $rowData['whatsapp'] ?? false;

                // Debug log
                Log::info("Processing row {$rowNumber}", [
                    'row_keys' => array_keys($rowData),
                    'full_name' => $fullName,
                    'phone' => $phone,
                ]);

                // Validate required fields (ONLY full_name is required)
                if (empty($fullName)) {
                    throw new \Exception("Missing full_name. Available columns: " . implode(', ', array_keys($rowData)));
                }

                // If no phone provided, generate a unique placeholder
                if (empty($phone)) {
                    // Generate unique phone based on name + timestamp
                    $phone = '+266' . substr(str_pad(abs(crc32($fullName . now()->timestamp)), 8, '0'), 0, 8);
                } else {
                    // Clean phone number
                    $phone = $this->cleanPhone($phone);
                }

                // Find or create client
                $client = Client::firstOrCreate(
                    ['phone' => $phone],
                    [
                        'full_name' => $fullName,
                        'email' => $email,
                        'organization_id' => $this->organizationId,
                    ]
                );

                // Update client if exists but data changed
                if ($client->wasRecentlyCreated === false) {
                    $client->update([
                        'full_name' => $fullName,
                        'email' => $email ?? $client->email,
                    ]);
                }

                // Check if ticket already exists for this client & event
                $existingTicket = Ticket::where('client_id', $client->id)
                    ->where('event_id', $this->eventId)
                    ->where('event_tier_id', $this->tierId)
                    ->first();

                if ($existingTicket) {
                    throw new \Exception("Ticket already exists for this client at this event/tier");
                }

                // Convert WhatsApp to boolean
                if (is_string($hasWhatsApp)) {
                    $hasWhatsApp = in_array(strtolower($hasWhatsApp), ['true', '1', 'yes', 'y']);
                }

                // Create ticket
                $ticket = Ticket::create([
                    'event_id' => $this->eventId,
                    'client_id' => $client->id,
                    'event_tier_id' => $this->tierId,
                    'created_by' => $this->createdBy,
                    'has_whatsapp' => $hasWhatsApp,
                    'preferred_delivery' => $hasWhatsApp ? 'both' : 'email',
                ]);

                // Mark as complimentary if specified
                if ($this->isComplimentary) {
                    $ticket->markAsComplimentary(
                        $this->createdBy,
                        $this->reason ?? 'Bulk import - complimentary ticket'
                    );
                } else {
                    // For paid tickets, set initial payment status
                    $ticket->update([
                        'payment_status' => 'pending',
                        'status' => 'pending',
                    ]);
                }

                // Generate QR code
                $ticket->generateQrCode();

                // Auto-deliver if complimentary
                if ($this->isComplimentary) {
                    dispatch(fn() => $ticket->autoDeliverTicket())->afterResponse();
                }

                DB::commit();
                $this->successCount++;

                Log::info("Bulk import: Created ticket for {$client->full_name}", [
                    'row' => $rowNumber,
                    'ticket_id' => $ticket->id,
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                $this->errorCount++;
                $this->errors[] = [
                    'row' => $rowNumber,
                    'name' => $fullName ?? 'N/A',
                    'phone' => $phone ?? 'N/A',
                    'error' => $e->getMessage(),
                ];

                Log::error("Bulk import error on row {$rowNumber}", [
                    'error' => $e->getMessage(),
                    'data' => $row->toArray(),
                ]);
            }
        }
    }

    /**
     * Clean phone number (remove spaces, dashes, etc.)
     */
    protected function cleanPhone(string $phone): string
    {
        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);
        
        // If doesn't start with +266, add it
        if (!str_starts_with($cleaned, '+266')) {
            $cleaned = '+266' . ltrim($cleaned, '0');
        }
        
        return $cleaned;
    }
}