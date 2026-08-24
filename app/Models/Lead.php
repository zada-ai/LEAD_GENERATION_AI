<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    'last_contacted_at',
    'next_follow_up_at',
    'created_by',
])]
class Lead extends Model
{
    /** @use HasFactory */
    use HasFactory;

    protected $casts = [
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
