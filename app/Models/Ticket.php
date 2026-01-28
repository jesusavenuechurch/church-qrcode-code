<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use App\Notifications\TicketRegistrationNotification;
use App\Notifications\TicketApprovedNotification;
use App\Notifications\LowTicketInventoryNotification;

class Ticket extends Model
{
    use Notifiable;

    protected $fillable = [
        'event_id', 'client_id', 'event_tier_id', 'ticket_number', 'qr_code',
        'qr_code_path', 'status', 'payment_method', 'amount', 'payment_status',
        'payment_date', 'payment_reference', 'delivery_method', 'delivered_at',
        'checked_in_at', 'checked_in_by', 'created_by', 'ticket_preference',
        'printed_at', 'printed_by', 'avatar_path', 'avatar_generated_at',
        // ✅ NEW: WhatsApp delivery fields
        'preferred_delivery', 'has_whatsapp', 'delivery_status',
        'whatsapp_delivered_at', 'email_delivered_at', 'delivery_log', 'amount_paid',
        'is_complimentary', 'complimentary_issued_by', 'complimentary_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'payment_date' => 'datetime',
        'delivered_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'avatar_generated_at' => 'datetime',
        'printed_at' => 'datetime',
        // ✅ NEW: WhatsApp delivery casts
        'has_whatsapp' => 'boolean',
        'whatsapp_delivered_at' => 'datetime',
        'email_delivered_at' => 'datetime',
        'delivery_log' => 'array',
        'is_complimentary' => 'boolean',
    ];

    public function isComplimentary(): bool
    {
        return $this->is_complimentary === true;
    }

