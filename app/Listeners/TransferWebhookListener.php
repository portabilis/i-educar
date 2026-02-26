<?php

namespace App\Listeners;

use App\Events\TransferEvent;
use App\Models\LegacyInstitution;
use App\Models\NotificationType;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Http;

class TransferWebhookListener
{
    public function __construct(
        public NotificationService $service
    ) {
        //
    }

    public function handle(TransferEvent $event): void
    {
        $registration = $event->transfer->oldRegistration;

        $configs = LegacyInstitution::find(
            $registration->school->institution->getKey()
        )->generalConfiguration;

        if (!$configs || empty($configs->url_novo_educacao)) {
            return;
        }

        $callbackUrl = route('webhook.transfer.callback', ['id' => $event->transfer->getKey()]);

        $response = Http::withHeader('token', trim($configs->token_novo_educacao))
            ->post(trim($configs->url_novo_educacao, '/') . '/api/v2/ieducar_api_student_transfers', [
                'student_enrollment_api_code' => $registration->getKey(),
                'callback_url' => $callbackUrl,
            ]);

        if ($response->successful()) {
            $message = sprintf(
                'Solicitação de envio dos lançamentos do(a) aluno(a) %s, enviada para o i-Diário. Em até 5 minutos os dados estarão disponíveis no i-Educar.',
                $registration->student->person->name,
            );

            $link = '/intranet/educar_matricula_det.php?cod_matricula=' . $registration->getKey();

            $this->service->createByUser(
                $event->transfer->ref_usuario_cad,
                $message,
                $link,
                NotificationType::TRANSFER
            );
        }
    }
}
