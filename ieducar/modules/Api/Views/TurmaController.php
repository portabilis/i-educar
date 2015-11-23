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
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'Portabilis/Model/Report/TipoBoletim.php';
require_once "App/Model/IedFinder.php";
require_once 'include/funcoes.inc.php';

class TurmaController extends ApiCoreController
{
  // validators

  protected function validatesTurmaId() {
    return  $this->validatesPresenceOf('id') &&
            $this->validatesExistenceOf('turma', $this->getRequest()->id);
  }

  protected function canGetTurmasPorEscola() {
    return  $this->validatesPresenceOf('ano') &&
            $this->validatesPresenceOf('instituicao_id');
  }

  // validations

  protected function canGet() {
    return $this->canAcceptRequest() &&
           $this->validatesTurmaId();
  }

  protected function canGetAlunosMatriculadosTurma(){
    return  $this->validatesPresenceOf('instituicao_id') &&
            $this->validatesPresenceOf('turma_id');
  }

  // api

  protected function ordenaTurmaAlfabetica(){

    $codTurma = $this->getRequest()->id;

    $sql = "UPDATE pmieducar.matricula_turma SET sequencial_fechamento = 0 WHERE ref_cod_turma = $1";
    $this->fetchPreparedQuery($sql, $codTurma);

    return true;
  }

  protected function getTipoBoletim() {
  	$tipo = App_Model_IedFinder::getTurma($codTurma = $this->getRequest()->id);
  	$tipo = $tipo['tipo_boletim'];

    $tiposBoletim = Portabilis_Model_Report_TipoBoletim;

    $tipos = array(null                                          				        => 'indefinido',
                   $tiposBoletim::BIMESTRAL                      				        => 'portabilis_boletim',
                   $tiposBoletim::BIMESTRAL_MODELO_FICHA         				        => 'portabilis_ficha_individual_bimestral_duque',
                   $tiposBoletim::BIMESTRAL_CONCEITUAL           				        => 'portabilis_boletim_primeiro_ano_bimestral',
                   $tiposBoletim::BIMESTRAL_EDUCACAO_INFANTIL    				        => 'portabilis_boletim_bimestral_infantil_manual',
                   $tiposBoletim::TRIMESTRAL                     				        => 'portabilis_boletim_trimestral',
                   $tiposBoletim::TRIMESTRAL_CONCEITUAL          				        => 'portabilis_boletim_primeiro_ano_trimestral',
                   $tiposBoletim::SEMESTRAL                      				        => 'portabilis_boletim_semestral',
                   $tiposBoletim::SEMESTRAL_CONCEITUAL           				        => 'portabilis_boletim_conceitual_semestral',
                   $tiposBoletim::SEMESTRAL_CONCEITUAL_RETRATO   				        => 'portabilis_boletim_primeiro_ano_semestral_retrato',
                   $tiposBoletim::SEMESTRAL_EDUCACAO_INFANTIL    				        => 'portabilis_boletim_educ_infantil_semestral',
                   $tiposBoletim::PARECER_SEMESTRAL_MODELO1      				        => 'portabilis_boletim_parecer_semestral_modelo1',
                   $tiposBoletim::PARECER_DESCRITIVO_COMPONENTE  				        => 'portabilis_boletim_parecer',
                   $tiposBoletim::PARECER_DESCRITIVO_GERAL       				        => 'portabilis_boletim_parecer_geral',
                   $tiposBoletim::BIMESTRAL_MODELO2                             => 'portabilis_boletim_modelo2',
                   $tiposBoletim::BIMESTRAL_PACAJA               				        => 'portabilis_boletim_bimestral_pacaja',
                   $tiposBoletim::ANUAL                          				        => 'portabilis_boletim_anual',
                   $tiposBoletim::BIMESTRAL_SEM_EXAME            				        => 'portabilis_boletim_bimestral_sem_exame',
                   $tiposBoletim::EJA_BIMESTRAL_SEMESTRAL        				        => 'portabilis_boletim_eja_bimestral_semestral',
                   $tiposBoletim::PARECER_DESCRITIVO_GERAL_DUQUE 				        => 'portabilis_boletim_parecer_geral_duque',
                   $tiposBoletim::BIMESTRAL_CONCEITUAL_PARAUAPEBAS      		    => 'portabilis_boletim_bimestral_conceitual_parauapebas',
                   $tiposBoletim::BIMESTRAL_CONCEITUAL_SIMPLIFICADO_PARAUAPEBAS => 'portabilis_boletim_bimestral_conceitual_simplificado_parauapebas',
                   $tiposBoletim::BIMESTRAL_CONCEITUAL_RETRATO_CACADOR          => 'portabilis_boletim_primeiro_ano_bimestral_retrato_cacador',
                   $tiposBoletim::BIMESTRAL_RETRATO_PARAGOMINAS                 => 'portabilis_boletim_bimestral_paragominas',
                   $tiposBoletim::TRIMESTRAL_RECUPERACAO_PARALELA               => 'portabilis_boletim_trimestral_recuperacao_paralela',
                   $tiposBoletim::ACOMPANHAMENTO_EDUCACAO_INTANTIL              => 'portabilis_boletim_acompanhamento_educ_infantil',
                   $tiposBoletim::AVALIACAO_INFANTIL_SEMESTRAL                  => 'portabilis_boletim_avaliacao_infantil_balneario_camboriu',
                   $tiposBoletim::TRIMESTRAL_CONCEITUAL_PARECER                 => 'portabilis_boletim_conceitual_trimestral_parecer',
                   $tiposBoletim::BIMESTRAL_RECUPERACAO_SEMESTRAL               => 'portabilis_boletim_recuperacao_semestral',
                   $tiposBoletim::BIMESTRAL_CONCEITUAL_COCALDOSUL               => 'portabilis_boletim_bimestral_conceitual_cocaldosul',
                   $tiposBoletim::BOLETIM_6AO9_SAOMIGUELDOSCAMPOS               => 'portabilis_boletim_6ao9_saomigueldoscampos',
                   $tiposBoletim::TRIMESTRAL_CONCEITUAL_BC                      => 'portabilis_boletim_primeiro_ano_trimestral_bc',
    );

    return array('tipo-boletim' => $tipos[$tipo]);
  }

