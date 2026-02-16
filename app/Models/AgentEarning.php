<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgentEarning extends Model
{
    use HasFactory;
    protected $fillable = [
        'agent_id',
        'organization_id',
        'organization_package_id',
        'type',
        'amount',
        'package_price',
        'package_type',
        'milestone_tier',
        'milestone_org_count',
        'status',
        'approved_by',
        'approved_at',
        'paid_at',
        'payment_method',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'package_price' => 'decimal:2',
        'milestone_tier' => 'integer',
        'milestone_org_count' => 'integer',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(OrganizationPackage::class, 'organization_package_id');
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

    public function scopeCommissions($query)
    {
        return $query->where('type', 'commission');
    }

    public function scopeBonuses($query)
    {
        return $query->where('type', 'milestone_bonus');
    }

    // ===== HELPERS =====

    public function isCommission(): bool
    {
        return $this->type === 'commission';
    }

    public function isBonus(): bool
    {
        return $this->type === 'milestone_bonus';
    }

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

    public function cancel(string $reason = null): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'notes' => $reason ? "Cancelled: {$reason}" : 'Cancelled',
        ]);
    }

    // ===== STATIC HELPERS =====

    /**
     * Calculate 20% commission from package price
     */
    public static function calculateCommission(float $packagePrice): float
    {
        return round($packagePrice * 0.20, 2);
    }

    /**
     * Calculate milestone bonus: 300 + ((tier - 1) × 20)
     */
    public static function calculateMilestoneBonus(int $tier): float
    {
        return 300 + (($tier - 1) * 20);
    }

    /**
     * Get milestone tier from org count (5 orgs = tier 1, 10 = tier 2, etc.)
     */
    public static function getMilestoneTier(int $orgCount): ?int
    {
        // ONLY return a tier if it is exactly a multiple of 5
        if ($orgCount > 0 && $orgCount % 5 === 0) {
            return (int) ($orgCount / 5);
        }

        return null; // Return null if it's not a milestone count
    }
}