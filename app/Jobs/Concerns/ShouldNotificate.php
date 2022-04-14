<?php

namespace App\Jobs\Concerns;

use App\Models\NotificationType;
use App\Services\NotificationService;

trait ShouldNotificate
{
    public function notificateSuccess()
    {
        $message = $this->getSuccessMessage();

        $this->notificate($message);
    }

    public function notificateError()
    {
        $message = $this->getErrorMessage();

        $this->notificate($message);
    }

    public function notificate($message)
    {
        if ($this->user) {
            $this->notificateUser($message, $this->user);
        }
    }

    public function notificateUser($message, $user)
    {
        $this->getNotificationService()->createByUser(
            $user->getKey(),
            $message,
            $this->getNotificationUrl(),
            $this->getNotificationType(),
        );
    }

    public function getNotificationService(): NotificationService
    {
        return app(NotificationService::class);
    }

    public function getSuccessMessage()
    {
        return 'Processo executado com sucesso';
    }

    public function getErrorMessage()
    {
        return 'Não foi possível executar o processo';
    }

    public function getNotificationUrl()
    {
        return null;
    }

    public function getNotificationType()
    {
        return NotificationType::OTHER;
    }
}
