<?php

namespace App\Listeners;

use App\Models\LegacyTransferRequest;
use App\Models\NotificationType;
use App\Process;
use App\Services\NotificationService;
use App\Traits\HasNotificationUsers;

class TransferNotificationListener
{
    use HasNotificationUsers;

    /**
     * @var NotificationService
     */
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        /** @var LegacyTransferRequest $transfer */
        $transfer = $event->transfer;
        $registration = $transfer->oldRegistration;

        $message = sprintf(
            'O(a) aluno(a) %s, %s, %s, %s, %s foi transferido(a) da rede.',
            $registration->student->person->name,
            $registration->school->name,
            $registration->grade->name,
            $registration->lastEnrollment->schoolClass->name,
            $registration->ano
        );

        $link = '/intranet/educar_matricula_det.php?cod_matricula=' . $registration->getKey();

        $users = $this->getUsers(Process::NOTIFY_TRANSFER, $registration->school->getKey());

        foreach ($users as $user) {
            $this->service->createByUser($user->cod_usuario, $message, $link, NotificationType::TRANSFER);
        }
    }
}
