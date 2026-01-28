<?php
// app/Models/Agent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Agent extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'referral_token',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Organizations referred by this agent
     */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    /**
     * Admin who created this agent
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ===== HELPERS =====

    /**
     * Get the public registration URL for this agent
     */
    public function getRegistrationUrlAttribute(): string
    {
        return url("/org/register/{$this->referral_token}");
    }

    /**
     * Count of active organizations referred
     */
    public function getActiveOrganizationsCountAttribute(): int
    {
        return $this->organizations()->where('is_active', true)->count();
    }

    /**
     * Count of total organizations referred
     */
    public function getTotalReferralsAttribute(): int
    {
        return $this->organizations()->count();
    }

    // ===== HOOKS =====

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($agent) {
            // Auto-generate referral token if not provided
            if (empty($agent->referral_token)) {
                $agent->referral_token = static::generateUniqueToken();
            }
        });
    }

    /**
     * Generate unique referral token
     */
    protected static function generateUniqueToken(): string
    {
        do {
            $token = 'AG-' . strtoupper(Str::random(6));
        } while (static::where('referral_token', $token)->exists());

        return $token;
    }
}