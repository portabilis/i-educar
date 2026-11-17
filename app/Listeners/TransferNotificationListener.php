<?php

namespace App\Listeners;

use App\Models\LegacyTransferRequest;
use App\Models\NotificationType;
use App\Process;
use App\Services\NotificationService;
use App\Traits\HasNotificationUsers;
use App\User;

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

        $this->notifyDestinationSchool($transfer, array_column($users, 'cod_usuario'));
    }

    private function notifyDestinationSchool(LegacyTransferRequest $transfer, array $notifiedUsers): void
    {
        $school = $transfer->destinationSchool;
        $registration = $transfer->oldRegistration;

        if (!$school || $transfer->ref_cod_matricula_entrada ||
            (int) $school->ref_cod_instituicao !== (int) $registration->school->ref_cod_instituicao) {
            return;
        }

        $users = $this->getUsers(Process::NOTIFY_TRANSFER, $school->getKey());
        $recipients = User::query()
            ->whereIn('cod_usuario', array_column($users, 'cod_usuario'))
            ->whereNotIn('cod_usuario', $notifiedUsers)
            ->where('ref_cod_instituicao', $school->ref_cod_instituicao)
            ->whereHas('schools', fn ($query) => $query->where('cod_escola', $school->getKey()))
            ->get()
            ->filter(fn (User $user) => $user->can('modify', Process::REGISTRATIONS));

        $message = sprintf(
            'O(a) aluno(a) %s possui uma transferência de %s para %s aguardando recebimento.',
            $registration->student->person->name,
            $registration->school->name,
            $school->name
        );
        $link = '/intranet/educar_index.php';

        foreach ($recipients as $user) {
            $this->service->createByUser($user->getKey(), e($message), $link, NotificationType::TRANSFER);
        }
    }
}
