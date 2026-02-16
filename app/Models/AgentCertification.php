<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentCertification extends Model
{
    protected $fillable = [
        'agent_id',
        'score',
        'total_questions',
        'passed',
        'attempts',
        'completed_at',
        'last_attempted_at',
    ];

    protected $casts = [
        'passed'            => 'boolean',
        'completed_at'      => 'datetime',
        'last_attempted_at' => 'datetime',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function getScorePercentageAttribute(): int
    {
        if ($this->total_questions === 0) return 0;
        return (int) round(($this->score / $this->total_questions) * 100);
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->passed) return 'Certified';
        if ($this->attempts > 0) return 'Failed';
        return 'Not Started';
    }
}