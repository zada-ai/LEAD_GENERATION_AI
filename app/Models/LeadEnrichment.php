<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadEnrichment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'description',
        'detected_services',
        'website_summary',
        'technology_info',
        'opportunity_notes',
        'enriched_at',
    ];

    protected $casts = [
        'detected_services' => 'array',
        'technology_info' => 'array',
        'enriched_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
