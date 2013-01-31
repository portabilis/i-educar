<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
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
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */


/**
 * Portabilis_Mailer class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */

// requer bibliotecas Mail e Net_SMTP, ver /scripts/install_pear_packages.sh
require_once 'Mail.php';

class Portabilis_Mailer {

  static $selfMailer;

  // #TODO refatorar classe para ser singleton ?

  public function __construct() {
    /* Configurações podem ser alteradas em tempo de execução, ex:
       $mailerInstance->configs->smtp->username = 'new_username'; */

    $this->configs = $GLOBALS['coreExt']['Config']->app->mailer;
  }


  public function sendMail($to, $subject, $message) {
    $from = "{$this->configs->smtp->from_name} <{$this->configs->smtp->from_email}>";
    $headers = array ('From'    => $from,
                      'To'      => $to,
                      'Subject' => $subject);

    $smtp = Mail::factory('smtp', array ('host'     => $this->configs->smtp->host,
                                         'port'     => $this->configs->smtp->port,
                                         'auth'     => $this->configs->smtp->auth == '1',
                                         'username' => $this->configs->smtp->username,
                                         'password' => $this->configs->smtp->password,
                                         'debug'    => $this->configs->debug == '1',
                                         'persist'  => false));

    // if defined some allowed domain defined in config, check if $to is allowed

    if (! is_null($this->configs->allowed_domains)) {
      foreach ($this->configs->allowed_domains as $domain) {
        if (strpos($to, "@$domain") != false) {
          $sendResult = ! PEAR::isError($smtp->send($to, $headers, $message));
          break;
        }
      }
    }
    else
      $sendResult = ! PEAR::isError($smtp->send($to, $headers, $message));

    error_log("** Sending email from: $from, to: $to, subject: $subject, message: $message");
    error_log("sendResult: $sendResult");

    return $sendResult;
  }


  static function mail($to, $subject, $message) {
    if (! isset(self::$selfMailer))
    self::$selfMailer = new Portabilis_Mailer();

    return self::$selfMailer->sendMail($to, $subject, $message);
  }


  static protected function host() {
    return $_SERVER['HTTP_HOST'];
  }
}