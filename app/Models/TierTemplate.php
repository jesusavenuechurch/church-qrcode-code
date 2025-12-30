<?php

// app/Models/TierTemplate.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TierTemplate extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'tiers_config', // JSON array of tier configs
        'is_default',
    ];

    protected $casts = [
        'tiers_config' => 'array',
        'is_default' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Apply this template to an event
     */
    public function applyToEvent(Event $event): int
    {
        $created = 0;

        foreach ($this->tiers_config as $tierConfig) {
            EventTier::create([
                'event_id' => $event->id,
                'tier_name' => $tierConfig['tier_name'],
                'description' => $tierConfig['description'] ?? null,
                'price' => $tierConfig['price'],
                'color' => $tierConfig['color'] ?? null,
                'quantity_available' => $tierConfig['quantity_available'] ?? null,
                'is_active' => true,
            ]);
            $created++;
        }

        return $created;
    }

    /**
     * Create template from existing event tiers
     */
    public static function createFromEvent(Event $event, string $templateName): ?self
    {
        $tiersConfig = $event->tiers->map(fn ($tier) => [
            'tier_name' => $tier->tier_name,
            'description' => $tier->description,
            'price' => $tier->price,
            'color' => $tier->color,
            'quantity_available' => $tier->quantity_available,
        ])->toArray();

        return self::create([
            'organization_id' => $event->organization_id,
            'name' => $templateName,
            'description' => "Template created from event: {$event->name}",
            'tiers_config' => $tiersConfig,
            'is_default' => false,
        ]);
    }
}