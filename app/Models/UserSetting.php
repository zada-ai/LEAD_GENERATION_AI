<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gemini_model',
        'enable_ai_analysis',
        'enable_ai_personalization',
        'default_daily_limit',
        'default_follow_up_days',
        'default_approval_required',
        'default_email_account_id',
    ];

    protected $casts = [
        'enable_ai_analysis' => 'boolean',
        'enable_ai_personalization' => 'boolean',
        'default_approval_required' => 'boolean',
        'default_daily_limit' => 'integer',
        'default_follow_up_days' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function defaultEmailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class, 'default_email_account_id');
    }
}
