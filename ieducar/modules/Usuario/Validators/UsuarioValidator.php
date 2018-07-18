<?php

class UsuarioValidator
{
    public static function validatePassword(
        $messenger,
        $oldPassword,
        $newPassword,
        $confirmation,
        $encriptedPassword,
        $matricula
    ) {
        $newPassword  = strtolower($newPassword);
        $confirmation = strtolower($confirmation);

        if (empty($newPassword)) {
            $messenger->append('Por favor informe uma senha.', 'error');
        } elseif (strlen($newPassword) < 8) {
            $messenger->append('Por favor informe uma senha mais segura, com pelo menos 8 caracteres.', 'error');
        } elseif ($newPassword != $confirmation) {
            $messenger->append('A confirma&ccedil;&atilde;o de senha n&atilde;o confere com a senha.', 'error');
        } elseif (strpos($newPassword, $matricula) != false) {
            $messenger->append('A senha informada &eacute; similar a sua matricula, informe outra senha.', 'error');
        } elseif ($encriptedPassword == $oldPassword) {
            $messenger->append('Informe uma senha diferente da atual.', 'error');
        }

        return ! $messenger->hasMsgWithType('error');
    }
}
