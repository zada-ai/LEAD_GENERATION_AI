<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
    protected $fillable = [
        'email_account_id',
        'lead_id',
        'gmail_message_id',
        'gmail_thread_id',
        'message_id',
        'in_reply_to',
        'references',
        'from_email',
        'from_name',
        'to_email',
        'subject',
        'body_text',
        'body_html',
        'snippet',
        'direction',
        'received_at',
        'sent_at',
        'is_read',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the email account that owns this email.
     */
    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    /**
     * Get the lead associated with this email.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the conversation this email belongs to.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'gmail_thread_id', 'gmail_thread_id')
            ->where('email_account_id', $this->email_account_id);
    }
}
