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
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
//require_once 'include/pmieducar/clsPmieducarMatriculaTurma.inc.php';
//require_once 'Avaliacao/Service/Boletim.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
//require_once "Reports/Reports/BoletimReport.php";

class AlunoController extends ApiCoreController
{
  protected $_dataMapper  = null;

  #TODO definir este valor com mesmo código cadastro de tipo de exemplar?
  protected $_processoAp  = 0;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';


  /*protected function validatesUserIsLoggedIn() {
    #FIXME validar tokens API
    return true;
  }*/


  /*protected function canAcceptRequest() {
    return parent::canAcceptRequest();
  }*/


  protected function canPost() {
    return $this->canAcceptRequest() &&
           $this->validatesPresenceOf('pessoa_id');

           // #TODO continuar validando
  }


  protected function canPut() {
    return $this->canAcceptRequest()              &&
           $this->validatesPresenceOf('aluno_id') &&
           $this->validatesPresenceOf('pessoa_id');

           // #TODO continuar validando
  }


  // load resources

  protected function loadNomeAluno($alunoId = null) {
    if (is_null($alunoId))
      $alunoId = $this->getRequest()->aluno_id;

    $sql = "select nome from cadastro.pessoa, pmieducar.aluno where idpes = ref_idpes and cod_aluno = $1";
    $nome = $this->fetchPreparedQuery($sql, $alunoId, false, 'first-field');

    return $this->safeString($nome);
  }


  protected function loadNameFor($resourceName, $id){
    $sql = "select nm_{$resourceName} from pmieducar.{$resourceName} where cod_{$resourceName} = $1";
    $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

    return $this->safeString($nome);
  }


  // api responders

  protected function post() {
    $aluno = array();

    if ($this->canPost()) {
      // TODO do somenthing
    }

    return $aluno;
  }

  protected function put() {
    $aluno = array();

    if ($this->canPut()) {
      // TODO do somenthing
    }

    return $aluno;
  }


  public function Gerar() {

    // creates a new resource
    if ($this->isRequestFor('post', 'aluno'))
      $this->appendResponse('aluno', $this->post());

    // updates a resource
    elseif ($this->isRequestFor('put', 'aluno'))
      $this->appendResponse('aluno', $this->put());

    else
      $this->notImplementedOperationError();
  }
}
