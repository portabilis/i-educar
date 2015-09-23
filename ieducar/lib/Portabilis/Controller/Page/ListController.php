<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
 * @package   Avaliacao
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'Core/Controller/Page/ListController.php';
require_once 'lib/Portabilis/View/Helper/Application.php';
require_once "lib/Portabilis/View/Helper/Inputs.php";

// Process controller
class Portabilis_Controller_Page_ListController extends Core_Controller_Page_ListController
{

  protected $backwardCompatibility = false;

  public function __construct() {
    $this->rodape  = "";
    $this->largura = '100%';

    $this->loadAssets();
    parent::__construct();
  }

  protected function loadResourceAssets($dispatcher){
    $rootPath       = $_SERVER['DOCUMENT_ROOT'];
    $controllerName = ucwords($dispatcher->getControllerName());
    $actionName     = ucwords($dispatcher->getActionName());

    $style          = "/modules/$controllerName/Assets/Stylesheets/$actionName.css";
    $script         = "/modules/$controllerName/Assets/Javascripts/$actionName.js";

    if (file_exists($rootPath . $style))
      Portabilis_View_Helper_Application::loadStylesheet($this, $style);

    if (file_exists($rootPath . $script))
      Portabilis_View_Helper_Application::loadJavascript($this, $script);
  }

  protected function loadAssets(){
    Portabilis_View_Helper_Application::loadJQueryLib($this);
    Portabilis_View_Helper_Application::loadJQueryFormLib($this);

    $styles = array('/modules/Portabilis/Assets/Stylesheets/Frontend.css',
                    '/modules/Portabilis/Assets/Stylesheets/Frontend/Process.css');
    Portabilis_View_Helper_Application::loadStylesheet($this, $styles);

    $scripts = array(
      '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
      '/modules/Portabilis/Assets/Javascripts/Validator.js',
      '/modules/Portabilis/Assets/Javascripts/Utils.js'
    );

    if (! $this->backwardCompatibility)
      $scripts[] = '/modules/Portabilis/Assets/Javascripts/Frontend/Process.js';

    Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
  }
}