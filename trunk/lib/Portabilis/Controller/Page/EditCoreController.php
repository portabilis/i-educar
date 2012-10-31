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

class Portabilis_Controller_Page_EditCoreController extends Core_Controller_Page_EditController
{
  protected $_dataMapper  = ''; #Avaliacao_Model_NotaComponenteDataMapper';
  protected $_processoAp  = 0;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';


  /* public function __construct() {
  } */

  protected function validator() {
    if (! isset($this->_validator))
      $this->_validator = new Validator();

    return $this->_validator;
  }


  protected function messenger() {
    if (! isset($this->_messenger))
      $this->_messenger = new Messenger();

    return $this->_messenger;
  }

  protected function mailer() {
    if (! isset($this->_mailer))
      $this->_mailer = new Mailer();

    return $this->_mailer;
  }

  public function _generate(CoreExt_Controller_Page_Interface $instance)
  {
    parent::generate($instance);
    header('Content-type: text/html; charset=UTF-8');
  }

  protected function redirectTo($url) {
    header("Location: $url");
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


  // methods that can be overwritten

  function afterSave() {
  }


  // methods that must be overwritten

  function render() {
    throw new Exception("The method 'render' must be overwritten!");
  }


  function edit() {
    throw new Exception("The method 'edit' must be overwritten!");
  }

  // methods that cannot be overwritten

  function Gerar(){
    $this->render();
  }

  protected function _initEditar()
  {
    return true;
  }

  public function Editar() {
    $result         = $this->edit();
    $this->mensagem = $this->messenger()->toHtml();

    if (is_null($result))
      $result = ! $this->messenger()->hasMsgWithType('error');
    elseif(! is_bool($result))
      throw new Exception("Invalid value returned from 'edit' method: '$result', please return null, true or false!");

    if ($result == true)
      $this->afterSave();

    return $result;
  }

  protected function _initNovo()
  {
    return false;
  }

  protected function _save()
  {
    return false;
  }

  function Novo()
  {
    throw new Exception("Method 'Novo' not allowed for controllers that inherit from " .
                        "Portabilis_Controller_Page_EditCoreController.");
  }

  function Excluir()
  {
    throw new Exception("Method 'Excluir' not allowed for controllers that inherit from " .
                        "Portabilis_Controller_Page_EditCoreController.");
  }
}
