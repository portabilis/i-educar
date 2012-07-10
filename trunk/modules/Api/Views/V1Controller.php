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
require_once 'include/pmieducar/clsPmieducarMatriculaTurma.inc.php';
#require_once 'Biblioteca/Model/TipoExemplarDataMapper.php';
require_once 'lib/Portabilis/Array/Utils.php';

class V1Controller extends ApiCoreController
{
  protected $_dataMapper  = null;

  #TODO definir este valor com mesmo código cadastro de tipo de exemplar?
  protected $_processoAp  = 0;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';


  protected function validatesUserIsLoggedIn() {

    #FIXME validar tokens API
    return true;
  }


  protected function canAcceptRequest() {
    return parent::canAcceptRequest() &&
           $this->validatesPresenceOf(array('aluno_id', 'escola_id'));
  }


  // load resources

  protected function loadNomeAluno() {
    $sql = "select nome from cadastro.pessoa, pmieducar.aluno where idpes = ref_idpes and cod_aluno = $1";
    $nome = $this->fetchPreparedQuery($sql, $this->getRequest()->aluno_id, false, 'first-field');

    return ucwords(strtolower(utf8_encode($nome)));
  }

  protected function loadNomeSerie($serieId){
    $sql = "select nm_serie from pmieducar.serie where cod_serie = $1";
    $nome = $this->fetchPreparedQuery($sql, $serieId, false, 'first-field');

    return ucwords(strtolower(utf8_encode($nome)));
  }


  protected function _loadDadosForMatricula($matricula) {
    $matriculaTurma = new clsPmieducarMatriculaTurma();

    // atualmente somente carrega as matriculas de determinada escola
    $matriculaTurma = $matriculaTurma->lista(
      $matricula['id'],
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      1,
      $matricula['serie_id'],
      $matricula['curso_id'],
      $matricula['escola_id'],
      null,
      $matricula['aluno_id']
    );

    $matriculaTurma = $matriculaTurma[0];

    if (is_array($matriculaTurma) && count($matriculaTurma) > 0){
      $attrs = array('ref_cod_instituicao' => 'instituicao_id',
                     'ref_ref_cod_escola'  => 'escola_id',
                     'ref_ref_cod_serie'   => 'serie_id',
                     'ref_cod_matricula'   => 'matricula_id',
                     'nm_curso'            => 'nome_instituicao',
                     'nm_curso'            => 'nome_escola',
                     'nm_curso'            => 'nome_curso',
                     'nm_turma'            => 'nome_turma');

      $matriculaTurma               = Portabilis_Array_Utils::filter($matriculaTurma, $attrs);

      $matriculaTurma['ano']        = $matricula['ano'];
      $matriculaTurma['nome_serie'] = strtolower(utf8_decode($this->loadNomeSerie($matriculaTurma['serie_id'])));
      $matriculaTurma['nome_curso'] = ucwords(strtolower($matriculaTurma['nome_curso']));
      $matriculaTurma['nome_turma'] = ucwords(strtolower(utf8_decode($matriculaTurma['nome_turma'])));
    }
    else{
      throw new Exception("Não foi possivel recuperar a matricula: {$matricula['id']}.");
    }

    return $matriculaTurma;
  }


  protected function loadMatriculas() {
    $matriculas = array();

    $sql = "select ano, cod_matricula as id, ref_ref_cod_escola as escola_id, ref_cod_curso as curso_id, ref_ref_cod_serie as serie_id from pmieducar.matricula where ref_cod_aluno = $1 and ref_ref_cod_escola = $2 and ativo = 1 order by ano, id";
    $params = array($this->getRequest()->aluno_id, $this->getRequest()->escola_id);

    $_matriculas = $this->fetchPreparedQuery($sql, $params, false);

    if (is_array($_matriculas)) {
      foreach($_matriculas as $matricula) {
        $matriculas[] = $this->_loadDadosForMatricula($matricula);
      }
    }

    return $matriculas;
  }


  // api responder

  protected function getAluno() {
    $aluno = array('id'         => $this->getRequest()->aluno_id, 
                   'nome'       => $this->loadNomeAluno(), 
                   'matriculas' => $this->loadMatriculas());

    $aluno['nome'] = ucwords(strtolower(utf8_encode($aluno['nome'])));

    return $aluno;
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'aluno'))
      $this->appendResponse('aluno', $this->getAluno());
    else
      $this->notImplementedOperationError();
  }
}
