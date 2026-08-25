<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'subject',
        'body',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
