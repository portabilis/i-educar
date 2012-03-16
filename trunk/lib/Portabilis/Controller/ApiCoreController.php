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


# TODO remover requires descenessários
#require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
#require_once 'Avaliacao/Service/Boletim.php';
#require_once 'App/Model/MatriculaSituacao.php';
#require_once 'RegraAvaliacao/Model/TipoPresenca.php';
#require_once 'RegraAvaliacao/Model/TipoParecerDescritivo.php';
#require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
#require_once 'include/portabilis/dal.php';
#require_once 'include/pmieducar/clsPmieducarHistoricoEscolar.inc.php';
#require_once 'include/pmieducar/clsPmieducarHistoricoDisciplinas.inc.php';

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
  }


  protected function validatesUserIsLoggedIn(){
    return $this->validator->validatesPresenceOf($this->getSession()->id_pessoa, '', 'Usuário deve estar logado');
  }


  protected function validatesPresenceAndValueInSetOfAtt(){
    return $this->validator->validatesPresenceOf($this->getRequest()->att, 'att') &&
           $this->validator->validatesValueInSetOf($this->getRequest()->att, $this->getExpectedAtts(), 'att');
  }


  protected function validatesPresenceAndValueInSetOfOper(){
    $msg = "Operação '{$this->getRequest()->oper}' não implementada.";

    return $this->validator->validatesPresenceOf($this->getRequest()->oper, 'oper') &&
           $this->validator->validatesValueInSetOf($this->getRequest()->oper, $this->getExpectedOpers(), 'oper',
                                                   $raiseExceptionOnFail = false, $msg);
  }


  protected function canAcceptRequest()
  {
    return $this->validatesUserIsLoggedIn() &&
           $this->validatesPresenceAndValueInSetOfAtt() &&
           $this->validatesPresenceAndValueInSetOfOper();
  }


  protected function notImplementedError()
  {
    $this->messenger->append("Operação '{$this->getRequest()->oper}' inválida para o att '{$this->getRequest()->att}'");
  }


  protected function appendResponse($name, $value){
    $this->response[$name] = $value;
  }


  protected function prepareResponse(){
    try {
      $msgs = array();
      $this->appendResponse('att', isset($this->getRequest()->att) ? $this->getRequest()->att : '');
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


  // subescrever nas classes filhas setando as operações permitidas, ex:
  // return array('resource1', 'resource2', 'resource3');
  protected function getExpectedAtts() {
    throw new CoreExt_Exception('É necessário sobrescrever o método "getExpectedAtts()" de ApiCoreController.');
  }


  // subescrever nas classes filhas setando as operações permitidas, ex:
  // return array('post', 'get', 'delete');
  protected function getExpectedOpers() {
    throw new CoreExt_Exception('É necessário sobrescrever o método "getExpectedOpers()" de ApiCoreController.');
  }


  /*  subescrever nas classes filhas setando as verificações desejadas e retornando a resposta,
      conforme oper (operação ex: get, post ou delete) e/ ou att (nome do recurso) recebidos ex:

      if ($this->getRequest()->oper == 'get')
      {
        // caso retorne apenas um recurso
        $this->appendResponse('matriculas', $matriculas);

        // ou para multiplos recursos, pode-se usar o argumento att
        if ($this->getRequest()->att == 'matriculas')
          $this->appendResponse('matriculas', $this->getMatriculas());
        elseif ($this->getRequest()->att == 'alunos')
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
}
