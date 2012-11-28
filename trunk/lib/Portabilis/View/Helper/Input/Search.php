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
 * Portabilis_View_Helper_DynamicInput_BibliotecaPesquisaObra class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Search extends Portabilis_View_Helper_Input_Core {

  public function search($objectName, $attrName, $options = array()) {
    $defaultOptions = array('options'                 => array(),
                            'validatesRequiredFields' => false,
                            'searchPath'              => "/intranet/educar_pesquisa_$objectName.php" );

    $options    = $this->mergeOptions($options, $defaultOptions);

    $inputHint  = "<img border='0' onclick='pesquisa" . ucwords($objectName) . "();' id='lupa_pesquisa_" . $objectName .
                  "' name='lupa_pesquisa_" . $objectName . "' src='imagens/lupa.png' />";

    $defaultInputOptions = array('id'         => $objectName . '_' . $attrName,
                                 'label'      => ucwords($attrName),
                                 'value'      => '',
                                 'size'       => '30',
                                 'max_length' => '255',
                                 'required'   => true,
                                 'expressao'  => false,
                                 'duplo'      => false,
                                 'label_hint' => '',
                                 'input_hint' => $inputHint,
                                 'callback'   => '',
                                 'event'      => 'onKeyUp',
                                 'disabled'   => true);

    $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);
    call_user_func_array(array($this->viewInstance, 'campoTexto'), $inputOptions);

    $hiddenInputId = $objectName . "_id";
    $this->viewInstance->campoOculto($hiddenInputId, '');

    /*
      #TODO receive an option, to set depends_on ? (recebe id, elemento do qual depende,
      e no evento change deste, reseta o campo pesquisa)

    // reset js

    $resetJs = 'var reset' . ucwords($attrName) . ' = function() {
        $("#' . $hiddenInputId . '").val("");
        $("#' . $inputOptions['id'] . '").val("");
      }

      $("#ref_cod_escola").change(reset' . ucwords($attrName) . ');';

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $resetJs, true);
    */


    // search js

    if ($options['validatesRequiredFields']) {
      $js = 'function pesquisa' . ucwords($objectName) . '() {
        var exceptFields = [document.getElementById("' . $inputOptions['id'] . '")];

        if (validatesPresenseOfValueInRequiredFields([], exceptFields)) {
          pesquisa_valores_popless("' . $options['searchPath'] . '");
        }
      }';
    }
    else {
      $js = 'function pesquisa' . ucwords($objectName) . '() {
        pesquisa_valores_popless("' . $options['searchPath'] . '");
      }';
    }

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js);
  }
}
?>