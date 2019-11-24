<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedUser
{
    /**
     * Handle the event.
     *
     * @param Authenticated $event
     *
     * @throws ValidationException
     */
    public function handle(Authenticated $event)
    {
        if ($event->user->isInactive()) {
            Auth::logout();

            throw ValidationException::withMessages([
                $event->user->login => __('auth.inactive')
            ]);
        }

        if ($event->user->isExpired()) {
            Auth::logout();

            throw ValidationException::withMessages([
                $event->user->login => __('auth.inactive')
            ]);
        }
    }
}
