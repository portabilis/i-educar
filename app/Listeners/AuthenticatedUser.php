<?php

namespace App\Listeners;

use App\Services\DisableUsersWithDaysGoneSinceLastAccessService;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Portabilis_Date_Utils;

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
     *
     * @throws ValidationException
     */
    public function handle(Authenticated $event)
    {
        if ($event->user->isInactive()) {
            Auth::logout();

            throw ValidationException::withMessages([
                $event->user->login => $event->user->employee->motivo ?: __('auth.inactive'),
            ]);
        }

        if ($event->user->isNotStarted()) {
            Auth::logout();

            throw ValidationException::withMessages([
                $event->user->login => __('auth.not_started', ['date' => Portabilis_Date_Utils::pgSQLToBr($event->user->employee->data_inicial)]),
            ]);
        }

        $this->validateUserExpirationPeriod($event->user);

        if ($event->user->isExpired()) {
            Auth::logout();

            throw ValidationException::withMessages([
                $event->user->login => __('auth.expired'),
            ]);
        }
    }

    public function validateUserExpirationPeriod(Authenticatable $user)
    {
        $this->disableUsersWithDaysGoneSinceLastAccessService->execute($user);
    }
}
