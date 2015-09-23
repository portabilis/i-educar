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
 * Portabilis_View_Helper_Input_MultipleSearch class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_MultipleSearch extends Portabilis_View_Helper_Input_Core {

  public function multipleSearch($objectName, $attrName, $options = array()) {
    $defaultOptions = array('options'            => array(),
                            'apiModule'         => 'Api',
                            'apiController'     => ucwords($objectName),
                            'apiResource'       => $objectName . '-search',
                            'searchPath'         => '');

    $options = $this->mergeOptions($options, $defaultOptions);

    if (empty($options['searchPath']))
      $options['searchPath'] = "/module/" . $options['apiModule'] . "/" . $options['apiController'] .
                               "?oper=get&resource=" . $options['apiResource'];

    // #TODO load resources value?

    /*
    // load value if received an resource id
    $resourceId = $options['hiddenInputOptions']['options']['value'];

    if ($resourceId && ! $options['options']['value'])
    $options['options']['value'] = $resourceId . " - ". $this->resourcesValue($resourceId);
    */

    $this->selectInput($objectName, $attrName, $options);

    $this->loadAssets();
    $this->js($objectName, $attrName, $options);
  }

  protected function selectInput($objectName, $attrName, $options) {
    $textHelperOptions = array('objectName' => $objectName);

    $this->inputsHelper()->select($attrName, $options['options'], $textHelperOptions);
  }


  protected function loadAssets() {
    Portabilis_View_Helper_Application::loadChosenLib($this->viewInstance);

    $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js';
    Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
  }


  protected function js($objectName, $attrName, $options) {
    // setup multiple search

    /*
      all search options (including the option chosenOptions, that is passed for chosen plugin),
      can be overwritten adding "var = multipleSearch<ObjectName>Options = { 'options' : 'val', option2 : '_' };"
      in the script file for the resource controller.
    */

    $resourceOptions = "multipleSearch" . Portabilis_String_Utils::camelize($objectName) . "Options";

    $js = "$resourceOptions = typeof $resourceOptions == 'undefined' ? {} : $resourceOptions;
           multipleSearchHelper.setup('$objectName', '$attrName', '" . $options['searchPath'] . "', $resourceOptions);";

    // this script will be executed after the script for the current controller (if it was loaded in the view);
    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
  }
}