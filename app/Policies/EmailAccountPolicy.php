<?php

namespace App\Policies;

use App\Models\EmailAccount;
use App\Models\User;

class EmailAccountPolicy
{
    /**
     * Determine if user can view the email account.
     */
    public function view(User $user, EmailAccount $emailAccount): bool
    {
        return $user->id === $emailAccount->user_id;
    }

    /**
     * Determine if user can update the email account.
     */
    public function update(User $user, EmailAccount $emailAccount): bool
    {
        return $user->id === $emailAccount->user_id;
    }

    /**
     * Determine if user can delete the email account.
     */
    public function delete(User $user, EmailAccount $emailAccount): bool
    {
        return $user->id === $emailAccount->user_id;
    }
}
