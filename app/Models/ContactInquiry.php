<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactInquiry extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'subject',
        'message',
        'status',
        'handled_by',
        'handled_at',
        'notes',
    ];

    protected $casts = [
        'handled_at' => 'datetime',
    ];

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function markAsRead(int $userId): void
    {
        if ($this->status === 'new') {
            $this->update([
                'status' => 'read',
                'handled_by' => $userId,
                'handled_at' => now(),
            ]);
        }
    }
}