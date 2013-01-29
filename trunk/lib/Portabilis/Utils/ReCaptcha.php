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

// requer a biblioteca Services_ReCaptcha, ver /scripts/install_pear_packages.sh
require_once 'Services/ReCaptcha.php';

/**
 * Portabilis_Utils_ReCaptcha class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */

class Portabilis_Utils_ReCaptcha {

  static function getWidget() {
    $recaptchaConfigs = $GLOBALS['coreExt']['Config']->app->recaptcha;
    $recaptcha = new Services_ReCaptcha($recaptchaConfigs->public_key,
                                        $recaptchaConfigs->private_key,
                                        array('lang' => $recaptchaConfigs->options->lang,
                                              'theme' => $recaptchaConfigs->options->theme,
                                              'secure' => $recaptchaConfigs->options->secure == '1'));
    return $recaptcha;
  }

}