<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'lead_id',
        'email_account_id',
        'email_id',
        'subject',
        'body',
        'status',
        'approved_at',
        'sent_at',
        'failed_at',
        'error_message',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function email(): BelongsTo
    {
        return $this->belongsTo(Email::class);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'sent' => 'bg-green-100 text-green-800',
            'replied' => 'bg-emerald-100 text-emerald-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'pending_approval' => 'bg-yellow-100 text-yellow-800',
            'queued' => 'bg-indigo-100 text-indigo-800',
            'failed' => 'bg-red-100 text-red-800',
            'skipped' => 'bg-gray-100 text-gray-800',
            'draft' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
