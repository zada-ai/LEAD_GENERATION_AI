<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'business_name',
    'contact_name',
    'email',
    'phone',
    'website',
    'facebook_url',
    'instagram_url',
    'linkedin_url',
    'country',
    'state',
    'city',
    'area',
    'category',
    'service_interest',
    'source',
    'status',
    'priority',
    'notes',
    'ai_score',
    'ai_priority',
    'ai_service',
    'ai_summary',
    'ai_analyzed_at',
    'last_outbound_at',
    'last_contacted_at',
    'next_follow_up_at',
    'created_by',
])]
class Lead extends Model
{
    /** @use HasFactory */
    use HasFactory;

    protected $casts = [
        'ai_score' => 'integer',
        'ai_analyzed_at' => 'datetime',
        'last_outbound_at' => 'datetime',
        'last_contacted_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this lead.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Lead enrichment details
     */
    public function enrichment(): HasOne
    {
        return $this->hasOne(LeadEnrichment::class);
    }

    /**
     * Lead suppression record
     */
    public function suppression(): HasOne
    {
        return $this->hasOne(LeadSuppression::class);
    }

    /**
     * Campaign messages sent to this lead
     */
    public function campaignMessages(): HasMany
    {
        return $this->hasMany(CampaignMessage::class);
    }

    /**
     * Follow ups for this lead
     */
    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Activity logs for this lead
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Emails associated with this lead
     */
    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    /**
     * Conversations associated with this lead
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Check if this lead is suppressed or marked do not contact
     */
    public function isSuppressed(): bool
    {
        if ($this->status === 'do_not_contact') {
            return true;
        }

        return LeadSuppression::where('email', $this->email)
            ->orWhere('lead_id', $this->id)
            ->exists();
    }

    /**
     * Get AI Score Badge color
     */
    public function getAiScoreBadgeColorAttribute(): string
    {
        if ($this->ai_score === null) {
            return 'bg-gray-100 text-gray-700';
        }

        return match (true) {
            $this->ai_score >= 85 => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            $this->ai_score >= 70 => 'bg-green-100 text-green-800 border-green-300',
            $this->ai_score >= 40 => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            default => 'bg-red-100 text-red-800 border-red-300',
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'bg-blue-100 text-blue-800',
            'contacted' => 'bg-purple-100 text-purple-800',
            'interested' => 'bg-green-100 text-green-800',
            'follow_up' => 'bg-yellow-100 text-yellow-800',
            'converted' => 'bg-emerald-100 text-emerald-800',
            'not_interested' => 'bg-red-100 text-red-800',
            'do_not_contact' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'bg-red-100 text-red-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'low' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
