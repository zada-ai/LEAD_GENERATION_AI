<?php

namespace App\Providers;

use App\Models\Conversation;
use App\Models\EmailAccount;
use App\Policies\ConversationPolicy;
use App\Policies\EmailAccountPolicy;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        EmailAccount::class => EmailAccountPolicy::class,
        Conversation::class => ConversationPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
