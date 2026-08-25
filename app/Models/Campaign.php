<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_account_id',
        'name',
        'description',
        'service',
        'status',
        'daily_limit',
        'approval_required',
        'filter_criteria',
        'follow_up_days',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'approval_required' => 'boolean',
        'filter_criteria' => 'array',
        'daily_limit' => 'integer',
        'follow_up_days' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function template(): HasOne
    {
        return $this->hasOne(CampaignTemplate::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CampaignMessage::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'running' => 'bg-green-100 text-green-800',
            'scheduled' => 'bg-blue-100 text-blue-800',
            'paused' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-purple-100 text-purple-800',
            'draft' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
