<?php

use App\Models\Mensagem;
use App\Models\NotificationType;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class ValidacaoEnviarMensagemController extends ApiCoreController
{
    public function enviarMensagem () {
        $mensagem = $this->getRequest()->mensagem;
        $registro_id = $this->getRequest()->registro_id;
        $typeValidation = $this->getRequest()->typeValidation;
        $receptor_user_id = $this->getRequest()->receptor_user_id;
        $url = $this->getRequest()->url;

        if (!empty($mensagem) && !empty($url) && is_numeric($typeValidation) && is_numeric($receptor_user_id) && is_numeric($registro_id)) {
            $emissor_user_id = Auth::id();

            $data = [
                'registro_id' => $registro_id,
                'emissor_user_id' => $emissor_user_id,
                'receptor_user_id' => $receptor_user_id,
                'texto' => $mensagem,
            ];

            $resultado = Mensagem::create($data);

            if ($resultado) {
                $notification = new NotificationService();
                $notification->createByUser(
                    $receptor_user_id,
                    "Você recebeu uma nova mensagem sobre a validação da aula.",
                    $url,
                    NotificationType::VALIDATION_CLASS,
                    $registro_id,
                    $emissor_user_id
                );

                return true;
            }
        }

        return false;
    }

    public function Gerar()
    {
        if ($this->isRequestFor('post', 'enviar-mensagem')) {
            $this->appendResponse($this->enviarMensagem());
        }
    }
}
