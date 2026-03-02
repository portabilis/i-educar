<?php

namespace App\Http\Controllers;

use App\Models\LegacyTransferRequest;
use App\Models\NotificationType;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class TransferWebhookCallbackController extends Controller
{
    public function __construct(
        public NotificationService $service
    ) {}

    public function __invoke(
        Request $request,
        int $id,
    ) {
        $transfer = LegacyTransferRequest::query()
            ->findOrFail($id);

        $registration = $transfer->oldRegistration;

        $message = sprintf(
            'Solicitação de envio dos lançamentos do(a) aluno(a) %s, concluída com sucesso. Os dados já estão disponíveis no i-Educar.',
            $registration->student->person->name,
        );

        $link = '/intranet/educar_matricula_det.php?cod_matricula=' . $registration->getKey();

        $this->service->createByUser(
            $transfer->ref_usuario_cad,
            $message,
            $link,
            NotificationType::TRANSFER
        );

        return response()->json([
            'message' => 'Notification created successfully',
        ]);
    }
}
