<?php
//error_reporting(E_ERROR);
//ini_set("display_errors", 1);
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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Avaliacao/Service/Boletim.php';
require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once 'Avaliacao/Model/FaltaComponenteDataMapper.php';
require_once 'Avaliacao/Model/FaltaGeralDataMapper.php';
require_once 'Avaliacao/Model/ParecerDescritivoComponenteDataMapper.php';
require_once 'Avaliacao/Model/ParecerDescritivoGeralDataMapper.php';
require_once 'RegraAvaliacao/Model/TipoPresenca.php';
require_once 'RegraAvaliacao/Model/TipoParecerDescritivo.php';

require_once 'Portabilis/String/Utils.php';

class DiarioController extends ApiCoreController
{
  protected $_processoAp        = 642;

  protected function getRegra($turmaId) {
    return App_Model_IedFinder::getRegraAvaliacaoPorTurma($turmaId);
  }

  protected function getComponentesPorMatricula($matriculaId) {
    return App_Model_IedFinder::getComponentesPorMatricula($matriculaId);
  }

  protected function validateComponenteCurricular($matriculaId, $componenteCurricularId){

    $componentes = $this->getComponentesPorMatricula($matriculaId);
    $componentes = CoreExt_Entity::entityFilterAttr($componentes, 'id', 'id');
    $valid = in_array($componenteCurricularId, $componentes);
    if(!$valid){
      throw new CoreExt_Exception(Portabilis_String_Utils::toLatin1("Componente curricular de código $componenteCurricularId não existe para essa turma/matrícula."));
    }
    return $valid;
  }

  protected function trySaveServiceBoletim($turmaId, $alunoId) {
    try {
      $this->serviceBoletim($turmaId, $alunoId)->save();
    }
    catch (CoreExt_Service_Exception $e) {
      // excecoes ignoradas :( pois servico lanca excecoes de alertas, que não são exatamente erros.
      // error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
    }
  }

  protected function findMatriculaByTurmaAndAluno($turmaId, $alunoId){
    $resultado = array();

    $sql = 'SELECT m.cod_matricula AS id
              FROM pmieducar.matricula m
              INNER JOIN pmieducar.matricula_turma mt ON m.cod_matricula = mt.ref_cod_matricula
              WHERE m.ativo = 1
              AND  mt.ref_cod_turma = $1
              AND m.ref_cod_aluno = $2
              LIMIT 1';

    $matriculaId = $this->fetchPreparedQuery($sql, array($turmaId, $alunoId), true, 'first-field');

    if(empty($matriculaId))
      throw new CoreExt_Exception(Portabilis_String_Utils::toLatin1("Não foi possível encontrar uma matrícula para o aluno {$alunoId}."));

    return $matriculaId;
  }

  protected function serviceBoletim($turmaId, $alunoId) {
    $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

    if (! isset($this->_boletimServiceInstances))
      $this->_boletimServiceInstances = array();

    // set service
    if (! isset($this->_boletimServiceInstances[$matriculaId])) {
      try {
        $params = array('matricula' => $matriculaId);
        $this->_boletimServiceInstances[$matriculaId] = new Avaliacao_Service_Boletim($params);
      }
      catch (Exception $e){
        $this->messenger->append(Portabilis_String_Utils::toLatin1("Erro ao instanciar serviço boletim para matricula {$matriculaId}: ") . $e->getMessage(), 'error', true);
      }
    }

    // validates service
    if (is_null($this->_boletimServiceInstances[$matriculaId]))
      throw new CoreExt_Exception(Portabilis_String_Utils::toLatin1("Não foi possivel instanciar o serviço boletim para a matrícula $matriculaId."));

    return $this->_boletimServiceInstances[$matriculaId];
  }

  protected function canPostNotas(){
    return $this->validatesPresenceOf('turmas') && $this->validatesPresenceOf('etapa');
  }

  protected function canPostFaltasPorComponente(){
    return $this->validatesPresenceOf('turmas') && $this->validatesPresenceOf('etapa');
  }

