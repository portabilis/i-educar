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
require_once 'intranet/include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Portabilis/Date/Utils.php';

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
    $escolaId       = $this->getRequest()->escola_id ? $this->getRequest()->escola_id : 0;
    $ano            = $this->getRequest()->ano       ? $this->getRequest()->ano       : 0;
    $andamento      = $this->getRequest()->andamento ? 1 : 0;
    $utilizaFiltroAbandonoTransferencia = $this->getRequest()->filtro_abandono_transferencia ? 1 : 0;

    return array('sqlParams'    => array($escolaId, $ano, $andamento, $utilizaFiltroAbandonoTransferencia),
                 'selectFields' => array('aluno_id'));
  }

  protected function sqlsForNumericSearch() {
    // seleciona por (codigo matricula ou codigo aluno), opcionalmente por codigo escola e
    // opcionalmente por ano.
    return "select aluno.cod_aluno as aluno_id,
            matricula.cod_matricula as id, 
            pessoa.nome as name
       from cadastro.pessoa
      inner join pmieducar.aluno on(pessoa.idpes = aluno.ref_idpes)
      inner join pmieducar.matricula on(aluno.cod_aluno = matricula.ref_cod_aluno)
      inner join pmieducar.escola on(escola.cod_escola = matricula.ref_ref_cod_escola)
      inner join pmieducar.instituicao on (escola.ref_cod_instituicao = instituicao.cod_instituicao)
        and aluno.ativo = 1
        and matricula.ativo = 1
        and (case when $4 = 1 then
              matricula.aprovado = 3
             else
              (case when $5 = 1 then
                (case when instituicao.permissao_filtro_abandono_transferencia then
                  matricula.aprovado in (1, 2, 3, 7, 8, 9)
                 else
                  matricula.aprovado in (1, 2, 3, 4, 6, 7, 8, 9)
                 end)
               else
                matricula.aprovado in (1, 2, 3, 4, 6, 7, 8, 9)
               end)
             end)
        and (case when aprovado = 4 then not exists (select * from pmieducar.matricula m where m.ativo = 1 and m.ano = matricula.ano and m.ref_cod_aluno = aluno.cod_aluno and m.ref_ref_cod_escola = matricula.ref_ref_cod_escola and m.aprovado <> 4 ) else true end)
        and (matricula.cod_matricula like $1||'%' or matricula.ref_cod_aluno like $1||'%') 
        and (select case when $2 != 0 then matricula.ref_ref_cod_escola = $2 else true end) 
        and (select case when $3 != 0 then matricula.ano = $3 else true end) limit 15";
  }


  protected function sqlsForStringSearch() {
    // seleciona por nome aluno, opcionalmente por codigo escola e opcionalmente por ano.
    return "select aluno.cod_aluno as aluno_id,
            matricula.cod_matricula as id, 
            pessoa.nome as name
       from cadastro.pessoa
      inner join pmieducar.aluno on(pessoa.idpes = aluno.ref_idpes)
      inner join pmieducar.matricula on(aluno.cod_aluno = matricula.ref_cod_aluno)
      inner join pmieducar.escola on(escola.cod_escola = matricula.ref_ref_cod_escola)
      inner join pmieducar.instituicao on (escola.ref_cod_instituicao = instituicao.cod_instituicao)
      where aluno.ativo = 1
        and matricula.ativo = 1
        and (case when $4 = 1 then
              matricula.aprovado = 3
             else
              (case when $5 = 1 then
                (case when instituicao.permissao_filtro_abandono_transferencia then
                  matricula.aprovado in (1, 2, 3, 7, 8, 9)
                 else
                  matricula.aprovado in (1, 2, 3, 4, 6, 7, 8, 9)
                 end)
               else
                matricula.aprovado in (1, 2, 3, 4, 6, 7, 8, 9)
               end)
             end)
        and (case when aprovado = 4 then not exists (select * from pmieducar.matricula m where m.ativo = 1 and m.ano = matricula.ano and m.ref_cod_aluno = aluno.cod_aluno and m.ref_ref_cod_escola = matricula.ref_ref_cod_escola and m.aprovado <> 4 ) else true end)
        and lower(to_ascii(pessoa.nome)) like '%'||lower(to_ascii($1))||'%' 
        and (select case when $2 != 0 then matricula.ref_ref_cod_escola = $2 else true end) 
        and (select case when $3 != 0 then matricula.ano = $3 else true end) limit 15";
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


  protected function canGetMovimentacaoEnturmacao() {
    return $this->validatesPresenceOf('ano');
  }

  protected function getMovimentacaoEnturmacao() {
    $ano = $this->getRequest()->ano;
    $escola = $this->getRequest()->escola;

    if ($this->canGetMovimentacaoEnturmacao()) {

      if (!$this->validatesPresenceOf('escola')) {
        $escola = 0;
      }

      $sql = "SELECT ref_cod_aluno AS aluno_id,
                     cod_matricula AS matricula_id,
                     aprovado AS situacao,
                     ativo AS ativo,
                     coalesce(updated_at::varchar, '') AS data_atualizacao
              FROM pmieducar.matricula
              WHERE ano = $1
                AND CASE WHEN $2 = 0 THEN TRUE ELSE ref_ref_cod_escola = $2 END";

      $params     = array($ano, $escola);
      $matriculas = $this->fetchPreparedQuery($sql, $params, false);

      if (is_array($matriculas) && count($matriculas) > 0) {
        $attrs      = array('aluno_id', 'matricula_id', 'situacao', 'data_atualizacao', 'ativo');
        $matriculas = Portabilis_Array_Utils::filterSet($matriculas, $attrs);

        foreach($matriculas as $key => $matricula) {
          $sql = "SELECT ref_cod_turma AS turma_id,
                         sequencial AS sequencial,
                         sequencial_fechamento AS sequencial_fechamento,
                         coalesce(data_enturmacao::date::varchar, '') AS data_entrada,
                         coalesce(data_exclusao::date::varchar, '') AS data_saida,
                         coalesce(updated_at::varchar, '') AS data_atualizacao
                  FROM pmieducar.matricula_turma
                  WHERE ref_cod_matricula = $1";

          $params      = array($matriculas[$key]['matricula_id']);
          $enturmacoes = $this->fetchPreparedQuery($sql, $params, false);

          if (is_array($enturmacoes) && count($enturmacoes) > 0) {
            $attrs      = array('turma_id', 'sequencial', 'sequencial_fechamento', 'data_entrada', 'data_saida', 'data_atualizacao');
            $enturmacoes = Portabilis_Array_Utils::filterSet($enturmacoes, $attrs);

            $matriculas[$key]['enturmacoes'] = $enturmacoes;
          }
        }
        return array('matriculas' => $matriculas);
      }
    }
  }

  protected function getFrequencia() {
    $cod_matricula = $this->getRequest()->id;
    $objBanco = new clsBanco();
    $frequencia = $objBanco->unicoCampo(" SELECT modules.frequencia_da_matricula({$cod_matricula}); ");
    return array('frequencia' => $frequencia);
  }

  protected function deleteAbandono() {
    if ($this->canDeleteAbandono()) {
      $matriculaId        = $this->getRequest()->id;
      $tipoSemAbandono    = null;
      $situacaoAndamento  = App_Model_MatriculaSituacao::EM_ANDAMENTO;

      $sql = 'update pmieducar.matricula set aprovado = $1, ref_cod_abandono_tipo = $2 where cod_matricula = $3';
      $this->fetchPreparedQuery($sql, array($situacaoAndamento, $tipoSemAbandono, $matriculaId));

      $params = array($matriculaId, 0);
      $sql = 'SELECT max(sequencial) as codigo FROM pmieducar.matricula_turma where ref_cod_matricula = $1 and ativo = $2';
      $sequencial = $this->fetchPreparedQuery($sql, $params, false, 'first-field');

      $sql = 'UPDATE pmieducar.matricula_turma set ativo = 1, transferido = false, remanejado = false, abandono = false, reclassificado = false where sequencial = $1 and ref_cod_matricula = $2';

      $params = array($sequencial, $matriculaId);
      $this->fetchPreparedQuery($sql, $params);


      $this->messenger->append('Abandono desfeito.', 'success');
    }
  }

  protected function deleteReclassificacao(){
    $matriculaId = $this->getRequest()->id;
    $matricula   = new clsPmieducarMatricula($matriculaId);
    $matricula   = $matricula->detalhe();
    $alunoId     = $matricula['ref_cod_aluno'];
    $situacaoAndamento  = App_Model_MatriculaSituacao::EM_ANDAMENTO;

    $sql = 'update pmieducar.matricula_turma set ativo = 1, reclassificado = NULL where ref_cod_matricula = $1';
    $this->fetchPreparedQuery($sql, array($matriculaId));

    $sql = 'update pmieducar.matricula set matricula_reclassificacao = 0, aprovado = $1 where cod_matricula = $2';
    $this->fetchPreparedQuery($sql, array($situacaoAndamento, $matriculaId));

    return array('aluno_id' => $alunoId);
  }

  protected function canPostReservaExterna(){
    return $this->validatesPresenceOf('instituicao_id') && $this->validatesPresenceOf('ano')
             && $this->validatesPresenceOf('curso_id')  && $this->validatesPresenceOf('serie_id')
             && $this->validatesPresenceOf('turma_turno_id') && $this->validatesPresenceOf('qtd_alunos')
             && $this->validatesPresenceOf('escola_id');
  }

  protected function postReservaExterna(){
    if($this->canPostReservaExterna()){

      $instituicaoId = $this->getRequest()->instituicao_id;
      $escolaId = $this->getRequest()->escola_id;
      $cursoId = $this->getRequest()->curso_id;
      $serieId = $this->getRequest()->serie_id;
      $turmaTurnoId = $this->getRequest()->turma_turno_id;
      $ano = $this->getRequest()->ano;
      $qtd_alunos = $this->getRequest()->qtd_alunos;

      $params = array($instituicaoId, $escolaId, $cursoId, $serieId, $turmaTurnoId, $ano);

      $sql = 'DELETE
                FROM pmieducar.quantidade_reserva_externa
                WHERE ref_cod_instituicao = $1
                AND ref_cod_escola = $2
                AND ref_cod_curso = $3
                AND ref_cod_serie = $4
                AND ref_turma_turno_id = $5
                AND ano = $6';

      $this->fetchPreparedQuery($sql, $params);

      $params[] = $qtd_alunos;

      $sql = ' INSERT INTO pmieducar.quantidade_reserva_externa VALUES ($1,$2,$3,$4,$5,$6,$7)';

      $this->fetchPreparedQuery($sql, $params);

      $this->messenger->append('Quantidade de alunos atualizada com sucesso!.', 'success');
    }
  }

  protected function validaDataEntrada(){
  	if(!Portabilis_Date_Utils::validaData($this->getRequest()->data_entrada)){
  		$this->messenger->append('Valor inválido para data de entrada ' . $this->getRequest()->data_entrada, 'error');
  		return false;
  	}else{
  		return true;
  	}
  }

  protected function validaDataSaida(){
  	if(!Portabilis_Date_Utils::validaData($this->getRequest()->data_saida)){
  		$this->messenger->append('Valor inválido para data de saída', 'error');
  		return false;
  	}else{
  		return true;
  	}
  }

  protected function postDataEntrada(){
  	if($this->validaDataEntrada()){
  		$matricula_id = $this->getRequest()->matricula_id;
  		$data_entrada = Portabilis_Date_Utils::brToPgSQL($this->getRequest()->data_entrada);
  		$matricula = new clsPmieducarMatricula($matricula_id);
  		$matricula->data_matricula = $data_entrada;
  		if($matricula->edita()){
  			$this->messenger->append('Data de entrada atualizada com sucesso.', 'success');
  		}
  	}
  }

  protected function postDataSaida(){
  	if($this->validaDataSaida()){
  		$matricula_id = $this->getRequest()->matricula_id;
  		$data_saida = Portabilis_Date_Utils::brToPgSQL($this->getRequest()->data_saida);
  		$matricula = new clsPmieducarMatricula($matricula_id);
  		$matricula->data_cancel = $data_saida;
  		if($matricula->edita()){
  			return $this->messenger->append('Data de saida atualizada com sucesso.', 'success');
  		}
  	}
  }
  protected function postSituacao(){
    if($this->validatesPresenceOf('matricula_id') && $this->validatesPresenceOf('nova_situacao')){
      $matriculaId = $this->getRequest()->matricula_id;
      $matricula = new clsPmieducarMatricula($matriculaId);

      $situacaoAntiga = $matricula->aprovado;
      $situacaoNova   = $this->getRequest()->nova_situacao;

      if($situacaoNova == App_Model_MatriculaSituacao::TRANSFERIDO || $situacaoNova == App_Model_MatriculaSituacao::ABANDONO){
        $enturmacoes = new clsPmieducarMatriculaTurma();
        $enturmacoes = $enturmacoes->lista($matriculaId, null, null, null, null, null, null, null, 1 );

        if($enturmacoes){

          foreach ($enturmacoes as $enturmacao) {
            $enturmacao = new clsPmieducarMatriculaTurma( $matriculaId, $enturmacao['ref_cod_turma'], 1, null, null, date("Y-m-d H:i:s"), 0, null, $enturmacao['sequencial']);
            if(!$enturmacao->edita()){
              return false;
            }

            if ($situacaoNova == App_Model_MatriculaSituacao::TRANSFERIDO)
              $enturmacao->marcaAlunoTransferido();
            elseif ($situacaoNova == App_Model_MatriculaSituacao::ABANDONO)
              $enturmacao->marcaAlunoAbandono();

          }
        }
      }elseif($situacaoNova == App_Model_MatriculaSituacao::APROVADO || $situacaoNova == App_Model_MatriculaSituacao::EM_ANDAMENTO || $situacaoNova == App_Model_MatriculaSituacao::REPROVADO){

        $params = array($matriculaId, 0);
        $sql = 'SELECT max(sequencial) as codigo FROM pmieducar.matricula_turma where ref_cod_matricula = $1 and ativo = $2';
        $sequencial = $this->fetchPreparedQuery($sql, $params, false, 'first-field');

        $sql = 'UPDATE pmieducar.matricula_turma set ativo = 1, transferido = false, remanejado = false, abandono = false, reclassificado = false where sequencial = $1 and ref_cod_matricula = $2';

        $params = array($sequencial, $matriculaId);
        $this->fetchPreparedQuery($sql, $params);

      }

      $matricula->aprovado = $this->getRequest()->nova_situacao;
      if($matricula->edita()){
        return $this->messenger->append('Situação da matrícula alterada com sucesso.', 'success');
      }
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'matricula'))
      $this->appendResponse($this->get());

    elseif ($this->isRequestFor('get', 'matriculas'))
      $this->appendResponse($this->getMatriculas());

    elseif ($this->isRequestFor('get', 'frequencia'))
      $this->appendResponse($this->getFrequencia());

    elseif ($this->isRequestFor('get', 'matricula-search'))
      $this->appendResponse($this->search());

    elseif ($this->isRequestFor('get', 'movimentacao-enturmacao'))
      $this->appendResponse($this->getMovimentacaoEnturmacao());

    elseif ($this->isRequestFor('delete', 'abandono'))
      $this->appendResponse($this->deleteAbandono());

    elseif ($this->isRequestFor('delete', 'reclassificacao'))
      $this->appendResponse($this->deleteReclassificacao());

    elseif ($this->isRequestFor('post', 'reserva-externa'))
      $this->appendResponse($this->postReservaExterna());

  	elseif ($this->isRequestFor('post', 'data-entrada'))
  	  $this->appendResponse($this->postDataEntrada());

  	elseif ($this->isRequestFor('post', 'data-saida'))
  	  $this->appendResponse($this->postDataSaida());

    elseif ($this->isRequestFor('post', 'situacao'))
      $this->appendResponse($this->postSituacao());

    else
      $this->notImplementedOperationError();
  }
}
