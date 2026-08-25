<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lead_id',
        'campaign_id',
        'type',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'lead_created' => '👤',
            'lead_updated' => '✏️',
            'ai_analyzed' => '🤖',
            'email_generated' => '📝',
            'email_approved' => '✅',
            'email_sent' => '📤',
            'email_replied' => '💬',
            'lead_converted' => '🎉',
            'lead_suppressed' => '🚫',
            'followup_created' => '⏰',
            'followup_completed' => '✔️',
            default => '📋',
        };
    }
}
