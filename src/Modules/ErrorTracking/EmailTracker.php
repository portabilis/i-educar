<?php

namespace iEducar\Modules\ErrorTracking;

use Exception;
use Throwable;

class EmailTracker implements Tracker
{
    public function notify(Throwable $exception, $data = [])
    {
        $message = $this->getStringMessage($exception, $data);
        $subject = '[Erro inesperado] i-Educar - ' . $this->getHost();
        $to = $this->getRecipient();

        (new \Portabilis_Mailer())->sendMail($to, $subject, $message);
    }

    private function getStringMessage(Throwable $exception, array $data)
    {
        $data = $this->getStringData($data);
        $trace = $this->getStringTrace();

        return "Olá!\n\n" .
            "Ocorreu um erro inesperado no banco de dados, detalhes abaixo:\n\n" .
            "  ERRO: ' {$exception->getMessage()}\n" .
            "  LINHA {$exception->getLine()} em {$exception->getFile()}\n" .
            "  DADOS: {$data}\n" .
            "  TRACE: \n{$trace}\n" .
            "\n\n-\n\n" .
            'Você recebeu este email pois seu email foi configurado para receber ' .
            'notificações de erros.';
    }

    private function getStringTrace()
    {
        return json_encode(debug_backtrace());
    }

    private function getStringData($data)
    {
        return json_encode($data);
    }

    protected function getHost()
    {
        return $_SERVER['HTTP_HOST'] ?? null;
    }

    private function getRecipient()
    {
        $recipient = $GLOBALS['coreExt']['Config']->modules->error->email_recipient;

        if (empty($recipient)) {
            throw new Exception('Parâmetro modules.error.email_recipient não encontrado');
        }

        return $recipient;
    }
}