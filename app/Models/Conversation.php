<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'lead_id',
        'email_account_id',
        'gmail_thread_id',
        'subject',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the lead associated with this conversation.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the email account associated with this conversation.
     */
    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    /**
     * Get the emails in this conversation.
     */
    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'gmail_thread_id', 'gmail_thread_id')
            ->orderByRaw('COALESCE(received_at, sent_at, created_at) asc');
    }

    /**
     * Get the latest email in this conversation.
     */
    public function latestEmail(): ?Email
    {
        return $this->emails()->reorder()->orderByRaw('COALESCE(received_at, sent_at, created_at) desc')->first();
    }

    /**
     * Mark conversation as read.
     */
    public function markAsRead(): void
    {
        $this->emails()->update(['is_read' => true]);
    }

    /**
     * Check if conversation has unread emails.
     */
    public function hasUnread(): bool
    {
        return $this->emails()->where('is_read', false)->exists();
    }
}
