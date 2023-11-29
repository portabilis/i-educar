<?php

namespace App\Providers;

use App\Events\RegistrationCopyEvent;
use App\Events\RegistrationEvent;
use App\Events\ReportIssued;
use App\Events\TransferEvent;
use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Listeners\AcceptTransferRequestListener;
use App\Listeners\AuthenticatedUser;
use App\Listeners\ConfigureAuthenticatedUserForAudit;
use App\Listeners\CopyTransferDataListener;
use App\Listeners\ForgetCachedUserListener;
use App\Listeners\LoginLegacySession;
use App\Listeners\MessageSendingListener;
use App\Listeners\NotificationWhenResetPassword;
use App\Listeners\RegistrationCopyListener;
use App\Listeners\ReportIssuedListener;
use App\Listeners\TransferNotificationListener;
use App\Models\LegacyRegistrationDisciplinaryOccurrenceType;
use App\Models\SchoolManager;
use App\Observers\LegacyRegistrationDisciplinaryOccurrenceTypeObserver;
use App\Observers\SchoolManagerObserver;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSending;

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
            AuthenticatedUser::class,
            ConfigureAuthenticatedUserForAudit::class,
        ],
        RegistrationEvent::class => [
            CopyTransferDataListener::class,
            AcceptTransferRequestListener::class,
        ],
        RegistrationCopyEvent::class => [
            RegistrationCopyListener::class,
        ],
        TransferEvent::class => [
            TransferNotificationListener::class,
        ],
        MessageSending::class => [
            MessageSendingListener::class,
        ],
        UserUpdated::class => [
            ForgetCachedUserListener::class,
        ],
        UserDeleted::class => [
            ForgetCachedUserListener::class,
        ],
        ReportIssued::class => [
            ReportIssuedListener::class,
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
        LegacyRegistrationDisciplinaryOccurrenceType::observe(LegacyRegistrationDisciplinaryOccurrenceTypeObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
