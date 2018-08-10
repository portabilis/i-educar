<?php

require_once 'Portabilis/Mailer.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Utils/User.php';
require_once 'Portabilis/Utils/Database.php';

class NotificationMailer extends Portabilis_Mailer
{
    public function unexpectedDataBaseError($appError, $pgError, $sql)
    {
        if ($this->canSendEmail()) {
            $lastError = error_get_last();
            $userId = Portabilis_Utils_User::currentUserId();
            $to = $this->notificationEmail();
            $subject = '[Erro inesperado bd] i-Educar - ' . $this->host();
            $trace = $this->stackTrace();

            $message = "Olá!\n\n" .
                "Ocorreu um erro inesperado no banco de dados, detalhes abaixo:\n\n" .
                "  ERRO APP: ' . $appError\n" .
                "  ERRO PHP: ' . {$lastError['message']}\n" .
                "  ERRO POSTGRES: $pgError\n" .
                "  LINHA {$lastError['line']} em {$lastError['file']}\n" .
                "  SQL: {$sql}\n" .
                "  ID USUÁRIO {$userId}\n" .
                "  TRACE: \n$trace\n" .
                "\n\n-\n\n" .
                'Você recebeu este email pois seu email foi configurado para receber ' .
                'notificações de erros.';

            return ($to ? $this->sendMail($to, $subject, $message) : false);
        }
    }

    public function unexpectedError($appError)
    {
        if ($this->canSendEmail()) {
            $lastError = error_get_last();
            $user = $this->tryLoadUser();
            $to = $this->notificationEmail();
            $subject = '[Erro inesperado] i-Educar - ' . $this->host();
            $trace = $this->stackTrace();

            $message = "Olá!\n\n" .
                "Ocorreu um erro inesperado, detalhes abaixo:\n\n" .
                "  ERRO APP: ' . $appError\n" .
                "  ERRO PHP: ' . {$lastError['message']}\n" .
                "  LINHA {$lastError['line']} em {$lastError['file']}\n" .
                "  USUÁRIO {$user['matricula']} email {$user['email']}\n" .
                "  TRACE: \n$trace\n" .
                "\n\n-\n\n" .
                'Você recebeu este email pois seu email foi configurado para receber ' .
                'notificações de erros.';

            return ($to ? $this->sendMail($to, $subject, $message) : false);
        }
    }

    protected function canSendEmail()
    {
        return (bool)$GLOBALS['coreExt']['Config']->modules->error->send_notification_email === true;
    }

    protected function notificationEmail()
    {
        $email = $GLOBALS['coreExt']['Config']->modules->error->notification_email;

        if (!is_string($email)) {
            error_log('Não foi definido um email para receber detalhes dos erros, por favor adicione a opção ' .
                '\'modules.error_notification.email = email@dominio.com\' ao arquivo ini de configuração.');

            return false;
        }

        return $email;
    }

    protected function tryLoadUser()
    {
        try {
            $sql = 'select matricula, email from portal.funcionario WHERE ref_cod_pessoa_fj = $1';
            $options = ['params' => Portabilis_Utils_User::currentUserId(), 'return_only' => 'first-row'];
            $user = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
        } catch (Exception $e) {
            $user = ['matricula' => 'Erro ao obter', 'email' => 'Erro ao obter'];
        }

        return $user;
    }

    protected function stackTrace()
    {
        return json_encode(debug_backtrace());
    }
}
