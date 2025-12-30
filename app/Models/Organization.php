<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'description', 'website', 'logo_path', 'is_active', 
    'slug', 'tagline', 'contact_email',];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($organization) {
            if (empty($organization->slug)) {
                $organization->slug = static::generateUniqueSlug($organization->name);
            }
        });

        static::updating(function ($organization) {
            if ($organization->isDirty('name') && !$organization->isDirty('slug')) {
                $organization->slug = static::generateUniqueSlug($organization->name);
            }
        });
    }

    protected static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function partners(): HasMany
    {
        return $this->hasMany(Partner::class, 'organization_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'organization_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'organization_id');
    }

    public function paymentMethods()
    {
        return $this->hasMany(OrganizationPaymentMethod::class);
    }
}