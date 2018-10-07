<?php
namespace Ieducar\Portabilis\Utils;

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
 * @author Éber Freitas Dias <eber.freitas@gmail.com
 * @category  i-Educar
 * @license   GPL-2.0+
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'vendor/autoload.php';

class Portabilis_Utils_ReCaptcha
{
    public static function getWidget()
    {
        $config = $GLOBALS['coreExt']['Config']->app->recaptcha;
        $template = '<div class="g-recaptcha" data-sitekey="%s"></div><script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=%s"></script>';

        return sprintf($template, $config->public_key, $config->options->lang);
    }

    public static function check($response)
    {
        $recaptcha = new \ReCaptcha\ReCaptcha($GLOBALS['coreExt']['Config']->app->recaptcha->private_key);
        $resp = $recaptcha->verify($response, $_SERVER['REMOTE_ADDR']);

        return $resp->isSuccess();
    }
}
