<?php
// app/Models/PackageOverage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageOverage extends Model
{
    protected $fillable = [
        'organization_package_id',
        'event_id',
        'overage_type',
        'quantity',
        'rate_per_unit',
        'total_amount',
        'accepted',
        'accepted_at',
        'accepted_by',
        'status',
        'paid_at',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'rate_per_unit' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'accepted' => 'boolean',
        'accepted_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    public function package(): BelongsTo
    {
        return $this->belongsTo(OrganizationPackage::class, 'organization_package_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    // ===== SCOPES =====

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ===== ACTIONS =====

    public function accept(int $userId): void
    {
        $this->update([
            'accepted' => true,
            'accepted_at' => now(),
            'accepted_by' => $userId,
            'status' => 'accepted',
        ]);
    }

    public function decline(int $userId): void
    {
        $this->update([
            'status' => 'declined',
            'notes' => "Declined by user {$userId}",
        ]);
    }

    public function markAsPaid(string $paymentReference): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_reference' => $paymentReference,
        ]);
    }
}