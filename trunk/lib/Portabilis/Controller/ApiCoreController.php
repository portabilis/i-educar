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

require_once 'include/clsBanco.inc.php';

require_once 'Core/Controller/Page/EditController.php';
require_once 'CoreExt/Exception.php';

require_once 'lib/Portabilis/Messenger.php';
require_once 'lib/Portabilis/Validator.php';
require_once 'lib/Portabilis/DataMapper/Utils.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Utils/User.php';

class ApiCoreController extends Core_Controller_Page_EditController
{

  // variaveis usadas apenas em formulários, desnecesário subescrever nos filhos.

  protected $_saveOption        = FALSE;
  protected $_deleteOption      = FALSE;
  protected $_titulo            = '';


  // adicionar classe do data mapper que se deseja usar, em tais casos.
  protected $_dataMapper        = null;


  /* Variaveis utilizadas pelos validadores validatesAuthorizationToDestroy e validatesAuthorizationToChange.
     Notar que todos usuários tem autorização para o processo 0,
     nos controladores em que se deseja verificar permissões, adicionar o processo AP da funcionalidade.
  */
  protected $_processoAp        = 0;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::INSTITUCIONAL;


  public function __construct() {
    $this->messenger = new Portabilis_Messenger();
    $this->validator = new Portabilis_Validator($this->messenger);
    $this->response  = array();
  }

  protected function currentUser() {
    return Portabilis_Utils_User::load($this->getSession()->id_pessoa);
  }


  // validators

  protected function validatesAccessKey() {
    $valid = false;

    if (! is_null($this->getRequest()->access_key)) {
      $accessKey = $GLOBALS['coreExt']['Config']->apis->access_key;
      $valid     = $accessKey == $this->getRequest()->access_key;

      if (! $valid)
        $this->messenger->append('Chave de acesso inválida!');
    }

    return $valid;
  }

  protected function validatesSignature() {
    // #TODO implementar validação urls assinadas
    return true;
  }

  protected function validatesUserIsLoggedIn(){
    $canAccess = is_numeric($this->getSession()->id_pessoa);

    if (! $canAccess)
      $canAccess = ($this->validatesAccessKey() && $this->validatesSignature());

    if (! $canAccess) {
      $msg = 'Usuário deve estar logado ou a chave de acesso deve ser enviada!';
      $this->messenger->append($msg, 'error', $encodeToUtf8 = false, $ignoreIfHasMsgWithType = 'error');
    }

    return $canAccess;
  }


  protected function validatesUserIsAdmin() {
    $user = $this->currentUser();

    if(! $user['super']) {
      $this->messenger->append("O usuário logado deve ser o admin");
      return false;
    }

    return true;
  }

  protected function validatesId($resourceName, $options = array()) {
    $attrName = $resourceName . ($resourceName ? '_id' : 'id');

    return  $this->validatesPresenceOf($attrName) &&
            $this->validatesExistenceOf($resourceName, $this->getRequest()->$attrName, $options);
  }

  // subescrever nos controladores cujo recurso difere do padrao (schema pmieducar, tabela <resource>, pk cod_<resource>)
  protected function validatesResourceId() {
    return  $this->validatesPresenceOf('id') &&
            $this->validatesExistenceOf($this->getRequest()->resource, $this->getRequest()->id);
  }

  protected function validatesAuthorizationToDestroy() {
    $can = $this->getClsPermissoes()->permissao_excluir($this->getBaseProcessoAp(),
                                                        $this->getSession()->id_pessoa,
                                                        $this->_nivelAcessoOption);

    if (! $can)
      $this->messenger->append("Usuário sem permissão para excluir '{$this->getRequest()->resource}'.");

    return $can;
  }

  protected function validatesAuthorizationToChange() {
    $can = $this->getClsPermissoes()->permissao_cadastra($this->getBaseProcessoAp(),
                                                         $this->getSession()->id_pessoa,
                                                         $this->_nivelAcessoOption);

    if (! $can)
      $this->messenger->append("Usuário sem permissão para cadastrar '{$this->getRequest()->resource}'.");

    return $can;
  }


  // validation

  protected function canAcceptRequest() {
    return $this->validatesUserIsLoggedIn() &&
           $this->validatesPresenceOf(array('oper', 'resource'));
  }


  protected function canGet() {
    return $this->validatesResourceId();
  }


  protected function canChange() {
    throw new Exception('canChange must be overwritten!');
  }


  protected function canPost() {
    return $this->canChange() &&
           $this->validatesAuthorizationToChange();
  }


