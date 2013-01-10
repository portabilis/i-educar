<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Mailer
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Mailer.php';

class ErrorMailer extends Portabilis_Mailer
{
  static function unexpectedDataBaseError($appError, $pgError, $sql) {
    $lastError = error_get_last();

    if (self::canSendEmail()) {
      $to      = self::notificationEmail();
      $subject = "[Erro inesperado bd] i-Educar - " . self::host();
      $message = "Olá!\n\n"                                                             .
                 "Ocorreu um erro inesperado no banco de dados, detalhes abaixo:\n\n"   .
                 "  ERRO APP: ' . $appError\n"                                          .
                 "  ERRO PHP: ' . {$lastError['message']}\n"                            .
                 "  ERRO POSTGRES: $pgError\n"                                          .
                 "  LINHA {$lastError['line']} em {$lastError['file']}\n"               .
                 "  SQL: $sql"                                                          .
                 "\n\n-\n\n"                                                            .
                 "Você recebeu este email pois seu email foi configurado para receber " .
                 "notificações de erros.";

      // only call (send) email, if a email was set.
      return ($to ? self::mail($to, $subject, $message) : false);
    }
  }

  // common error mailer methods

  protected static function canSendEmail() {
    return $GLOBALS['coreExt']['Config']->modules->error_notification->send_email == true;
  }

  protected static function notificationEmail() {
  $email = $GLOBALS['coreExt']['Config']->modules->error_notification->email;

    if (! is_string($email)) {
      error_log("Não foi definido um email para receber detalhes dos erros, por favor adicione a opção " .
                "'modules.error_notification.email = email@dominio.com' ao arquivo ini de configuração.");

      return false;
    }

    return $email;
  }
}