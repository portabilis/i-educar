<?php

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
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'App/Model/MatriculaSituacao.php';

class MatriculaController extends ApiCoreController
{

  protected function canGetMatriculas() {
    return $this->validatesId('escola') &&
           $this->validatesId('aluno');
  }

  protected function canDeleteAbandono() {
    return  $this->validatesPresenceOf('id') &&
            $this->validatesExistenceOf('matricula', $this->getRequest()->id);
  }

  // search options

  protected function searchOptions() {
    $escolaId = $this->getRequest()->escola_id ? $this->getRequest()->escola_id : 0;
    $ano      = $this->getRequest()->ano       ? $this->getRequest()->ano       : 0;

    return array('sqlParams'    => array($escolaId, $ano),
                 'selectFields' => array('aluno_id'));
  }

  protected function sqlsForNumericSearch() {
    // seleciona por (codigo matricula ou codigo aluno), opcionalmente por codigo escola e
    // opcionalmente por ano.
    return "select distinct ON (aluno.cod_aluno) aluno.cod_aluno as aluno_id,
            matricula.cod_matricula as id, pessoa.nome as name from pmieducar.matricula,
            pmieducar.aluno, cadastro.pessoa where aluno.cod_aluno = matricula.ref_cod_aluno and
            pessoa.idpes = aluno.ref_idpes and aluno.ativo = matricula.ativo and
            matricula.ativo = 1 and matricula.aprovado in (1, 2, 3, 4, 7, 8, 9) and
            (matricula.cod_matricula like $1||'%' or matricula.ref_cod_aluno like $1||'%') and
            (select case when $2 != 0 then matricula.ref_ref_cod_escola = $2 else 1=1 end) and
            (select case when $3 != 0 then matricula.ano = $3 else 1=1 end) limit 15";
  }


  protected function sqlsForStringSearch() {
    // seleciona por nome aluno, opcionalmente por codigo escola e opcionalmente por ano.
    return "select distinct ON (aluno.cod_aluno) aluno.cod_aluno as aluno_id,
            matricula.cod_matricula as id, pessoa.nome as name from pmieducar.matricula,
            pmieducar.aluno, cadastro.pessoa where aluno.cod_aluno = matricula.ref_cod_aluno and
            pessoa.idpes = aluno.ref_idpes and aluno.ativo = matricula.ativo and
            matricula.ativo = 1 and matricula.aprovado in (1, 2, 3, 4, 7, 8, 9) and
            lower(to_ascii(pessoa.nome)) like lower(to_ascii($1))||'%' and
            (select case when $2 != 0 then matricula.ref_ref_cod_escola = $2 else 1=1 end) and
            (select case when $3 != 0 then matricula.ano = $3 else 1=1 end) limit 15";
  }


  protected function formatResourceValue($resource) {
    $alunoId = $resource['aluno_id'];
    $nome    = $this->toUtf8($resource['name'], array('transform' => true));

    return $resource['id'] . " - ($alunoId) $nome";
  }


  // load

  protected function loadNomeEscola($escolaId) {
    $sql = "select nome from cadastro.pessoa, pmieducar.escola where idpes = ref_idpes and cod_escola = $1";
    $nome = $this->fetchPreparedQuery($sql, $escolaId, false, 'first-field');

    return $this->safeString($nome);
  }

  protected function loadNameFor($resourceName, $id){
    $sql = "select nm_{$resourceName} from pmieducar.{$resourceName} where cod_{$resourceName} = $1";
    $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

    return $this->safeString($nome);
  }

  protected function loadDadosForMatricula($matriculaId){
    $sql            = "select cod_matricula as id, ref_cod_aluno as aluno_id, matricula.ano,
                       escola.ref_cod_instituicao as instituicao_id, matricula.ref_ref_cod_escola
                       as escola_id, matricula.ref_cod_curso as curso_id, matricula.ref_ref_cod_serie
                       as serie_id, matricula_turma.ref_cod_turma as turma_id from
                       pmieducar.matricula_turma, pmieducar.matricula, pmieducar.escola where escola.cod_escola =
                       matricula.ref_ref_cod_escola and ref_cod_matricula = cod_matricula and ref_cod_matricula =
                       $1 and matricula.ativo = matricula_turma.ativo and matricula_turma.ativo = 1 order by
                       matricula_turma.sequencial limit 1";

    $dadosMatricula = $this->fetchPreparedQuery($sql, $matriculaId, false, 'first-row');

    $attrs          = array('id', 'aluno_id', 'ano', 'instituicao_id', 'escola_id',
                            'curso_id', 'serie_id', 'turma_id');

    return Portabilis_Array_Utils::filter($dadosMatricula, $attrs);
  }

