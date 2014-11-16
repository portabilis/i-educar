<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);
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

require_once 'lib/Portabilis/View/Helper/Input/Core.php';


/**
 * Portabilis_View_Helper_Input_Numeric class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Numeric extends Portabilis_View_Helper_Input_Core {

  protected function fixupValidation($inputOptions) {
    // fixup para remover caracteres não numericos
    $js = " \$j('#" . $inputOptions['id'] . "').keyup(function(){
      var oldValue = this.value;
      this.value = this.value.replace(/[^0-9\.]/g, '');

      if (oldValue != this.value)
        messageUtils.error('Informe apenas números.', this);

    });";

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = false);
  }

  public function numeric($attrName, $options = array()) {
    $defaultOptions = array('options' => array(), 'objectName' => '');

    $options             = $this->mergeOptions($options, $defaultOptions);
    $spacer              = ! empty($options['objectName']) && ! empty($attrName) ? '_' : '';

    $label = ! empty($attrName) ? $attrName : $options['objectName'];
    $label = str_replace('_id', '', $label);

    $defaultInputOptions = array('id'         => $options['objectName'] . $spacer . $attrName,
                                 'label'      => ucwords($label),
                                 'value'      => null,
                                 'size'       => 15,
                                 'max_length' => 15,
                                 'required'   => true,
                                 'label_hint' => ' ',
                                 'input_hint' => '',
                                 'script'     => false,
                                 'event'      => 'onKeyUp',
                                 'inline'     => false,
                                 'disabled'   => false);

    $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);
    $inputOptions['label'] = Portabilis_String_Utils::toLatin1($inputOptions['label'], array('escape' => false));

    call_user_func_array(array($this->viewInstance, 'campoNumero'), $inputOptions);
    $this->fixupPlaceholder($inputOptions);
    $this->fixupValidation($inputOptions);
  }
}