  protected function canPut() {
    return $this->canChange() &&
           $this->validatesResourceId() &&
           $this->validatesAuthorizationToChange();
  }


  protected function canSearch() {
    return $this->validatesPresenceOf('query');
  }


  protected function canDelete() {
    return $this->validatesResourceId() &&
           $this->validatesAuthorizationToDestroy();
  }


  protected function canEnable() {
    return $this->validatesResourceId() &&
           $this->validatesAuthorizationToChange();
  }


  // api

  protected function notImplementedOperationError() {
    $this->messenger->append("Operação '{$this->getRequest()->oper}' não implementada para o recurso '{$this->getRequest()->resource}'");
  }


  protected function appendResponse($name, $value = '') {
    if (is_array($name)) {
      foreach($name as $k => $v) {
        $this->response[$k] = $v;
      }
    }
    elseif (! is_null($name))
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
      $this->appendResponse('any_error_msg', $this->messenger->hasMsgWithType('error'));

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
    header('Content-type: application/json; charset=UTF-8');

    try {
      if ($this->canAcceptRequest())
        $instance->Gerar();
    }
    catch (Exception $e){
      $this->messenger->append('Exception: ' . $e->getMessage(), 'error', $encodeToUtf8 = true);
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
    throw new CoreExt_Exception("The method 'Gerar' must be overwritten!");
  }


  // #TODO mover validadores para classe lib/Portabilis/Validator.php / adicionar wrapper para tais

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


  protected function validatesExistenceOf($resourceName, $value, $options = array()) {
    $defaultOptions = array('schema_name'      => 'pmieducar',
                            'field_name'       => "cod_{$resourceName}",
                            'add_msg_on_error' => true);

    $options        = $this->mergeOptions($options, $defaultOptions);

    return $this->validator->validatesValueIsInBd($options['field_name'],
                                                  $value,
                                                  $options['schema_name'],
                                                  $resourceName,
                                                  $raiseExceptionOnFail = false,
                                                  $addMsgOnError        = $options['add_msg_on_error']);
  }

  protected function validatesUniquenessOf($resourceName, $value, $options = array()) {
    $defaultOptions = array('schema_name'      => 'pmieducar',
                            'field_name'       => "cod_{$resourceName}",
                            'add_msg_on_error' => true);

    $options        = $this->mergeOptions($options, $defaultOptions);

    return $this->validator->validatesValueNotInBd($options['field_name'],
                                                   $value,
                                                   $options['schema_name'],
                                                   $resourceName,
                                                   $raiseExceptionOnFail = false,
                                                   $addMsgOnError        = $options['add_msg_on_error']);
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


  // wrappers for Portabilis_*Utils*


  // DEPRECADO
  // #TODO nas classes filhas, migrar chamadas de fetchPreparedQuery para usar novo padrao com array de options
  protected function fetchPreparedQuery($sql, $params = array(), $hideExceptions = true, $returnOnly = '') {
    $options = array('params'      => $params,
                     'show_errors' => ! $hideExceptions,
                     'return_only' => $returnOnly,
                     'messenger'   => $this->messenger);

    return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
  }

  protected function getDataMapperFor($packageName, $modelName){
    return Portabilis_DataMapper_Utils::getDataMapperFor($packageName, $modelName);
  }

  protected function getEntityOf($dataMapper, $id) {
    return $dataMapper->find($id);
  }

  protected function tryGetEntityOf($dataMapper, $id) {
    try {
      $entity = $this->getEntityOf($dataMapper, $id);
    }
    catch(Exception $e) {
      $entity = null;
    }

    return $entity;
  }

  protected function createEntityOf($dataMapper, $data = array()) {
    return $dataMapper->createNewEntityInstance($data);
  }

  protected function getOrCreateEntityOf($dataMapper, $id) {
    $entity = $this->tryGetEntityOf($dataMapper, $id);
    return (is_null($entity) ? $this->createEntityOf($dataMapper) : $entity);
  }

  protected function deleteEntityOf($dataMapper, $id) {
    $entity = $this->tryGetEntityOf($dataMapper, $id);
    return (is_null($entity) ? true : $dataMapper->delete($entity));
  }

  protected function saveEntity($dataMapper, $entity) {
    if ($entity->isValid())
      $dataMapper->save($entity);

    else {
      $errors = $entity->getErrors();
      $msgs   = array();

      foreach ($errors as $attr => $msg) {
        if (! is_null($msg))
          $msgs[] = "$attr => $msg";
      }

      //$msgs transporte_aluno
      $msg = 'Erro ao salvar o recurso ' . $dataMapper->resourceName() . ': ' . join(', ', $msgs);
      $this->messenger->append($msg, 'error', true);
    }
  }

  protected static function mergeOptions($options, $defaultOptions) {
    return Portabilis_Array_Utils::merge($options, $defaultOptions);
  }

  protected function toUtf8($str, $options = array()) {
    return Portabilis_String_Utils::toUtf8($str, $options);
  }

  protected function toLatin1($str, $options = array()) {
    return Portabilis_String_Utils::toLatin1($str, $options);
  }

  // DEPRECADO #TODO nas classe filhas migrar de safeString => toUtf8
  protected function safeString($str, $transform = true) {
    return $this->toUtf8($str, array('transform' => $transform));
  }

  // DEPRECADO #TODO nas classe filhas migrar de safeStringForDb => toLatin1
  protected function safeStringForDb($str) {
    return $this->toLatin1($str);
  }


  // search

  protected function defaultSearchOptions() {
    $resourceName = Portabilis_String_Utils::underscore($this->getDispatcher()->getActionName());

    return array('namespace'    => 'pmieducar',
                 'table'        => $resourceName,
                 'idAttr'       => "cod_$resourceName",
                 'labelAttr'    => 'nome',
                 'selectFields' => array(),
                 'sqlParams'    => array());
  }

  // overwrite in subclass to chande search options
  protected function searchOptions() {
    return array();
  }

  protected function sqlsForNumericSearch() {
    $searchOptions = $this->mergeOptions($this->searchOptions(), $this->defaultSearchOptions());

    $namespace     = $searchOptions['namespace'];
    $table         = $searchOptions['table'];
    $idAttr        = $searchOptions['idAttr'];
    $labelAttr     = $searchOptions['labelAttr'];

    $searchOptions['selectFields'][] = "$idAttr as id, $labelAttr as name";
    $selectFields                    = join(', ', $searchOptions['selectFields']);

    return "select distinct $selectFields from $namespace.$table
            where $idAttr like $1||'%' order by $idAttr limit 15";
  }


  protected function sqlsForStringSearch() {
    $searchOptions = $this->mergeOptions($this->searchOptions(), $this->defaultSearchOptions());

    $namespace     = $searchOptions['namespace'];
    $table         = $searchOptions['table'];
    $idAttr        = $searchOptions['idAttr'];
    $labelAttr     = $searchOptions['labelAttr'];

    $searchOptions['selectFields'][] = "$idAttr as id, $labelAttr as name";
    $selectFields                    = join(', ', $searchOptions['selectFields']);

    return "select distinct $selectFields from $namespace.$table
            where lower(to_ascii($labelAttr)) like lower(to_ascii($1))||'%' order by $labelAttr limit 15";
  }

  protected function sqlParams($query) {
    $searchOptions = $this->mergeOptions($this->searchOptions(), $this->defaultSearchOptions());
    $params        = array($query);

    foreach($searchOptions['sqlParams'] as $param)
      $params[] = $param;

    return $params;
  }

  protected function loadResourcesBySearchQuery($query) {
    $results      = array();
    $numericQuery = preg_replace("/[^0-9]/", "", $query);

    if (! empty($numericQuery)) {
      $sqls   = $this->sqlsForNumericSearch();
      $params = $this->sqlParams($numericQuery);
    }
    else {

      // convertido query para latin1, para que pesquisas com acentuação funcionem.
      $query     = Portabilis_String_Utils::toLatin1($query, array('escape' => false));

      $sqls   = $this->sqlsForStringSearch();
      $params = $this->sqlParams($query);
    }

    if (! is_array($sqls))
      $sqls = array($sqls);

    foreach($sqls as $sql) {
      $_results = $this->fetchPreparedQuery($sql, $params, false);

      foreach($_results as $result) {
        if (! isset($results[$result['id']]))
          $results[$result['id']] = $this->formatResourceValue($result);
      }
    }

    return $results;
  }

  // formats the value of each resource, that will be returned in api as a label.

  protected function formatResourceValue($resource) {
    return $resource['id'] . ' - ' . $this->toUtf8($resource['name'], array('transform' => true));
  }


  // default api responders

  protected function search() {
    if ($this->canSearch())
      $resources = $this->loadResourcesBySearchQuery($this->getRequest()->query);

    if (empty($resources))
      $resources = array('' => 'Sem resultados.');

    return array('result' => $resources);
  }
}
