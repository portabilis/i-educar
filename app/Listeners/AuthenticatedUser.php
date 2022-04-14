<?php

namespace App\Listeners;

use App\Services\DisableUsersWithDaysGoneSinceLastAccessService;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedUser
{
    private $disableUsersWithDaysGoneSinceLastAccessService;

    public function __construct(DisableUsersWithDaysGoneSinceLastAccessService $disableUsersWithDaysGoneSinceLastAccessService)
    {
        $this->disableUsersWithDaysGoneSinceLastAccessService = $disableUsersWithDaysGoneSinceLastAccessService;
    }

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

        $this->validateUserExpirationPeriod($event->user);

        if ($event->user->isExpired()) {
            Auth::logout();

            throw ValidationException::withMessages([
                $event->user->login => __('auth.expired')
            ]);
        }
    }

    public function validateUserExpirationPeriod(Authenticatable $user)
    {
        $this->disableUsersWithDaysGoneSinceLastAccessService->execute($user);
    }
}
