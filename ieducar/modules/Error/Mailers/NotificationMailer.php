<?php

/**
 * i-Educar - Sistema de gestÃ­Â£o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de ItajaÃ­Â­
 *           <ctima@itajai.sc.gov.br>
 *
 * Este programa Ã© software livre; você pode redistribuÃ­-lo e/ou modificá-lo
 * sob os termos da LicenÃ§a PÃºblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da LicenÃ§a, como (a seu critÃ©rio)
 * qualquer versão posterior.
 *
 * Este programa Ã© distribuÃ­Â­do na expectativa de que seja Ãºtil, porÃ©m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implÃ­Â­cita de COMERCIABILIDADE OU
 * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereÃ§o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Mailer
 * @subpackage  Modules
 * @since     Arquivo disponÃ­Â­vel desde a versão ?
 * @version   $Id$
 */

require_once 'Portabilis/Mailer.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Utils/User.php';
require_once 'Portabilis/Utils/Database.php';

class NotificationMailer extends Portabilis_Mailer
{
  static function unexpectedDataBaseError($appError, $pgError, $sql) {
    if (self::canSendEmail()) {
      $lastError = error_get_last();
      $userId = Portabilis_Utils_User::currentUserId();

      $to      = self::notificationEmail();
      $subject = "[Erro inesperado bd] i-Educar - " . self::host();
      $trace = self::stackTrace();
      $message = "Olá!\n\n"                                                             .
                 "Ocorreu um erro inesperado no banco de dados, detalhes abaixo:\n\n"   .
                 "  ERRO APP: ' . $appError\n"                                          .
                 "  ERRO PHP: ' . {$lastError['message']}\n"                            .
                 "  ERRO POSTGRES: $pgError\n"                                          .
                 "  LINHA {$lastError['line']} em {$lastError['file']}\n"               .
                 "  SQL: {$sql}\n"                                                      .
                 "  ID USUÁRIO {$userId}\n"                                             .
                 "  TRACE: \n $trace \n"                                                .
                 "\n\n-\n\n"                                                            .
                 "Você recebeu este email pois seu email foi configurado para receber " .
                 "notificações de erros.";

      // only send email, if a notification email was set.
      return ($to ? self::mail($to, $subject, $message) : false);
    }
  }

  static function unexpectedError($appError) {
    if (self::canSendEmail()) {
      $lastError = error_get_last();
      $user      = self::tryLoadUser();

      $to      = self::notificationEmail();
      $subject = "[Erro inesperado] i-Educar - " . self::host();
      $trace = self::stackTrace();
      $message = "Olá!\n\n"                                                             .
                 "Ocorreu um erro inesperado, detalhes abaixo:\n\n"                     .
                 "  ERRO APP: ' . $appError\n"                                          .
                 "  ERRO PHP: ' . {$lastError['message']}\n"                            .
                 "  LINHA {$lastError['line']} em {$lastError['file']}\n"               .
                 "  USUÁRIO {$user['matricula']} email {$user['email']}\n"              .
                 "  TRACE: \n $trace \n"                                                .
                 "\n\n-\n\n"                                                            .
                 "Você recebeu este email pois seu email foi configurado para receber " .
                 "notificações de erros.";

      // only send email, if a notification email was set.
      return ($to ? self::mail($to, $subject, $message) : false);
    }
  }

  // common error mailer methods

  protected static function canSendEmail() {
    return $GLOBALS['coreExt']['Config']->modules->error->send_notification_email == true;
  }

  protected static function notificationEmail() {
  $email = $GLOBALS['coreExt']['Config']->modules->error->notification_email;

    if (! is_string($email)) {
      error_log("Não foi definido um email para receber detalhes dos erros, por favor adicione a opção " .
                "'modules.error_notification.email = email@dominio.com' ao arquivo ini de configuração.");

      return false;
    }

    return $email;
  }

  protected static function tryLoadUser() {
    try {

      $sql     = 'select matricula, email from portal.funcionario WHERE ref_cod_pessoa_fj = $1 ';
      $options = array('params' => Portabilis_Utils_User::currentUserId(), 'return_only' => 'first-row');
      $user    = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

    } catch (Exception $e) {
      $user = array('matricula' => 'Erro ao obter', 'email' => 'Erro ao obter');
    }

    return $user;
  }

  protected static function stackTrace() {
        $stack = debug_backtrace();
        $output = '';

        $stackLen = count($stack);
        for ($i = 1; $i < $stackLen; $i++) {
            $entry = $stack[$i];

            $func = $entry['function'] . '(';
            $argsLen = count($entry['args']);
            for ($j = 0; $j < $argsLen; $j++) {
                $func .= $entry['args'][$j];
                if ($j < $argsLen - 1) $func .= ', ';
            }
            $func .= ')';

            $output .= '#' . ($i - 1) . ' ' . $entry['file'] . ':' . $entry['line'] . ' - ' . $func . PHP_EOL;
        }

        return $output;
  }
}