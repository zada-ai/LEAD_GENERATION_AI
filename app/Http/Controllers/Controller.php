<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

abstract class Controller
{
    /**
     * Authorize an action for the current user.
     *
     * @throws AuthorizationException
     */
    protected function authorize(string $ability, mixed $arguments): mixed
    {
        return Gate::authorize($ability, $arguments);
    }
}