  protected function canPostFaltasGeral(){
    return $this->validatesPresenceOf('turmas') && $this->validatesPresenceOf('etapa');
  }

  protected function canPostPareceresPorEtapaComponente(){
    return $this->validatesPresenceOf('turmas') && $this->validatesPresenceOf('etapa');
  }

  protected function canPostPareceresAnualPorComponente(){
    return $this->validatesPresenceOf('turmas');
  }

  protected function canPostPareceresAnualGeral(){
    return $this->validatesPresenceOf('turmas');
  }

  protected function canPostPareceresPorEtapaGeral(){
    return $this->validatesPresenceOf('turmas') && $this->validatesPresenceOf('etapa');
  }

  protected function postNotas(){
    if($this->canPostNotas()){
      $turmas = $this->getRequest()->turmas;
      $etapa = $this->getRequest()->etapa;

      foreach ($turmas as $turma) {
        $turmaId = $turma['turma_id'];
        $alunos = $turma['alunos'];

        foreach ($alunos as $aluno) {
          $alunoId = $aluno['aluno_id'];

          $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

          if($this->validateMatricula($matriculaId)){
            $componentesCurriculares = $aluno['componentes_curriculares'];
            foreach ($componentesCurriculares as $componenteCurricular) {
              $componenteCurricularId = $componenteCurricular['componente_curricular_id'];

              if($this->validateComponenteCurricular($matriculaId, $componenteCurricularId)){

                $valor = $componenteCurricular['valor'];

                $array_nota = array(
                      'componenteCurricular' => $componenteCurricularId,
                      'nota'                 => $valor,
                      'etapa'                => $etapa,
                      'notaOriginal'         => $valor);

                $nota = new Avaliacao_Model_NotaComponente($array_nota);

                $this->serviceBoletim($turmaId, $alunoId)->addNota($nota);
                $this->trySaveServiceBoletim($turmaId, $alunoId);
              }
            }
          }
        }
      }

      $this->messenger->append('Notas postadas com sucesso!', 'success');
    }
  }

  protected function postFaltasPorComponente(){
    if($this->canPostFaltasPorComponente()){
      $turmas = $this->getRequest()->turmas;
      $etapa = $this->getRequest()->etapa;

      foreach ($turmas as $turma) {
        $turmaId = $turma['turma_id'];
        $alunos = $turma['alunos'];

        if($this->getRegra($turmaId)->get('tipoPresenca') != RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE){
          throw new CoreExt_Exception(Portabilis_String_Utils::toLatin1("A regra da turma $turmaId não permite lançamento de faltas por componente."));
        }

        foreach ($alunos as $aluno) {
          $alunoId = $aluno['aluno_id'];

          if($this->validateMatricula($matriculaId)){

            $componentesCurriculares = $aluno['componentes_curriculares'];
            foreach ($componentesCurriculares as $componenteCurricular) {
              $componenteCurricularId = $componenteCurricular['componente_curricular_id'];

              $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

              if($this->validateComponenteCurricular($matriculaId, $componenteCurricularId)){

                $faltas = $componenteCurricular['faltas'];

                $falta = new Avaliacao_Model_FaltaComponente(array(
                  'componenteCurricular' => $componenteCurricularId,
                  'quantidade'           => $faltas,
                  'etapa'                => $etapa
                ));

                $this->serviceBoletim($turmaId, $alunoId)->addFalta($falta);
                $this->trySaveServiceBoletim($turmaId, $alunoId);
              }
            }
          }
        }
      }

      $this->messenger->append('Faltas postadas com sucesso!', 'success');
    }
  }

