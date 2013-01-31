<?php

/**
 * i-Educar - Sistema de gest√≠¬£o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja√≠¬≠
 *           <ctima@itajai.sc.gov.br>
 *
 * Este programa √© software livre; vocÍ pode redistribu√≠-lo e/ou modific·-lo
 * sob os termos da Licen√ßa P√∫blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers„o 2 da Licen√ßa, como (a seu crit√©rio)
 * qualquer vers„o posterior.
 *
 * Este programa √© distribu√≠¬≠do na expectativa de que seja √∫til, por√©m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl√≠¬≠cita de COMERCIABILIDADE OU
 * ADEQUA√á√ÉO A UMA FINALIDADE ESPEC√çFICA. Consulte a Licen√ßa P√∫blica Geral
 * do GNU para mais detalhes.
 *
 * VocÍ deve ter recebido uma c√≥pia da Licen√ßa P√∫blica Geral do GNU junto
 * com este programa; se n„o, escreva para a Free Software Foundation, Inc., no
 * endere√ßo 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Mailer
 * @subpackage  Modules
 * @since     Arquivo dispon√≠¬≠vel desde a vers„o ?
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
      $message = "Ol·!\n\n"                                                             .
                 "Ocorreu um erro inesperado no banco de dados, detalhes abaixo:\n\n"   .
                 "  ERRO APP: ' . $appError\n"                                          .
                 "  ERRO PHP: ' . {$lastError['message']}\n"                            .
                 "  ERRO POSTGRES: $pgError\n"                                          .
                 "  LINHA {$lastError['line']} em {$lastError['file']}\n"               .
                 "  SQL: {$sql}\n"                                                      .
                 "  ID USU¡RIO {$userId}\n"                                             .
                 "\n\n-\n\n"                                                            .
                 "VocÍ recebeu este email pois seu email foi configurado para receber " .
                 "notificaÁıes de erros.";

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
      $message = "Ol·!\n\n"                                                             .
                 "Ocorreu um erro inesperado, detalhes abaixo:\n\n"                     .
                 "  ERRO APP: ' . $appError\n"                                          .
                 "  ERRO PHP: ' . {$lastError['message']}\n"                            .
                 "  LINHA {$lastError['line']} em {$lastError['file']}\n"               .
                 "  USU¡RIO {$user['matricula']} email {$user['email']}\n"              .
                 "\n\n-\n\n"                                                            .
                 "VocÍ recebeu este email pois seu email foi configurado para receber " .
                 "notificaÁıes de erros.";

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
      error_log("N„o foi definido um email para receber detalhes dos erros, por favor adicione a opÁ„o " .
                "'modules.error_notification.email = email@dominio.com' ao arquivo ini de configuraÁ„o.");

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
}