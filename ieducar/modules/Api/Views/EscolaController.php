<?php

error_reporting(E_ERROR);
ini_set("display_errors", 1);
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
require_once 'Portabilis/Array/Utils.php';
require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Utils/Database.php';

class EscolaController extends ApiCoreController
{
  protected $_processoAp        = 561;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;


  protected function canChange() {
    return true;
  }

  protected function loadEscolaInepId($escolaId) {
    $dataMapper = $this->getDataMapperFor('educacenso', 'escola');
    $entity     = $this->tryGetEntityOf($dataMapper, $escolaId);

    return (is_null($entity) ? null : $entity->get('escolaInep'));
  }


  protected function createUpdateOrDestroyEducacensoEscola($escolaId) {
    $dataMapper = $this->getDataMapperFor('educacenso', 'escola');

    if (empty($this->getRequest()->escola_inep_id))
      $result = $this->deleteEntityOf($dataMapper, $escolaId);
    else {
      $data = array(
        'escola'      => $escolaId,
        'escolaInep'  => $this->getRequest()->escola_inep_id,

        // campos deprecados?
        'fonte'      => 'fonte',
        'nomeInep'   => '-',

        // always setting now...
        'created_at' => 'NOW()',
      );

      $entity = $this->getOrCreateEntityOf($dataMapper, $escolaId);
      $entity->setOptions($data);

      $result = $this->saveEntity($dataMapper, $entity);
    }

    return $result;
  }

  protected function get() {
    if ($this->canGet()) {
      $id = $this->getRequest()->id;

      $escola = array();
      $escola['escola_inep_id'] = $this->loadEscolaInepId($id);

      return $escola;
    }
  }

  protected function put() {
    $id = $this->getRequest()->id;

    if ($this->canPut()) {
      $this->createUpdateOrDestroyEducacensoEscola($id);

      $this->messenger->append('Cadastro alterado com sucesso', 'success', false, 'error');
    }
    else
      $this->messenger->append('Aparentemente o cadastro não pode ser alterado, por favor, verifique.',
                               'error', false, 'error');

    return array('id' => $id);
  }

  protected function canGetEscolas(){
    return $this->validatesPresenceOf('instituicao_id') && $this->validatesPresenceOf('ano')
             && $this->validatesPresenceOf('curso_id')  && $this->validatesPresenceOf('serie_id') 
             && $this->validatesPresenceOf('turma_turno_id');
  }

  protected function getEscolas(){
    if ($this->canGetEscolas()){
      $instituicaoId = $this->getRequest()->instituicao_id;
      $ano = $this->getRequest()->ano;
      $cursoId = $this->getRequest()->curso_id;
      $serieId = $this->getRequest()->serie_id;
      $turmaTurnoId = $this->getRequest()->turma_turno_id;

      $sql = " SELECT DISTINCT cod_escola

                FROM pmieducar.escola e
                INNER JOIN pmieducar.escola_curso ec ON (e.cod_escola = ec.ref_cod_escola)
                INNER JOIN pmieducar.curso c ON (c.cod_curso = ec.ref_cod_curso)
                INNER JOIN pmieducar.escola_serie es ON (es.ref_cod_escola = e.cod_escola)
                INNER JOIN pmieducar.serie s ON (s.cod_serie = es.ref_cod_serie)
                INNER JOIN pmieducar.turma t ON (s.cod_serie = t.ref_ref_cod_serie AND t.ref_ref_cod_escola = e.cod_escola )
                INNER JOIN pmieducar.escola_ano_letivo eal ON(e.cod_escola = eal.ref_cod_escola)
                WHERE t.ano = $1
                AND t.turma_turno_id = $2
                AND c.cod_curso = $3
                AND e.ref_cod_instituicao = $4
                AND s.cod_serie = $5
                AND ec.ativo = 1
                AND c.ativo = 1
                AND e.ativo = 1
                AND es.ativo = 1
                AND s.ativo = 1
                AND t.ativo = 1
                AND eal.ativo = 1
                AND eal.andamento = 1
				AND eal.ano = $1";
      $escolaIds = $this->fetchPreparedQuery($sql, array($ano, $turmaTurnoId, $cursoId, $instituicaoId, $serieId));

      foreach($escolaIds as $escolaId){
      	$this->messenger->append("Escola: " . $escolaId[0] . " Maximo de alunos no turno: " . $this->_getMaxAlunoTurno($escolaId[0]) . " Quantidade alunos fila: " . $this->_getQtdAlunosFila($escolaId[0]) . " Quantidade matriculas turno: " . $this->_getQtdMatriculaTurno($escolaId[0]));
      	if(!$this->existeVagasDisponiveis($escolaId[0])){
      		if (($key = array_search($escolaId, $escolaIds)) !== false) {
    			unset($escolaIds[$key]);
			}
      	}
      }
      if(empty($escolaIds)){
      	$this->messenger->append("Desculpe, mas aparentemente não existem mais vagas disponíveis para a seleção informada. Altere a seleção e tente novamente.");
      	return array( 'escolas' => 0);
      }
      else{
      $attrs = array('cod_escola');
      return array( 'escolas' => Portabilis_Array_Utils::filterSet($escolaIds, $attrs));
  	  }
    }
  }

