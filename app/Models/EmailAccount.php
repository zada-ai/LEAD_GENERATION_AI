<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailAccount extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'email',
        'display_name',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'scope',
        'status',
        'last_synced_at',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'token_expires_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the email account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the emails for this account.
     */
    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    /**
     * Get the conversations for this account.
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Check if token is expired.
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return false;
        }

        return now()->greaterThanOrEqualTo($this->token_expires_at);
    }

    /**
     * Get count of synced emails.
     */
    public function getSyncedEmailsCount(): int
    {
        return $this->emails()->count();
    }
}