  protected function postPareceresPorEtapaComponente(){
    if($this->canPostPareceresPorEtapaComponente()){
      $turmas = $this->getRequest()->turmas;
      $etapa = $this->getRequest()->etapa;

      foreach ($turmas as $turma) {
        $turmaId = $turma['turma_id'];
        $alunos = $turma['alunos'];

        if($this->getRegra($turmaId)->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE){
          throw new CoreExt_Exception(Portabilis_String_Utils::toLatin1("A regra da turma $turmaId não permite lançamento de pareceres por etapa e componente."));
        }

        foreach ($alunos as $aluno) {
          $alunoId = $aluno['aluno_id'];

          $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

          if($this->validateMatricula($matriculaId)){

            $componentesCurriculares = $aluno['componentes_curriculares'];
            foreach ($componentesCurriculares as $componenteCurricular) {
              $componenteCurricularId = $componenteCurricular['componente_curricular_id'];

              if($this->validateComponenteCurricular($matriculaId, $componenteCurricularId)){

                $parecer = $componenteCurricular['parecer'];

                $falta = new Avaliacao_Model_ParecerDescritivoComponente(array(
                  'componenteCurricular' => $componenteCurricularId,
                  'parecer'           => Portabilis_String_Utils::toLatin1($parecer),
                  'etapa'                => $etapa
                ));

                $this->serviceBoletim($turmaId, $alunoId)->addParecer($falta);
                $this->trySaveServiceBoletim($turmaId, $alunoId);
              }
            }
          }
        }
      }

      $this->messenger->append('Pareceres postados com sucesso!', 'success');
    }
  }

  protected function postPareceresAnualPorComponente(){
    if($this->canPostPareceresAnualPorComponente()){
      $turmas = $this->getRequest()->turmas;

      foreach ($turmas as $turma) {
        $turmaId = $turma['turma_id'];
        $alunos = $turma['alunos'];

        if($this->getRegra($turmaId)->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE){
          throw new CoreExt_Exception(Portabilis_String_Utils::toLatin1("A regra da turma $turmaId não permite lançamento de pareceres anual por componente."));
        }

        foreach ($alunos as $aluno) {
          $alunoId = $aluno['aluno_id'];

          $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

          if($this->validateMatricula($matriculaId)){

            $componentesCurriculares = $aluno['componentes_curriculares'];
            foreach ($componentesCurriculares as $componenteCurricular) {
              $componenteCurricularId = $componenteCurricular['componente_curricular_id'];

              if($this->validateComponenteCurricular($matriculaId, $componenteCurricularId)){

                $parecer = $componenteCurricular['parecer'];

                $falta = new Avaliacao_Model_ParecerDescritivoComponente(array(
                  'componenteCurricular' => $componenteCurricularId,
                  'parecer'           => Portabilis_String_Utils::toLatin1($parecer)
                ));

                $this->serviceBoletim($turmaId, $alunoId)->addParecer($falta);
                $this->trySaveServiceBoletim($turmaId, $alunoId);
              }
            }
          }
        }
      }

      $this->messenger->append('Pareceres postados com sucesso!', 'success');
    }
  }

  protected function postPareceresPorEtapaGeral(){
    if($this->canPostPareceresPorEtapaGeral()){
      $turmas = $this->getRequest()->turmas;
      $etapa = $this->getRequest()->etapa;

      foreach ($turmas as $turma) {
        $turmaId = $turma['turma_id'];
        $alunos = $turma['alunos'];

        if($this->getRegra($turmaId)->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL){
          throw new CoreExt_Exception(Portabilis_String_Utils::toLatin1("A regra da turma $turmaId não permite lançamento de pareceres por etapa geral."));
        }

        $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

        if($this->validateMatricula($matriculaId)){

          foreach ($alunos as $aluno) {
            $alunoId = $aluno['aluno_id'];
            $parecer = $aluno['parecer'];

            $falta = new Avaliacao_Model_ParecerDescritivoGeral(array(
              'parecer'           => Portabilis_String_Utils::toLatin1($parecer),
              'etapa'                => $etapa
            ));

            $this->serviceBoletim($turmaId, $alunoId)->addParecer($falta);
            $this->trySaveServiceBoletim($turmaId, $alunoId);
          }
        }
      }

      $this->messenger->append('Pareceres postados com sucesso!', 'success');
    }
  }

