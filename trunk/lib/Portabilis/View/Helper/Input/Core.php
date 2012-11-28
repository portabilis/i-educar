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

require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'lib/Portabilis/View/Helper/Application.php';
require_once 'lib/Portabilis/Array/Utils.php';

/**
 * SelectMenusHelper class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Core {
  public function __construct($viewInstance, $inputsHelper) {
    $this->viewInstance  = $viewInstance;
    $this->_inputsHelper = $inputsHelper;


    // load styles

    $styles = array('/modules/Portabilis/Assets/Stylesheets/FrontendApi.css',
                    '/modules/Portabilis/Assets/Stylesheets/Utils.css');

    Portabilis_View_Helper_Application::loadStylesheet($this->viewInstance, $styles);


    // load js

    Portabilis_View_Helper_Application::loadJQueryLib($this->viewInstance);
    Portabilis_View_Helper_Application::loadJQueryUiLib($this->viewInstance);

    $dependencies = array('/modules/Portabilis/Assets/Javascripts/Utils.js',
                          '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
                          '/modules/Portabilis/Assets/Javascripts/Validator.js');

    Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $dependencies);


    // js fixups

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, 'fixupFieldsWidth();');
  }

  // wrapper for Portabilis_Array_Utils::merge
  protected static function mergeOptions($options, $defaultOptions) {
    return Portabilis_Array_Utils::merge($options, $defaultOptions);
  }

  protected function inputsHelper() {
    return $this->_inputsHelper;
  }
}
?>
