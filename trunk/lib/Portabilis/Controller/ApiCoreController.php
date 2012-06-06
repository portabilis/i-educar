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
require_once 'CoreExt/Exception.php';
require_once 'lib/Portabilis/Messenger.php';
require_once 'lib/Portabilis/Validator.php';
require_once 'include/clsBanco.inc.php';

class ApiCoreController extends Core_Controller_Page_EditController
{
  protected $_dataMapper  = ''; #Avaliacao_Model_NotaComponenteDataMapper';
  protected $_processoAp  = 0;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';


  public function __construct() {
    $this->messenger = new Messenger();
    $this->validator = new Validator($this->messenger);
    $this->response = array();
    $this->db = new clsBanco();
  }


  protected function validatesUserIsLoggedIn(){
    return $this->validator->validatesPresenceOf($this->getSession()->id_pessoa, '', false, 'Usuário deve estar logado');
  }

  protected function validatesPresenceOfResource(){
    return $this->validator->validatesPresenceOf($this->getRequest()->resource, 'resource');
  }

  protected function validatesPresenceOfOper(){
    return $this->validator->validatesPresenceOf($this->getRequest()->oper, 'oper');
  }


  protected function validatesPresenceOf($requiredParamNames) {
    if (! is_array($requiredParamNames))
      $requiredParamNames = array($requiredParamNames);

      $valid = true;

      foreach($requiredParamNames as $param) {
        if (! $this->validator->validatesPresenceOf($this->getRequest()->$param, $param) and $valid) {
          $valid = false;
        }
      }

    return $valid;
  }


  protected function validatesIsNumeric($expectedNumericParamNames) {
    if (! is_array($expectedNumericParamNames))
      $expectedNumericParamNames = array($expectedNumericParamNames);

      $valid = true;

      foreach($requiredParamNames as $param) {
        if (! $this->validator->validatesValueIsNumeric($this->getRequest()->$param, $param) and $valid) {
          $valid = false;
        }
      }

    return $valid;
  }


  protected function canAcceptRequest()
  {
    return $this->validatesUserIsLoggedIn() &&
           $this->validatesPresenceOfOper() &&
           $this->validatesPresenceOfResource();
  }


  protected function notImplementedOperationError()
  {
    $this->messenger->append("Operação '{$this->getRequest()->oper}' não implementada para o recurso '{$this->getRequest()->resource}'");
  }


  protected function appendResponse($name, $value = ''){
    if (is_array($name)) {
      foreach($name as $k => $v) {
        $this->response[$k] = $v;
      }
    }
    else
      $this->response[$name] = $value;
  }


  // subscrever nas classes filhas sentando os recursos disponibilizados e operacoes permitidas, ex:
  // return array('resources1' => array('get'), 'resouce2'    => array('post', 'delete'));
  protected function getAvailableOperationsForResources() {
    throw new CoreExt_Exception('É necessário sobrescrever o método "getExpectedOperationsForResources()" de ApiCoreController.');
  }


  protected function isRequestFor($oper, $resource) {
    return $this->getRequest()->resource == $resource &&
           $this->getRequest()->oper == $oper;
  }


  protected function prepareResponse(){
    try {
      if (isset($this->getRequest()->oper))
        $this->appendResponse('oper', $this->getRequest()->oper);

      if (isset($this->getRequest()->resource))
        $this->appendResponse('resource', $this->getRequest()->resource);

      $this->appendResponse('msgs', $this->messenger->getMsgs());
      $response = json_encode($this->response);
    }
    catch (Exception $e){
      error_log("Erro inesperado no metodo prepareResponse da classe ApiCoreController: {$e->getMessage()}");
      $response = array('msgs' => array('msg' => 'Erro inesperado no servidor. Por favor, tente novamente.',
                        'type' => 'error'));

      $response = json_encode($response);
    }

    echo $response;
  }


  public function generate(CoreExt_Controller_Page_Interface $instance){
    header('Content-type: application/json');

    try {
      if ($this->canAcceptRequest())
        $instance->Gerar();
    }
    catch (Exception $e){
      $this->messenger->append('Exception: ' . $e->getMessage(), $type = 'error', $encodeToUtf8 = true);
    }

    echo $this->prepareResponse();
  }


  /*  subescrever nas classes filhas setando as verificações desejadas e retornando a resposta,
      conforme recurso e operação recebida ex: get, post ou delete, ex:

      if ($this->getRequest()->oper == 'get')
      {
        // caso retorne apenas um recurso
        $this->appendResponse('matriculas', $matriculas);

        // ou para multiplos recursos, pode-se usar o argumento resource
        if ($this->getRequest()->resource == 'matriculas')
          $this->appendResponse('matriculas', $this->getMatriculas());
        elseif ($this->getRequest()->resource == 'alunos')
          $this->appendResponse('alunos', $this->getAlunos);
        else
          $this->notImplementedError();
      }
      elseif ($this->getRequest()->oper == 'post')
        $this->postMatricula();
      else
        $this->notImplementedError();
  */
  public function Gerar(){
    throw new CoreExt_Exception('É necessário sobrescrever o método "ApiCoreController()" de ApiCoreController.');
  }


  // wrapper para $this->db->execPreparedQuery($sql, $params)
  protected function fetchPreparedQuery($sql, $params = array(), $hideExceptions = true, $returnOnly = '') {
    try{
      $result = array();
      if ($this->db->execPreparedQuery($sql, $params) != false) {

        while ($this->db->ProximoRegistro())
          $result[] = $this->db->Tupla();

        if ($returnOnly == 'first-line' and isset($result[0]))
          $result = $result[0];
        elseif ($returnOnly == 'first-field' and isset($result[0]) and isset($result[0][0]))
          $result = $result[0][0];
      }
    }
    catch(Exception $e)
    {
      if (! $hideExceptions)
        $this->messages->append($e->getMessage(), "error", true);
    }
    return $result;
  }
}
