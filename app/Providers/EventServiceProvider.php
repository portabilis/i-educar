<?php

namespace App\Providers;

use App\Events\TransferEvent;
use App\Listeners\AuthenticatedUser;
use App\Listeners\TransferNotificationListener;
use App\Models\SchoolManager;
use App\Observers\SchoolManagerObserver;
use App\Listeners\LoginLegacySession;
use App\Listeners\NotificationWhenResetPassword;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\RegistrationEvent;
use App\Listeners\CopyTransferDataListener;
use App\Listeners\AcceptTransferRequestListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            LoginLegacySession::class,
        ],
        PasswordReset::class => [
            NotificationWhenResetPassword::class,
        ],
        Authenticated::class => [
            AuthenticatedUser::class
        ],
        RegistrationEvent::class => [
            CopyTransferDataListener::class,
            AcceptTransferRequestListener::class
        ],
        TransferEvent::class => [
            TransferNotificationListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        SchoolManager::observe(SchoolManagerObserver::class);
    }
}