    function existeVagasDisponiveis($escolaId){

    // Caso a capacidade de alunos naquele turno seja menor ou igual ao ao número de alunos matrículados + alunos na reserva de vaga externa deve bloquear
    if ($this->_getMaxAlunoTurno($escolaId) <= ($this->_getQtdAlunosFila($escolaId) + $this->_getQtdMatriculaTurno($escolaId) )){
      // $this->mensagem .= Portabilis_String_Utils::toLatin1("Não existem vagas disponíveis para essa série/turno!") . '<br/>';
      return false;
    }

    return true;
  }

  function _getQtdAlunosFila($escolaId){

    $sql = 'SELECT qtd_alunos FROM pmieducar.quantidade_reserva_externa WHERE ref_cod_instituicao = $1 AND ref_cod_escola = $2 AND ref_cod_curso = $3 AND ref_cod_serie = $4 AND ref_turma_turno_id = $5 ';

    return (int) Portabilis_Utils_Database::selectField($sql, array($this->getRequest()->instituicao_id, $escolaId,
    									                            $this->getRequest()->curso_id, $this->getRequest()->serie_id,
    									                            $this->getRequest()->turma_turno_id));
  }

  function _getQtdMatriculaTurno($escolaId){
    $obj_mt = new clsPmieducarMatriculaTurma();

    return count(array_filter(($obj_mt->lista($int_ref_cod_matricula = NULL, $int_ref_cod_turma = NULL,
              $int_ref_usuario_exc = NULL, $int_ref_usuario_cad = NULL,
              $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
              $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL, $int_ativo = 1,
              $int_ref_cod_serie = $this->ref_cod_serie, $int_ref_cod_curso = $this->getRequest()->curso_id,
              $int_ref_cod_escola = $escolaId,
              $int_ref_cod_instituicao = $this->getRequest()->instituicao_id, $int_ref_cod_aluno = NULL, $mes = NULL,
              $aprovado = NULL, $mes_menor_que = NULL, $int_sequencial = NULL,
              $int_ano_matricula = NULL, $tem_avaliacao = NULL, $bool_get_nome_aluno = FALSE,
              $bool_aprovados_reprovados = NULL, $int_ultima_matricula = NULL,
              $bool_matricula_ativo = 1, $bool_escola_andamento = true,
              $mes_matricula_inicial = FALSE, $get_serie_mult = FALSE,
              $int_ref_cod_serie_mult = NULL, $int_semestre = NULL,
              $pegar_ano_em_andamento = FALSE, $parar=NULL, $diario = FALSE, 
              $int_turma_turno_id = $this->getRequest()->turma_turno_id, $int_ano_turma = $this->getRequest()->ano))));
  }
  function _getMaxAlunoTurno($escolaId){
    $obj_t = new clsPmieducarTurma();
    $det_t = $obj_t->detalhe();

    $lista_t = $obj_t->lista($int_cod_turma = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, 
    $int_ref_ref_cod_serie = $this->getRequest()->serie_id, $int_ref_ref_cod_escola = $escolaId, $int_ref_cod_infra_predio_comodo = null, 
    $str_nm_turma = null, $str_sgl_turma = null, $int_max_aluno = null, $int_multiseriada = null, $date_data_cadastro_ini = null, 
    $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = 1, $int_ref_cod_turma_tipo = null, 
    $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $time_hora_inicio_intervalo_ini = null, 
    $time_hora_inicio_intervalo_fim = null, $time_hora_fim_intervalo_ini = null, $time_hora_fim_intervalo_fim = null, $int_ref_cod_curso = $this->getRequest()->curso_id, $int_ref_cod_instituicao = null, 
    $int_ref_cod_regente = null, $int_ref_cod_instituicao_regente = null, $int_ref_ref_cod_escola_mult = null, $int_ref_ref_cod_serie_mult = null, $int_qtd_min_alunos_matriculados = null, 
    $bool_verifica_serie_multiseriada = false, $bool_tem_alunos_aguardando_nota = null, $visivel = null, $turma_turno_id = $this->getRequest()->turma_turno_id, $tipo_boletim = null, $ano = $this->getRequest()->ano, $somenteAnoLetivoEmAndamento = FALSE);

    $max_aluno_turmas = 0;

    foreach ($lista_t as $reg) {
      $max_aluno_turmas += $reg['max_aluno'];
    }

    return $max_aluno_turmas;
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'escola'))
      $this->appendResponse($this->get());

    elseif ($this->isRequestFor('put', 'escola'))
      $this->appendResponse($this->put());

    elseif ($this->isRequestFor('get', 'escolas'))
      $this->appendResponse($this->getEscolas());

    else
      $this->notImplementedOperationError();
  }
}