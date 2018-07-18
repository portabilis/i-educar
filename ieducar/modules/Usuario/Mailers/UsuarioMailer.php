<?php

require_once 'lib/Portabilis/Mailer.php';

class UsuarioMailer extends Portabilis_Mailer
{
    public function updatedPassword($user, $link)
    {
        $to = $user->email;
        $subject = 'Sua senha foi alterada - i-Educar - ' . $this->host();

        $message = "Olá!\n\n" .
                   "A senha da matrícula '{$user->matricula}' foi alterada recentemente.\n\n" .
                   'Caso você não tenha feito esta alteração, por favor, tente alterar sua senha ' .
                   "acessando o link $link ou entre em contato com o administrador do sistema " .
                   '(solicitando mudança da sua senha), pois sua conta pode estar sendo usada ' .
                   'por alguma pessoa não autorizada.';

        return $this->sendMail($to, $subject, $message);
    }

    public function passwordReset($user, $link)
    {
        $to = $user->email;
        $subject = 'Redefinição de senha - i-Educar - ' . $this->host();

        $message = "Olá!\n\n" .
                   "Recebemos uma solicitação de redefinição de senha para a matrícula {$user->matricula}.\n\n" .
                   "Para redefinir sua senha acesse o link: $link\n\n" .
                   'Caso você não tenha feito esta solicitação, por favor, ignore esta mensagem.';

        return $this->sendMail($to, $subject, $message);
    }
}
