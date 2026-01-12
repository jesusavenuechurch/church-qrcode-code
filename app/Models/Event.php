<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'event_date',
        'duration_days',
        'location',
        'capacity',
        'status',
        'slug',
        'is_public',
        'tagline',
        'venue',
        'registration_deadline',
        'allow_installments',
        'minimum_deposit_percentage',
        'installment_instructions',
        'banner_image',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'registration_deadline' => 'datetime',

        // ✅ NEW
        'allow_installments' => 'boolean',
        'minimum_deposit_percentage' => 'decimal:2',
    ];

    /* ------------------------------------------------------------
     | Boot
     ------------------------------------------------------------ */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = static::generateUniqueSlug(
                    $event->name,
                    $event->organization_id
                );
            }
        });

        static::updating(function ($event) {
            if ($event->isDirty('name') && ! $event->isDirty('slug')) {
                $event->slug = static::generateUniqueSlug(
                    $event->name,
                    $event->organization_id
                );
            }
        });
    }

    protected static function generateUniqueSlug($name, $organizationId)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (
            static::where('slug', $slug)
                ->where('organization_id', $organizationId)
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /* ------------------------------------------------------------
     | Accessors
     ------------------------------------------------------------ */

    public function getPublicUrlAttribute(): ?string
    {
        if ($this->organization && $this->slug && $this->organization->slug) {
            return route('public.event', [
                'orgSlug' => $this->organization->slug,
                'eventSlug' => $this->slug,
            ]);
        }

        return null;
    }

    /**
     * Helper for deposits (used later in controllers / views)
     */
    public function requiresDeposit(): bool
    {
        return $this->allow_installments === true
            && ! is_null($this->minimum_deposit_percentage);
    }

    /* ------------------------------------------------------------
     | Relationships
     ------------------------------------------------------------ */

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(EventTier::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function checkins()
    {
        return $this->tickets()
            ->whereNotNull('checked_in_at');
    }
}