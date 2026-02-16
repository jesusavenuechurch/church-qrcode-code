<?php
// app/Models/AgentBonus.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentBonus extends Model
{
    protected $fillable = [
        'agent_id',
        'milestone_tier',
        'organizations_count',
        'bonus_amount',
        'status',
        'approved_by',
        'approved_at',
        'paid_at',
        'payment_method',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'milestone_tier' => 'integer',
        'organizations_count' => 'integer',
        'bonus_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ===== SCOPES =====

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ===== HELPERS =====

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function approve(int $userId): bool
    {
        return $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function markAsPaid(string $paymentMethod, string $paymentReference): bool
    {
        return $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
        ]);
    }

    /**
     * Calculate bonus amount for a given tier
     * Formula: 300 + ((tier - 1) × 20)
     */
    public static function calculateBonusAmount(int $tier): float
    {
        return 300 + (($tier - 1) * 20);
    }

    /**
     * Get tier from organization count
     * e.g., 5 orgs = tier 1, 10 orgs = tier 2, etc.
     */
    public static function getTierFromOrgCount(int $orgCount): int
    {
        return (int) floor($orgCount / 5);
    }
}