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

require_once 'Portabilis/View/Helper/Input/Core.php';
require_once 'Portabilis/Date/Utils.php';

/**
 * Portabilis_View_Helper_Input_Date class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Date extends Portabilis_View_Helper_Input_Core {

  public function date($attrName, $options = array()) {
    $defaultOptions = array('options' => array(), 'objectName' => '');

    $options             = $this->mergeOptions($options, $defaultOptions);
    $spacer              = ! empty($options['objectName']) && ! empty($attrName) ? '_' : '';

    $label = ! empty($attrName) ? $attrName : $options['objectName'];
    $label = str_replace('_id', '', $label);

    $defaultInputOptions = array('id'             => $options['objectName'] . $spacer . $attrName,
                                 'label'          => ucwords($label),
                                 'value'          => null,
                                 'required'       => true,
                                 'label_hint'     => '',
                                 'inline'         => false,
                                 'callback'       => false,
                                 'disabled'       => false,

                                 // opcoes suportadas pelo elemento, mas não pelo helper ieducar
                                 'size'           => 9);

    $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

    $isDbFormated = strrpos($inputOptions['value'], '-') > -1;

    if ($isDbFormated)
      $inputOptions['value'] = Portabilis_Date_Utils::pgSQLToBr($inputOptions['value']);

    call_user_func_array(array($this->viewInstance, 'campoData'), $inputOptions);
    $this->fixupPlaceholder($inputOptions);

    // implementado fixup via js, pois algumas opções não estão sendo verificadas pelo helper ieducar.
    $this->fixupOptions($inputOptions);
  }

  protected function fixupOptions($inputOptions) {
    $id           = $inputOptions['id'];

    $sizeFixup    = "\$input.attr('size', " . $inputOptions['size'] . ");";
    $disableFixup = $inputOptions['disabled'] ? "\$input.attr('disabled', 'disabled');" : '';

    $script = "
      var \$input = \$j('#" . $id . "');
      $sizeFixup
      $disableFixup
    ";

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $script, $afterReady = true);
  }
}
