<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
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
 * @subpackage  lib
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'Core/Controller/Page/EditController.php';

require_once 'lib/Portabilis/Messenger.php';
require_once 'lib/Portabilis/Validator.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/DataMapper/Utils.php';

require_once 'lib/Portabilis/View/Helper/Application.php';

// Resource controller
class Portabilis_Controller_Page_EditController extends Core_Controller_Page_EditController
{

  protected $_dataMapper = null;

  # vars that must be overwritten in subclasses
    # protected $_processoAp        = 0;
    # protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;

  # vars that can be overwritten
    # protected $_dataMapper   = 'Avaliacao_Model_NotaComponenteDataMapper';
    # protected $_saveOption   = FALSE;
    # protected $_deleteOption = FALSE;
    # protected $_titulo       = 'Cadastro de aluno';

  protected $_nivelAcessoInsuficiente = "/module/Error/unauthorized";


  protected $_titulo               = '';
  protected $backwardCompatibility = false;

  public function __construct(){
    parent::__construct();
    $this->loadAssets();
  }

  // methods that can be overwritten

  protected function canSave()
  {
    return true;
  }


  // methods that must be overwritten

  function Gerar()
  {
    throw new Exception("The method 'Gerar' must be overwritten!");
  }


  protected function save()
  {
    throw new Exception("The method 'save' must be overwritten!");
  }


  // methods that cannot be overwritten

  protected function _save()
  {
    $result = false;

    // try set or load entity before validation or save
    if (! $this->_initNovo())
      $this->_initEditar();

    if (! $this->messenger()->hasMsgWithType('error') && $this->canSave()) {
      try {
        $result = $this->save();

        if (is_null($result))
          $result = ! $this->messenger()->hasMsgWithType('error');
        elseif(! is_bool($result))
          throw new Exception("Invalid value returned from '_save' method: '$result', please return null, true or false!");
      }
      catch (Exception $e) {
        $this->messenger()->append('Erro ao gravar altera&ccedil;&otilde;es, por favor, tente novamente.', 'error');
        error_log("Erro ao gravar alteracoes: " .  $e->getMessage());

        $result = false;
      }

      $result = $result && ! $this->messenger()->hasMsgWithType('error');

      if ($result)
        $this->messenger()->append('Altera&ccedil;&otilde;es gravadas com sucesso.', 'success', false, 'success');
     }

    return $result;
  }


  protected function flashMessage()
  {
    if (! $this->hasErrors())
      return $this->messenger()->toHtml();

    return '';
  }


  // helpers

  protected function validator() {
    if (! isset($this->_validator))
      $this->_validator = new Portabilis_Validator();

    return $this->_validator;
  }


  protected function messenger() {
    if (! isset($this->_messenger))
      $this->_messenger = new Portabilis_Messenger();

    return $this->_messenger;
  }


  protected function mailer() {
    if (! isset($this->_mailer))
      $this->_mailer = new Portabilis_Mailer();

    return $this->_mailer;
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
                    '/modules/Portabilis/Assets/Stylesheets/Frontend/Resource.css');
    Portabilis_View_Helper_Application::loadStylesheet($this, $styles);


    $scripts = array('/modules/Portabilis/Assets/Javascripts/ClientApi.js',
                     '/modules/Portabilis/Assets/Javascripts/Validator.js',
                     '/modules/Portabilis/Assets/Javascripts/Utils.js');

    if (! $this->backwardCompatibility)
      $scripts[] = '/modules/Portabilis/Assets/Javascripts/Frontend/Resource.js';

    Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
  }


  // wrappers for Portabilis_*Utils*

  protected static function mergeOptions($options, $defaultOptions) {
    return Portabilis_Array_Utils::merge($options, $defaultOptions);
  }


  protected function fetchPreparedQuery($sql, $options = array()) {
    return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
  }


  protected function getDataMapperFor($packageName, $modelName){
    return Portabilis_DataMapper_Utils::getDataMapperFor($packageName, $modelName);
  }
}