  protected function getTurmasPorEscola(){
    if($this->canGetTurmasPorEscola()){

      $ano = $this->getRequest()->ano;
      $instituicaoId = $this->getRequest()->instituicao_id;


      $sql = 'SELECT cod_turma as id, nm_turma as nome, ref_ref_cod_escola as escola_id
                FROM pmieducar.turma
                WHERE ref_cod_instituicao = $1
                AND ano = $2
                AND ativo = 1
                ORDER BY ref_ref_cod_escola, nm_turma';

      $turmas = $this->fetchPreparedQuery($sql, array($instituicaoId, $ano));

      $attrs = array('id', 'nome', 'escola_id');
      $turmas = Portabilis_Array_Utils::filterSet($turmas, $attrs);

      foreach ($turmas as &$turma) {
        $turma['nome'] = Portabilis_String_Utils::toUtf8($turma['nome']);
      }

      return array('turmas' => $turmas);
    }
  }

  protected function getAlunosMatriculadosTurma(){
    if($this->canGetAlunosMatriculadosTurma()){
      $instituicaoId = $this->getRequest()->instituicao_id;
      $turmaId       = $this->getRequest()->turma_id;
      $disciplinaId  = $this->getRequest()->disciplina_id;
      $dataMatricula = $this->getRequest()->data_matricula;

      $sql = "SELECT a.cod_aluno as id,
                     m.dependencia,
                     mt.sequencial_fechamento as sequencia
              FROM pmieducar.aluno a
              INNER JOIN pmieducar.matricula m ON m.ref_cod_aluno = a.cod_aluno
              INNER JOIN pmieducar.matricula_turma mt ON m.cod_matricula = mt.ref_cod_matricula
              INNER JOIN pmieducar.turma t ON mt.ref_cod_turma = t.cod_turma
              INNER JOIN cadastro.pessoa p ON p.idpes = a.ref_idpes
              WHERE m.ativo = 1
                AND a.ativo = 1
                AND t.ativo = 1
                AND t.ref_cod_instituicao = $1
                AND t.cod_turma  = $2
                AND (CASE WHEN coalesce($3, current_date)::date = current_date THEN mt.ativo = 1 else true END)
                AND (CASE WHEN mt.ativo = 0 THEN
                        mt.sequencial = ( select max(matricula_turma.sequencial)
                                                       from pmieducar.matricula_turma
                                                      where matricula_turma.ref_cod_matricula = mt.ref_cod_matricula
                                                        and matricula_turma.ref_cod_turma = mt.ref_cod_turma
                                                        and ($3::date >= matricula_turma.data_enturmacao::date
                                                            and $3::date < matricula_turma.data_exclusao::date)
                                                        and matricula_turma.ativo = 0
                                        )
                     ELSE true
                     END)";

      $params = array($instituicaoId, $turmaId, $dataMatricula);

      if(is_numeric($disciplinaId)){
        $params[] = $disciplinaId;
        $sql .= 'AND
                  CASE WHEN m.dependencia THEN
                    (
                      SELECT 1 FROM pmieducar.disciplina_dependencia dd
                      WHERE dd.ref_cod_matricula = m.cod_matricula
                      AND dd.ref_cod_disciplina = $4
                      LIMIT 1
                    ) IS NOT NULL
                  ELSE
                   (
                    SELECT 1 FROM pmieducar.dispensa_disciplina dd
                    WHERE dd.ativo = 1
                    AND dd.ref_cod_matricula = m.cod_matricula
                    AND dd.ref_cod_disciplina = $4
                    LIMIT 1
                  ) IS NULL
                END
        ';
      }

      $sql .= " ORDER BY mt.sequencial_fechamento, translate(upper(p.nome),'áéíóúýàèìòùãõâêîôûäëïöüÿçÁÉÍÓÚÝÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ','AEIOUYAEIOUAOAEIOUAEIOUYCAEIOUYAEIOUAOAEIOUAEIOUC')";

      $alunos = $this->fetchPreparedQuery($sql, $params);

      $attrs = array('id','dependencia', 'sequencia');
      $alunos = Portabilis_Array_Utils::filterSet($alunos, $attrs);

      foreach ($alunos as &$aluno) {
        $aluno['dependencia'] = dbBool($aluno['dependencia']);
      }

      return array('alunos' => $alunos);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'tipo-boletim'))
      $this->appendResponse($this->getTipoBoletim());
    else if($this->isRequestFor('get', 'ordena-turma-alfabetica'))
      $this->appendResponse($this->ordenaTurmaAlfabetica());
    else if($this->isRequestFor('get', 'turmas-por-escola'))
      $this->appendResponse($this->getTurmasPorEscola());
    else if($this->isRequestFor('get', 'alunos-matriculados-turma'))
      $this->appendResponse($this->getAlunosMatriculadosTurma());
    else
      $this->notImplementedOperationError();
  }
}
