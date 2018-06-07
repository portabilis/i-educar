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
 * @author    Paula Bonot <bonot@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     11/2013
 * @version   $Id$
 */

require_once 'lib/Portabilis/View/Helper/Input/MultipleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Utils/SafeJson.php';

/**
 * Portabilis_View_Helper_Input_MultipleSearchCustom class.
 *
 * @author    Paula Bonot <bonot@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     11/2013
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Resource_MultipleSearchCustom extends Portabilis_View_Helper_Input_MultipleSearch {

  public function MultipleSearchCustom($attrName, $options = array()) {
    $defaultOptions = array('objectName'    => 'custom',
                            'apiController' => 'custom',
                            'apiResource'   => 'custom-search',
                            'type'          => 'multiple');

    $options                         = $this->mergeOptions($options, $defaultOptions);
    $options['options']['resources'] = $this->insertOption(NULL, '', $options['options']['options']['all_values']);

    $this->placeholderJs($options);

    parent::multipleSearch($options['objectName'], $attrName, $options);
  }

  protected function placeholderJs($options) {
    $optionsVarName = "multipleSearch" . Portabilis_String_Utils::camelize($options['objectName']) . "Options";
    $js             = "if (typeof $optionsVarName == 'undefined') { $optionsVarName = {} };
                       $optionsVarName.placeholder = safeUtf8Decode('Selecione');";

    $json = SafeJson::encode($options['options']['options']['values']);

    $js .= 'arrayOptions.push({element : $j("#'. $options['objectName'] .'"),values : '. $json .'})';

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = false);
  }
  protected function loadAssets() {
    Portabilis_View_Helper_Application::loadChosenLib($this->viewInstance);
    $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js';
    Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
    $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/MultipleSearchCustom.js';
    Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
  }
}
