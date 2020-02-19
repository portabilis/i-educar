<?php

namespace App\Listeners;

use App\Exceptions\Transfer\MissingDescriptiveOpinionType;
use App\Exceptions\Transfer\StagesAreNotSame;
use App\Models\LegacyTransferRequest;
use App\Models\NotificationType;
use App\Process;
use App\Services\NotificationsService;

class TransferNotificationListener
{
    /**
     * @var NotificationsService
     */
    protected $service;

    /**
     * @param NotificationsService $service
     */
    public function __construct(NotificationsService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @throws MissingDescriptiveOpinionType
     * @throws StagesAreNotSame
     *
     * @return void
     */
    public function handle($event)
    {
        /** @var LegacyTransferRequest $transfer */
        $transfer = $event->transfer;
        $registration = $transfer->oldRegistration;

        $message = sprintf('O(a) aluno(a) %s, %s, %s, %s, %s foi transferido(a) da rede.',
            $registration->student->person->name,
            $registration->school->name,
            $registration->level->name,
            $registration->lastEnrollment->schoolClass->name,
            $registration->ano
        );

        $link = 'intranet/educar_matricula_det.php?cod_matricula=' . $registration->getKey();

        $this->service->createByPermission(Process::NOTIFY_TRANSFER, $message, $link, NotificationType::TRANSFERENCIA);
    }
}