    /**
     * Relationship to admin who issued complimentary ticket
     */
    public function complimentaryIssuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'complimentary_issued_by');
    }

    /**
     * Mark ticket as complimentary (called when admin creates it)
     */
    public function markAsComplimentary(int $adminId, ?string $reason = null): void
    {
        $this->update([
            'is_complimentary' => true,
            'complimentary_issued_by' => $adminId,
            'complimentary_reason' => $reason,
            'amount' => 0, // Complimentary = M0
            'amount_paid' => 0,
            'payment_status' => 'completed', // Auto-approved
            'status' => 'active', // Ready to use
            'payment_date' => now(),
        ]);

        Log::info("Ticket {$this->ticket_number} marked as complimentary by admin {$adminId}");
    }

    // ===== RELATIONSHIPS =====

    public function printedBy()
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    public function isPrinted(): bool
    {
        return $this->printed_at !== null;
    }

    public function markAsPrinted(int $adminId): void
    {
        $this->update([
            'printed_at' => now(),
            'printed_by' => $adminId,
        ]);
    }

    public function generateAvatar(): bool
    {
        try {
            // Make sure QR code exists first
            if (!$this->qr_code_path) {
                $this->generateQrCode();
            }

            // Prepare data for the view
            $data = [
                'ticket' => $this,
                'organization' => $this->event->organization,
                'event' => $this->event,
                'client' => $this->client,
                'tier' => $this->tier,
            ];

            // Generate PDF from view
            $pdf = \PDF::loadView('tickets.avatar', $data)
                ->setPaper([0, 0, 595.28, 280.63], 'landscape') // DL size in points (210mm x 99mm)
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('isRemoteEnabled', true) // Allow loading images
                ->setOption('chroot', [public_path('storage')]) // Allow access to storage
                ->setOption('enable_php', false);

            $filename = 'avatars/events/' . $this->event->organization_id . '/ticket_' . $this->id . '.pdf';
            
            // Ensure directory exists
            $directory = dirname(storage_path('app/public/' . $filename));
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            Storage::disk('public')->put($filename, $pdf->output());

            $this->update([
                'avatar_path' => $filename,
                'avatar_generated_at' => now(),
            ]);

            Log::info("PDF generated successfully for Ticket ID: {$this->id}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to generate avatar for ticket {$this->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path ? Storage::url($this->avatar_path) : null;
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(EventTier::class, 'event_tier_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function paymentRecords(): HasMany
    {
        return $this->hasMany(PaymentRecord::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(TicketPayment::class);
    }

    public function updatePaymentStatus(): void
    {
        if ($this->is_complimentary) {
            return;
        }
        // Calculate total paid from approved payments
        $totalPaid = $this->payments()->approved()->sum('amount');
        
        $this->update(['amount_paid' => $totalPaid]);

        // Update payment status based on amount paid
        if ($totalPaid >= $this->amount) {
            $this->update([
                'payment_status' => 'completed',
                'status' => 'active',
            ]);
        } elseif ($totalPaid > 0) {
            $this->update(['payment_status' => 'partial']);
        } else {
            $this->update(['payment_status' => 'pending']);
        }
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->amount - $this->amount_paid);
    }

    public function getPaymentProgressAttribute(): float
    {
        if ($this->amount <= 0) {
            return 100;
        }
        return min(100, ($this->amount_paid / $this->amount) * 100);
    }

    public function getMinimumDepositAttribute(): float
    {
        if (!$this->event->allow_installments) {
            return $this->amount;
        }
        
        $percentage = $this->event->minimum_deposit_percentage ?? 30;
        return round(($this->amount * $percentage) / 100, 2);
    }

    /**
     * Check if this ticket supports installments
     */
    public function hasInstallments(): bool
    {
        return $this->event->allow_installments ?? false;
    }

    /**
     * Check if ticket has pending installment payments
     */
    public function hasPendingPayments(): bool
    {
        return $this->payments()->pending()->exists();
    }

    // ===== 🆕 WHATSAPP DELIVERY HELPERS =====

    /**
     * Check if ticket should be delivered via WhatsApp
     * WhatsApp is OPTIONAL based on user preference
     */
    public function shouldDeliverViaWhatsApp(): bool
    {
        return $this->has_whatsapp && 
               in_array($this->preferred_delivery, ['whatsapp', 'both']) &&
               $this->client->phone;
    }

    /**
     * Check if ticket should be delivered via Email
     * Email is ALWAYS sent if email address exists (free, reliable, standard)
     */
    public function shouldDeliverViaEmail(): bool
    {
        return !empty($this->client->email);
    }

    /**
     * Check if ticket has been delivered via WhatsApp
     */
    public function isDeliveredViaWhatsApp(): bool
    {
        return $this->whatsapp_delivered_at !== null;
    }

    /**
     * Check if ticket has been delivered via Email
     */
    public function isDeliveredViaEmail(): bool
    {
        return $this->email_delivered_at !== null;
    }

    /**
     * Mark ticket as delivered via WhatsApp
     */
    public function markAsDeliveredViaWhatsApp(): void
    {
        $log = $this->delivery_log ?? [];
        $log[] = [
            'method' => 'whatsapp',
            'status' => 'delivered',
            'timestamp' => now()->toIso8601String(),
            'phone' => $this->client->phone,
        ];

        $this->update([
            'whatsapp_delivered_at' => now(),
            'delivery_status' => 'delivered',
            'delivery_log' => $log,
        ]);
    }

    /**
     * Mark ticket as delivered via Email
     */
    public function markAsDeliveredViaEmail(): void
    {
        $log = $this->delivery_log ?? [];
        $log[] = [
            'method' => 'email',
            'status' => 'delivered',
            'timestamp' => now()->toIso8601String(),
            'email' => $this->client->email,
        ];

        $this->update([
            'email_delivered_at' => now(),
            'delivery_status' => 'delivered',
            'delivery_log' => $log,
        ]);
    }

    /**
     * Log delivery failure
     */
    public function logDeliveryFailure(string $method, string $reason): void
    {
        $log = $this->delivery_log ?? [];
        $log[] = [
            'method' => $method,
            'status' => 'failed',
            'reason' => $reason,
            'timestamp' => now()->toIso8601String(),
        ];

        $this->update([
            'delivery_status' => 'failed',
            'delivery_log' => $log,
        ]);
    }

    // ===== HOOKS =====

    protected static function booted(): void
    {
        static::creating(function ($ticket) {
            if (!$ticket->qr_code) {
                $ticket->qr_code = 'QR-' . Str::uuid();
            }

            if (!$ticket->ticket_number) {
                $ticket->ticket_number = 'TKT-' . $ticket->event_id . '-' . Str::random(8);
            }

            Log::info("New ticket created: {$ticket->ticket_number}");
        });

        static::updated(function ($ticket) {
            if ($ticket->isDirty('event_tier_id')) {
                Log::info("Tier changed for Ticket ID: {$ticket->id}, regenerating QR code");
                $ticket->generateQrCode();
            }
        });

        static::created(function ($ticket) {
            // ✅ Don't notify admins for complimentary tickets (they just created it!)
            if (!$ticket->is_complimentary && $ticket->payment_status === 'pending') {
                Log::info("🔔 Payment is PENDING - calling notifyAdminsOfNewRegistration()");
                $ticket->notifyAdminsOfNewRegistration();
            } else {
                Log::info("⏭️ Skipping notification (complimentary or not pending)");
            }
        });

        static::updated(function ($ticket) {
            // ✅ Auto-deliver complimentary tickets immediately
            if ($ticket->isDirty('is_complimentary') && $ticket->is_complimentary) {
                dispatch(function () use ($ticket) {
                    $ticket->autoDeliverTicket();
                })->afterResponse();
            }

            // Normal payment approval flow
            if ($ticket->isDirty('payment_status') && $ticket->payment_status === 'completed') {
                dispatch(function () use ($ticket) {
                    $ticket->notifyClientOfApproval();
                    $ticket->checkLowInventory();
                    $ticket->autoDeliverTicket();
                })->afterResponse();
            }
        });
    }

    // ===== 🆕 AUTO-DELIVERY METHOD =====

    /**
     * Automatically deliver ticket based on preferred delivery method
     * NON-BLOCKING: Failures are logged but don't stop payment approval
     */
    public function autoDeliverTicket(): void
    {
        try {
            Log::info("🚀 Auto-delivering ticket {$this->ticket_number}");

            // Deliver via WhatsApp if enabled
            if ($this->shouldDeliverViaWhatsApp()) {
                try {
                    $success = \App\Http\Controllers\WhatsAppController::deliverTicket($this);
                    
                    if ($success) {
                        Log::info("✅ WhatsApp delivery successful for {$this->ticket_number}");
                    } else {
                        Log::warning("⚠️ WhatsApp delivery failed for {$this->ticket_number}");
                        $this->logDeliveryFailure('whatsapp', 'Delivery method returned false - check Twilio limits');
                    }
                } catch (\Exception $e) {
                    // Log but don't throw - delivery failures shouldn't block approval
                    Log::error("❌ WhatsApp delivery exception for {$this->ticket_number}: " . $e->getMessage());
                    $this->logDeliveryFailure('whatsapp', $e->getMessage());
                }
            } else {
                Log::info("⏭️ Skipping WhatsApp delivery for {$this->ticket_number} (not enabled or no phone)");
            }

            // Deliver via Email if enabled
            if ($this->shouldDeliverViaEmail()) {
                try {
                    // TODO: Implement email delivery
                    // Mail::to($this->client->email)->send(new TicketDeliveryMail($this));
                    Log::info("📧 Email delivery would trigger here for {$this->ticket_number}");
                } catch (\Exception $e) {
                    Log::error("❌ Email delivery exception for {$this->ticket_number}: " . $e->getMessage());
                    $this->logDeliveryFailure('email', $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            // Final catch-all - log but never throw
            Log::error("❌ Auto-delivery critical error for ticket {$this->ticket_number}: " . $e->getMessage());
        }
    }

    // ===== TIER COLOR LOGIC =====

    /**
     * Get the tier color (RGB) for QR code generation
     * Color is based on tier name or can be customized
     */
    public function getTierColorAttribute(): array
    {
        if (!$this->tier) {
            return ['r' => 0, 'g' => 0, 'b' => 0]; // Black fallback
        }

        // Use tier's color if it exists, otherwise default colors
        if ($this->tier->color) {
            return $this->hexToRgb($this->tier->color);
        }

        // Default colors by tier name
        return $this->getDefaultColorForTierName($this->tier->tier_name);
    }

    /**
     * Get default colors based on common tier names
     */
    private function getDefaultColorForTierName(string $tierName): array
    {
        $normalized = strtolower(trim($tierName));

        return match($normalized) {
            'general', 'standard' => ['r' => 0, 'g' => 100, 'b' => 200],        // Blue
            'silver' => ['r' => 192, 'g' => 192, 'b' => 192],                   // Silver
            'gold' => ['r' => 255, 'g' => 215, 'b' => 0],                       // Gold
            'vip' => ['r' => 139, 'g' => 69, 'b' => 19],                        // Brown/VIP
            'diamond', 'premium' => ['r' => 128, 'g' => 0, 'b' => 128],         // Purple
            'platinum' => ['r' => 230, 'g' => 230, 'b' => 250],                 // Lavender
            'ruby' => ['r' => 155, 'g' => 17, 'b' => 30],                       // Ruby Red
            'emerald' => ['r' => 80, 'g' => 200, 'b' => 120],                   // Emerald Green
            default => ['r' => 0, 'g' => 0, 'b' => 0],                          // Black fallback
        };
    }

    /**
     * Convert hex color to RGB array
     */
    private function hexToRgb(string $hex): array
    {
        // Remove # if present
        $hex = ltrim($hex, '#');

        // Handle 3-digit hex
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $color = hexdec($hex);
        return [
            'r' => ($color >> 16) & 0xFF,
            'g' => ($color >> 8) & 0xFF,
            'b' => $color & 0xFF,
        ];
    }

    // ===== QR CODE GENERATION =====

    /**
     * Generate QR code with tier-specific color
     * QR contains: ticket_id|event_id|tier_id|client_id|uuid (for validation)
     */
    public function generateQrCode(): bool
    {
        try {
            // QR content: verification URL with ticket UUID
            $verificationUrl = route('ticket.download', $this->qr_code);

            if (!$verificationUrl) {
                Log::warning("Route ticket.verify not found for Ticket ID: {$this->id}");
                return false;
            }

            $color = $this->tier_color;
            $filename = 'qr_codes/events/' . $this->event->organization_id . '/ticket_' . $this->id . '.png';

            // Generate QR code with tier color
            $qrContent = QrCode::format('png')
                ->size(300)
                ->margin(2)
                ->color($color['r'], $color['g'], $color['b'])
                ->generate($verificationUrl);

            Storage::disk('public')->put($filename, $qrContent);

            $this->update(['qr_code_path' => $filename]);

            Log::info("QR code generated for ticket {$this->ticket_number}: {$filename}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to generate QR code for ticket {$this->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get the public URL for the QR code
     */
    public function getQrCodeUrlAttribute(): ?string
    {
        return $this->qr_code_path 
            ? Storage::url($this->qr_code_path) 
            : null;
    }

    // ===== TICKET OPERATIONS =====

    /**
     * Validate QR code (used during scanning)
     */
    public function validateQrCode(string $providedQrCode): bool
    {
        // Check if QR matches
        if ($this->qr_code !== $providedQrCode) {
            Log::warning("Invalid QR code for ticket {$this->id}");
            return false;
        }

        // Check if already used
        if ($this->status === 'checked_in') {
            Log::warning("Ticket {$this->id} already checked in");
            return false;
        }

        // Check if ticket is active
        if ($this->status !== 'active' && $this->status !== 'pending') {
            Log::warning("Ticket {$this->id} status is {$this->status}");
            return false;
        }

        // Check if payment is pending (can still check in but flag it)
        if ($this->payment_status === 'pending') {
            Log::info("Ticket {$this->id} has pending payment but is valid");
        }

        return true;
    }

    /**
     * Check in a ticket (mark as used)
     */
    public function checkIn(?int $userId = null): bool
    {
        try {
            $this->update([
                'status' => 'checked_in',
                'checked_in_at' => now(),
                'checked_in_by' => $userId,
            ]);

            Log::info("Ticket {$this->ticket_number} checked in by user {$userId}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to check in ticket {$this->id}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Refund a ticket
     */
    public function refund(): bool
    {
        try {
            $this->update([
                'status' => 'refunded',
                'payment_status' => 'refunded',
            ]);

            Log::info("Ticket {$this->ticket_number} refunded");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to refund ticket {$this->id}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Check if ticket is valid for check-in
     */
    public function isValid(): bool
    {
        return $this->status === 'active' && $this->payment_status === 'completed';
    }

    /**
     * Check if ticket is checked in
     */
    public function isCheckedIn(): bool
    {
        return $this->status === 'checked_in';
    }

    /**
     * Check if payment is pending
     */
    public function isPendingPayment(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if payment is complete
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Get ticket summary for display (useful for scanning)
     */
    public function getSummaryAttribute(): array
    {
        return [
            'ticket_number' => $this->ticket_number,
            'client_name' => $this->client?->full_name,
            'event_name' => $this->event?->name,
            'tier_name' => $this->tier?->tier_name,
            'tier_color' => $this->tier_color,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'checked_in_at' => $this->checked_in_at,
            'is_valid' => $this->isValid(),
            'is_checked_in' => $this->isCheckedIn(),
        ];
    }

    protected function notifyAdminsOfNewRegistration()
    {
        try {
            Log::info("🔔 Attempting to notify admins for ticket {$this->id}");
            
            // Get org-specific admins
            $orgAdmins = \App\Models\User::where('organization_id', $this->event->organization_id)
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['org_admin', 'staff']);
                })
                ->get();

            // Get super admins (they see ALL organizations)
            $superAdmins = \App\Models\User::whereHas('roles', function ($q) {
                    $q->where('name', 'super_admin');
                })
                ->get();

            // Combine both
            $allAdmins = $orgAdmins->merge($superAdmins);

            Log::info("Found {$allAdmins->count()} admin(s) to notify (Org: {$orgAdmins->count()}, Super: {$superAdmins->count()})");

            if ($allAdmins->isEmpty()) {
                Log::warning("No admins found for organization {$this->event->organization_id}");
                return;
            }

            foreach ($allAdmins as $admin) {
                $admin->notify(new TicketRegistrationNotification($this));
                Log::info("✅ Notification sent to {$admin->email} (ID: {$admin->id})");
            }
            
            Log::info("🎉 All notifications sent successfully for ticket {$this->id}");
            
        } catch (\Exception $e) {
            Log::error("❌ Failed to send notifications for ticket {$this->id}: " . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    protected function notifyClientOfApproval()
    {
        // Client notification (optional for now since no email)
        // $this->client->notify(new TicketApprovedNotification($this));
    }

    protected function checkLowInventory()
    {
        try {
            $tier = $this->tier;
            
            if ($tier->quantity_available) {
                $remaining = $tier->quantity_available - $tier->quantity_sold;
                $percentageRemaining = ($remaining / $tier->quantity_available) * 100;

                Log::info("📊 Checking inventory for tier {$tier->id}: {$remaining} remaining ({$percentageRemaining}%)");

                if ($percentageRemaining <= 20 && $percentageRemaining > 0) {
                    // Org admins
                    $orgAdmins = \App\Models\User::where('organization_id', $this->event->organization_id)
                        ->whereHas('roles', function ($q) {
                            $q->whereIn('name', ['org_admin', 'staff']);
                        })
                        ->get();

                    // Super admins
                    $superAdmins = \App\Models\User::whereHas('roles', function ($q) {
                            $q->where('name', 'super_admin');
                        })
                        ->get();

                    $allAdmins = $orgAdmins->merge($superAdmins);

                    Log::info("⚠️ LOW INVENTORY ALERT! Notifying {$allAdmins->count()} admin(s)");

                    foreach ($allAdmins as $admin) {
                        $admin->notify(new LowTicketInventoryNotification($tier, $remaining));
                        Log::info("✅ Low inventory notification sent to {$admin->email}");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("❌ Failed to check low inventory: " . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    // For sending notifications to client
    public function routeNotificationForDatabase()
    {
        return $this->client;
    }
}