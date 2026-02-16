<?php
// app/Models/OrganizationPackage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationPackage extends Model
{
    use HasFactory;
    protected $fillable = [
        'organization_id',
        'package_type',
        'price_paid',
        'events_included',
        'events_used',
        'tickets_included',
        'tickets_used',
        'comp_tickets_included',
        'comp_tickets_used',
        'overage_ticket_rate',
        'status',
        'purchased_at',
        'expires_at',
        'payment_method',
        'payment_reference',
        'purchased_by',
        'agent_commission_processed',
        'is_free_trial',
        'notes',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'events_included' => 'integer',
        'events_used' => 'integer',
        'tickets_included' => 'integer',
        'tickets_used' => 'integer',
        'comp_tickets_included' => 'integer',
        'comp_tickets_used' => 'integer',
        'overage_ticket_rate' => 'decimal:2',
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
        'agent_commission_processed' => 'boolean',
        'is_free_trial' => 'boolean',
    ];

    // ===== PACKAGE DEFINITIONS =====
    
    public static function getPackageDefinitions(): array
    {
        return [
            'starter' => [
                'name' => 'Starter Event',
                'price' => 250.00,
                'events' => 1,
                'tickets' => 50,
                'comp_tickets' => 12,
                'overage_rate' => 10.00,
                'description' => 'Best for workshops, trainings, and small gatherings.',
            ],
            'standard' => [
                'name' => 'Standard Event',
                'price' => 700.00,
                'events' => 1,
                'tickets' => 300,
                'comp_tickets' => 40,
                'overage_rate' => 8.00,
                'description' => 'Conferences, seminars & large workshops.',
            ],
            'multi_event' => [
                'name' => 'Multi-Event Pack',
                'price' => 1500.00,
                'events' => 3,
                'tickets' => 1000,
                'comp_tickets' => 150,
                'overage_rate' => 6.00,
                'description' => 'For organizations running multiple events throughout the year.',
            ],
        ];
    }

    public static function createFreeTrialPackage(int $organizationId): self
    {
        $standardDef = self::getPackageDefinitions()['standard'];
        
        return self::create([
            'organization_id' => $organizationId,
            'package_type' => 'free_trial',
            'price_paid' => 0,
            'events_included' => 1,
            'events_used' => 0,
            'tickets_included' => $standardDef['tickets'],
            'tickets_used' => 0,
            'comp_tickets_included' => $standardDef['comp_tickets'],
            'comp_tickets_used' => 0,
            'overage_ticket_rate' => $standardDef['overage_rate'],
            'status' => 'active',
            'purchased_at' => now(),
            'is_free_trial' => true,
        ]);
    }

    // ===== RELATIONSHIPS =====

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'organization_package_id');
    }

    public function overages(): HasMany
    {
        return $this->hasMany(PackageOverage::class);
    }

    public function purchasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchased_by');
    }

    // ===== STATUS CHECKS =====

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isExhausted(): bool
    {
        return $this->status === 'exhausted';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->expires_at && $this->expires_at->isPast());
    }

    // ===== AVAILABILITY CHECKS =====

    public function hasEventsRemaining(): bool
    {
        return $this->events_used < $this->events_included;
    }

    public function hasTicketsRemaining(int $quantity = 1): bool
    {
        return ($this->tickets_used + $quantity) <= $this->tickets_included;
    }

    public function hasCompTicketsRemaining(int $quantity = 1): bool
    {
        return ($this->comp_tickets_used + $quantity) <= $this->comp_tickets_included;
    }

    // ===== GETTERS =====

    public function getRemainingEventsAttribute(): int
    {
        return max(0, $this->events_included - $this->events_used);
    }

    public function getRemainingTicketsAttribute(): int
    {
        return max(0, $this->tickets_included - $this->tickets_used);
    }

    public function getRemainingCompTicketsAttribute(): int
    {
        return max(0, $this->comp_tickets_included - $this->comp_tickets_used);
    }

    public function getUsagePercentageAttribute(): int
    {
        if ($this->tickets_included == 0) return 0;
        return (int) (($this->tickets_used / $this->tickets_included) * 100);
    }

    // ===== USAGE TRACKING =====

    public function incrementEventsUsed(): void
    {
        $this->increment('events_used');
        $this->checkIfExhausted();
    }

    public function incrementTicketsUsed(int $quantity = 1): void
    {
        $this->increment('tickets_used', $quantity);
        $this->checkIfExhausted();
    }

    public function incrementCompTicketsUsed(int $quantity = 1): void
    {
        $this->increment('comp_tickets_used', $quantity);
    }

    protected function checkIfExhausted(): void
    {
        if ($this->events_used >= $this->events_included && 
            $this->tickets_used >= $this->tickets_included) {
            $this->update(['status' => 'exhausted']);
        }
    }

    // ===== OVERAGE HANDLING =====

    public function calculateOverage(int $ticketsNeeded, bool $isComp = false): array
    {
        if ($isComp) {
            $remaining = $this->remaining_comp_tickets;
            $overage = max(0, $ticketsNeeded - $remaining);
            
            return [
                'needs_overage' => $overage > 0,
                'overage_quantity' => $overage,
                'overage_rate' => 0, // Comp tickets don't have overage charges
                'overage_amount' => 0,
                'can_create' => true, // Always allow comp ticket overage
            ];
        }

        $remaining = $this->remaining_tickets;
        $overage = max(0, $ticketsNeeded - $remaining);

        return [
            'needs_overage' => $overage > 0,
            'overage_quantity' => $overage,
            'overage_rate' => $this->overage_ticket_rate,
            'overage_amount' => $overage * $this->overage_ticket_rate,
            'can_create' => true, // Can create if user accepts overage
        ];
    }

    public function recordOverage(int $quantity, bool $isComp, ?int $eventId = null): PackageOverage
    {
        $rate = $isComp ? 0 : $this->overage_ticket_rate;
        $amount = $quantity * $rate;

        return $this->overages()->create([
            'event_id' => $eventId,
            'overage_type' => $isComp ? 'comp_tickets' : 'tickets',
            'quantity' => $quantity,
            'rate_per_unit' => $rate,
            'total_amount' => $amount,
            'status' => 'pending',
        ]);
    }

    // ===== DISPLAY HELPERS =====

    public function getDisplayNameAttribute(): string
    {
        if ($this->is_free_trial) {
            return '🎁 Free Trial (Standard Event)';
        }

        $definitions = self::getPackageDefinitions();
        return $definitions[$this->package_type]['name'] ?? ucfirst($this->package_type);
    }

    public function getSummaryAttribute(): string
    {
        return "{$this->display_name} - {$this->remaining_events} events, {$this->remaining_tickets} tickets, {$this->remaining_comp_tickets} comp remaining";
    }
}