<?php

namespace App\Listeners;

use App\Events\ActiveLookingChanged;
use App\Models\NotificationType;
use App\Process;
use App\Services\NotificationService;
use App\Traits\HasNotificationUsers;

class ActiveLookingNotificationListener
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
     * @return void
     */
    public function handle(ActiveLookingChanged $event)
    {
        $activeLooking = $event->activeLooking;
        $registration = $activeLooking->registration;

        $action = match ($event->action) {
            ActiveLookingChanged::ACTION_CREATED => 'foi registrado em Busca Ativa',
            ActiveLookingChanged::ACTION_UPDATED => 'teve seu registro na Busca Ativa atualizado',
        };

        $message = sprintf(
            'O(a) aluno(a) %s, %s, %s, %s, %s %s.',
            $registration->student->person->name,
            $registration->school->name,
            $registration->grade->name,
            $registration->lastEnrollment->schoolClass->name,
            $registration->ano,
            $action
        );

        $link = '/intranet/educar_busca_ativa_cad.php?id=' . $activeLooking->getKey() . '&ref_cod_matricula=' . $registration->getKey();

        $users = $this->getUsers(Process::NOTIFY_ACTIVE_LOOKING, $registration->school->getKey());

        foreach ($users as $user) {
            $this->service->createByUser($user->cod_usuario, $message, $link, NotificationType::ACTIVE_LOOKING);
        }
    }
}
