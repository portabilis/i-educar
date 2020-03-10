<?php

namespace App\Listeners;

use App\Models\LegacyTransferRequest;
use App\Models\NotificationType;
use App\Process;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class TransferNotificationListener
{
    /**
     * @var NotificationService
     */
    protected $service;

    /**
     * @param NotificationService $service
     */
    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
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
            $registration->level->name,
            $registration->lastEnrollment->schoolClass->name,
            $registration->ano
        );

        $link = '/intranet/educar_matricula_det.php?cod_matricula=' . $registration->getKey();

        $users = $this->getUsers(Process::NOTIFY_TRANSFER, $registration->school->getKey());

        foreach ($users as $user) {
            $this->service->createByUser($user->cod_usuario, $message, $link, NotificationType::TRANSFER);
        }
    }

    public function getUsers($process, $school)
    {
        return DB::select(DB::raw('
            SELECT cod_usuario
              FROM pmieducar.usuario u
              JOIN pmieducar.menu_tipo_usuario mtu ON mtu.ref_cod_tipo_usuario = u.ref_cod_tipo_usuario
              JOIN pmieducar.tipo_usuario tu ON tu.cod_tipo_usuario = u.ref_cod_tipo_usuario
              JOIN public.menus m ON m.id = mtu.menu_id
         LEFT JOIN pmieducar.escola_usuario eu ON eu.ref_cod_usuario = u.cod_usuario
             WHERE m.process = :process
               AND u.ativo = 1
               AND (eu.ref_cod_escola = :school OR tu.nivel <= 2) --INSTITUCIONAL
        '), [
            $process,
            $school
        ]);
    }
}
