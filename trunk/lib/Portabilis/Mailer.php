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
 * CoreExt_Session class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */

/* requer instalação Mail, Net_SMTP:
  $ pear install Mail
  $ pear install Net_SMTP
*/
require_once 'Mail.php';

class Portabilis_Mailer {

  static $selfMailer;

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

    $sendResult = $smtp->send($to, $headers, $message);

    return ! PEAR::isError($sendResult);
  }


  static function mail($to, $subject, $message) {
    if (! isset(self::$selfMailer))
    self::$selfMailer = new Portabilis_Mailer();

    return self::$selfMailer->sendMail($to, $subject, $message);
  }
}

// deprecated Mailer class
class Mailer extends Portabilis_Mailer {
}
?>

