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

class PessoaController extends ApiCoreController
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


  protected function canSearch() {
    return $this->canAcceptRequest() &&
           $this->validatesPresenceOf('query');
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


  protected function loadPessoasBySearchQuery($query) {
    $alunos       = array();
    $numericQuery = preg_replace("/[^0-9]/", "", $query);

    if (! empty($numericQuery))
      $sqlQueries = $this->sqlQueriesForNumericSearch($numericQuery);
    else
      $sqlQueries = $this->sqlQueriesForStringSearch($query);

    foreach($sqlQueries as $sqlQuery){
      $_alunos = $this->fetchPreparedQuery($sqlQuery['sql'], $sqlQuery['params'], false);

      foreach($_alunos as $aluno) {
        $id = $aluno['id'];

        if (! isset($alunos[$id]))
          $alunos[$id] = $id . ' - ' . $aluno['nome'];
      }
    }

    return $alunos;
  }


  protected function sqlQueriesForNumericSearch($numericQuery) {
    $sqlQueries = array();

    // search by idpes or cpf
    $sql = "select distinct pessoa.idpes as id, pessoa.nome from cadastro.pessoa, cadastro.fisica
            where fisica.idpes = pessoa.idpes and (pessoa.idpes = $1 or fisica.cpf like $2) order by nome limit 15";

    $sqlQueries[] = array('sql' => $sql, 'params' => array($numericQuery, $numericQuery . "%"));

    // search by rg
    $sql = "select distinct pessoa.idpes as id, pessoa.nome from cadastro.pessoa, cadastro.documento
            where pessoa.idpes = documento.idpes and documento.rg like $1 order by nome limit 15";

    $sqlQueries[] = array('sql' => $sql, 'params' => array($numericQuery . "%"));

    return $sqlQueries;
  }


  protected function sqlQueriesForStringSearch($stringQuery) {
    $sqlQueries = array();

    // search by name
    $sql = "select distinct pessoa.idpes as id, pessoa.nome from cadastro.pessoa
            where lower(pessoa.nome) like $1 order by nome limit 15";

    $sqlQueries[] = array('sql' => $sql, 'params' => array(strtolower($stringQuery) ."%"));

    // TODO search by nome mae

    return $sqlQueries;
  }


  // api responders

  protected function search() {
      $alunos = array();

      if ($this->canSearch())
        $alunos = $this->loadPessoasBySearchQuery($this->getRequest()->query);

      return array('result' => $alunos);
    }


  public function Gerar() {
    if ($this->isRequestFor('get', 'pessoa-search'))
      $this->appendResponse($this->search());
    else
      $this->notImplementedOperationError();
  }
}
