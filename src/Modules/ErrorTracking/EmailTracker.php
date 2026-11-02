<?php

namespace iEducar\Modules\ErrorTracking;

use App\Mail\ErrorTrackerMail;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler;
use Throwable;

class EmailTracker implements Tracker
{
    public function notify(Throwable $exception, $data = [])
    {
        $subject = '[Erro inesperado] i-Educar - ' . config('app.name');

        $to = $this->getRecipient();

        try {
            // O componente de depuração do Symfony não está instalado: a linha
            // abaixo lança Error e o restante do bloco não é executado.
            $e = FlattenException::create($exception);

            // @codeCoverageIgnoreStart
            $handler = new ExceptionHandler;

            $html = $handler->getHtml($e);

            $mail = new ErrorTrackerMail($to, $subject, $html);

            Mail::send($mail);
            // @codeCoverageIgnoreEnd
        } catch (Throwable) {
            //
        }
    }

    private function getRecipient()
    {
        return config('legacy.modules.error.email_recipient');
    }
}