  protected function tryLoadMatriculaTurma($matriculaId) {
    $sql            = "select ref_cod_turma as turma_id, turma.tipo_boletim from pmieducar.matricula_turma,
                       pmieducar.turma where ref_cod_turma = cod_turma and ref_cod_matricula = $1 and
                       matricula_turma.ativo = 1 limit 1";

    $matriculaTurma = $this->fetchPreparedQuery($sql, $matriculaId, false, 'first-row');

    if (is_array($matriculaTurma) and count($matriculaTurma) > 0) {
      $attrs                                     = array('turma_id', 'tipo_boletim');

      $matriculaTurma                            = Portabilis_Array_Utils::filter($matriculaTurma, $attrs);
      $matriculaTurma['nome_turma']              = $this->loadNameFor('turma', $matriculaTurma['turma_id']);
    }

    return $matriculaTurma;
  }

  protected function loadMatriculasAluno($alunoId, $escolaId) {
    // #TODO mostrar o nome da situação da matricula

    // seleciona somente matriculas em andamento, aprovado, reprovado, em exame, aprovado apos exame e retido faltas
    $sql = "select cod_matricula as id, ano, ref_cod_instituicao as instituicao_id, ref_ref_cod_escola as
            escola_id, ref_cod_curso as curso_id, ref_ref_cod_serie as serie_id from pmieducar.matricula,
            pmieducar.escola where cod_escola = ref_ref_cod_escola and ref_cod_aluno = $1 and ref_ref_cod_escola =
            $2 and matricula.ativo = 1 and matricula.aprovado in (1, 2, 3, 7, 8, 9) order by ano desc, id";

    $params     = array($alunoId, $escolaId);
    $matriculas = $this->fetchPreparedQuery($sql, $params, false);

    if (is_array($matriculas) && count($matriculas) > 0) {
      $attrs      = array('id', 'ano', 'instituicao_id', 'escola_id', 'curso_id', 'serie_id');
      $matriculas = Portabilis_Array_Utils::filterSet($matriculas, $attrs);

      foreach($matriculas as $key => $matricula) {
        $matriculas[$key]['nome_curso']                = $this->loadNameFor('curso', $matricula['curso_id']);
        $matriculas[$key]['nome_escola']               = $this->loadNomeEscola($this->getRequest()->escola_id);
        $matriculas[$key]['nome_serie']                = $this->loadNameFor('serie', $matricula['serie_id']);
        $matriculas[$key]['situacao']                  = '#TODO';
        $turma                                         = $this->tryLoadMatriculaTurma($matricula['id']);

        if (is_array($turma) and count($turma) > 0) {
          $matriculas[$key]['turma_id']                = $turma['turma_id'];
          $matriculas[$key]['nome_turma']              = $turma['nome_turma'];
          $matriculas[$key]['report_boletim_template'] = $turma['report_boletim_template'];
        }
      }
    }

    return $matriculas;
  }

  // api

  protected function get() {
    if ($this->canGet())
      return $this->loadDadosForMatricula($this->getRequest()->id);
  }


  protected function getMatriculas() {
    if ($this->canGetMatriculas()) {
      $matriculas = $this->loadMatriculasAluno($this->getRequest()->aluno_id, $this->getRequest()->escola_id);
      return array('matriculas' => $matriculas);
    }
  }

  protected function deleteAbandono() {
    if ($this->canDeleteAbandono()) {
      $matriculaId       = $this->getRequest()->id;
      $situacaoAndamento = App_Model_MatriculaSituacao::EM_ANDAMENTO;

      $sql = 'update pmieducar.matricula set aprovado = $1 where cod_matricula = $2';
      $this->fetchPreparedQuery($sql, array($situacaoAndamento, $matriculaId));

      $this->messenger->append('Abandono desfeito.', 'success');
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'matricula'))
      $this->appendResponse($this->get());

    elseif ($this->isRequestFor('get', 'matriculas'))
      $this->appendResponse($this->getMatriculas());

    elseif ($this->isRequestFor('get', 'matricula-search'))
      $this->appendResponse($this->search());

    elseif ($this->isRequestFor('delete', 'abandono'))
      $this->appendResponse($this->deleteAbandono());

    else
      $this->notImplementedOperationError();
  }
}
