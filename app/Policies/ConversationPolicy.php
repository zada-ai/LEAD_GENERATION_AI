<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    /**
     * Determine if user can view the conversation.
     */
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->emailAccount->user_id === $user->id;
    }

    /**
     * Determine if user can update the conversation.
     */
    public function update(User $user, Conversation $conversation): bool
    {
        return $conversation->emailAccount->user_id === $user->id;
    }

    /**
     * Determine if user can delete the conversation.
     */
    public function delete(User $user, Conversation $conversation): bool
    {
        return $conversation->emailAccount->user_id === $user->id;
    }
}