  protected function postPareceresAnualGeral(){
    if($this->canPostPareceresAnualGeral()){
      $turmas = $this->getRequest()->turmas;

      foreach ($turmas as $turma) {
        $turmaId = $turma['turma_id'];
        $alunos = $turma['alunos'];

        if($this->getRegra($turmaId)->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL){
          throw new CoreExt_Exception(Portabilis_String_Utils::toLatin1("A regra da turma $turmaId não permite lançamento de pareceres anual geral."));
        }

        foreach ($alunos as $aluno) {
          $alunoId = $aluno['aluno_id'];
          $parecer = $aluno['parecer'];

          $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

          if($this->validateMatricula($matriculaId)){

            $falta = new Avaliacao_Model_ParecerDescritivoGeral(array(
              'parecer'           => Portabilis_String_Utils::toLatin1($parecer)
            ));

            $this->serviceBoletim($turmaId, $alunoId)->addParecer($falta);
            $this->trySaveServiceBoletim($turmaId, $alunoId);
          }
        }
      }

      $this->messenger->append('Pareceres postados com sucesso!', 'success');
    }
  }

  protected function postFaltasGeral(){
    if($this->canPostFaltasPorComponente()){
      $turmas = $this->getRequest()->turmas;
      $etapa = $this->getRequest()->etapa;

      foreach ($turmas as $turma) {
        $turmaId = $turma['turma_id'];
        $alunos = $turma['alunos'];

        if($this->getRegra($turmaId)->get('tipoPresenca') != RegraAvaliacao_Model_TipoPresenca::GERAL){
          throw new CoreExt_Exception(Portabilis_String_Utils::toLatin1("A regra da turma $turmaId não permite lançamento de faltas geral."));
        }

        foreach ($alunos as $aluno) {
          $alunoId = $aluno['aluno_id'];
          $faltas = $aluno['faltas'];

          $matriculaId = $this->findMatriculaByTurmaAndAluno($turmaId, $alunoId);

          if($this->validateMatricula($matriculaId)){

            $falta = new Avaliacao_Model_FaltaGeral(array(
              'quantidade'           => $faltas,
              'etapa'                => $etapa
            ));

            $this->serviceBoletim($turmaId, $alunoId)->addFalta($falta);
            $this->trySaveServiceBoletim($turmaId, $alunoId);
          }
        }
      }

      $this->messenger->append('Faltas postadas com sucesso!', 'success');
    }
  }

  protected function validateMatricula($matriculaId){

    $ativo = false;

    if(!empty($matriculaId)){
      $sql = "SELECT m.ativo as ativo
                FROM pmieducar.matricula m
                WHERE m.cod_matricula = $1
                LIMIT 1";

      $ativo = $this->fetchPreparedQuery($sql, array($matriculaId), true, 'first-field');
    }

    return $ativo;
  }

  public function Gerar() {
    if ($this->isRequestFor('post', 'notas'))
      $this->appendResponse($this->postNotas());
    elseif ($this->isRequestFor('post', 'faltas-por-componente'))
      $this->appendResponse($this->postFaltasPorComponente());
    elseif ($this->isRequestFor('post', 'faltas-geral'))
      $this->appendResponse($this->postFaltasGeral());
    elseif ($this->isRequestFor('post', 'pareceres-por-etapa-e-componente'))
      $this->appendResponse($this->postPareceresPorEtapaComponente());
    elseif ($this->isRequestFor('post', 'pareceres-por-etapa-geral'))
      $this->appendResponse($this->postPareceresPorEtapaGeral());
    elseif ($this->isRequestFor('post', 'pareceres-anual-por-componente'))
      $this->appendResponse($this->postPareceresAnualPorComponente());
    elseif ($this->isRequestFor('post', 'pareceres-anual-geral'))
      $this->appendResponse($this->postPareceresAnualGeral());
    else
      $this->notImplementedOperationError();
  }
}
