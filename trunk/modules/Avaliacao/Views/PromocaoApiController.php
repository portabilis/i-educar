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
 * @package   Avaliacao
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once 'Avaliacao/Service/Boletim.php';
require_once 'App/Model/MatriculaSituacao.php';
require_once 'RegraAvaliacao/Model/TipoPresenca.php';
require_once 'RegraAvaliacao/Model/TipoParecerDescritivo.php';

require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'Avaliacao/Fixups/CleanComponentesCurriculares.php';


class PromocaoApiController extends ApiCoreController
{
  protected $_dataMapper = 'Avaliacao_Model_NotaComponenteDataMapper';
  protected $_processoAp = 644;

  protected function canAcceptRequest() {
    return parent::canAcceptRequest()    &&
           $this->validatesUserIsAdmin() &&
           $this->validatesPresenceOf('ano_escolar');
  }

  protected function canDeleteOldComponentesCurriculares() {
    return $this->validatesPresenceOf('ano_escolar');
  }

  protected function canPostPromocaoMatricula() {
    return $this->validatesPresenceOf('instituicao_id') &&
           $this->validatesPresenceOf('matricula_id');
  }

  protected function canGetQuantidadeMatriculas() {
    return $this->validatesPresenceOf('instituicao_id') &&
           $this->validatesPresenceOf('ano_escolar');
  }

  protected function loadNextMatriculaId($currentMatriculaId) {
    $sql = "select m.cod_matricula from pmieducar.matricula as m, pmieducar.matricula_turma as mt
            where m.ano = $1 and m.ativo = 1 and m.aprovado = 3
            and mt.ref_cod_matricula = m.cod_matricula and mt.ativo = 1 and
            ref_cod_matricula > $2 order by ref_cod_matricula limit 1";

    $options = array('params'      => array($this->getRequest()->ano_escolar, $currentMatriculaId),
                     'return_only' => 'first-field');

    return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
  }


  function loadSituacaoArmazenadaMatricula($matriculaId) {
    $sql     = "select aprovado from pmieducar.matricula where cod_matricula = $1 limit 1";

    $options = array('params' => $matriculaId, 'return_only' => 'first-field');
    return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
  }


  protected function loadDadosMatricula($matriculaId){
    $sql = "select m.cod_matricula as matricula_id, m.ref_cod_aluno as aluno_id,
            m.ref_ref_cod_escola as escola_id, m.ref_cod_curso as curso_id,
            m.ref_ref_cod_serie as serie_id, mt.ref_cod_turma as turma_id, m.ano,
            m.aprovado from pmieducar.matricula  as m, pmieducar.matricula_turma as mt
            where mt.ref_cod_matricula = m.cod_matricula and mt.ativo = 1 and
            cod_matricula = $1 limit 1";

    $options = array('params' => $matriculaId, 'return_only' => 'first-row');
    return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
  }


  #TODO substituir este metodo por service->getComponentes()?
  protected function loadComponentesCurriculares($matriculaId){
    $dadosMatricula = $this->loadDadosMatricula($matriculaId);

    $anoEscolar = $dadosMatricula['ano'];
    $escolaId = $dadosMatricula['escola_id'];
    $turmaId = $dadosMatricula['turma_id'];

    $sql = "select cc.id, cc.nome from modules.componente_curricular_turma as cct,
            modules.componente_curricular as cc, pmieducar.escola_ano_letivo as al
            where cct.turma_id = $1 and cct.escola_id = $2 and
            cct.componente_curricular_id = cc.id and al.ano = $3 and
            cct.escola_id = al.ref_cod_escola and cc.instituicao_id = $4";

    $options = array('params' => array($turmaId, $escolaId, $anoEscolar, $this->getRequest()->instituicao_id));
    $componentesCurricularesTurma = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

    if (count($componentesCurricularesTurma))
      return $componentesCurricularesTurma;

    $sql = "select cc.id, cc.nome from pmieducar.turma as t, pmieducar.escola_serie_disciplina as esd,
            modules.componente_curricular as cc, pmieducar.escola_ano_letivo as al
            where t.cod_turma = $1 and esd.ref_ref_cod_escola = $2 and
            t.ref_ref_cod_serie = esd.ref_ref_cod_serie and esd.ref_cod_disciplina = cc.id and
            al.ano = $3 and cc.instituicao_id = $4 and esd.ref_ref_cod_escola = al.ref_cod_escola and
            t.ativo = 1 and esd.ativo = 1 and al.ativo = 1";

    $options = array('params' => array($turmaId, $escolaId, $anoEscolar, $this->getRequest()->instituicao_id));
    $componentesCurricularesSerie = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

    return $componentesCurricularesSerie;
  }

  protected function trySaveBoletimService() {
    try {
      $this->boletimService()->save();
    }
    catch (CoreExt_Service_Exception $e) {
      // excecoes ignoradas :( pois servico lanca excecoes de alertas, que não são exatamente erros.
      // error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
    }
  }

  protected function boletimService($matriculaId, $reload = false) {
    $matriculaId = $this->matriculaId();

    if (! isset($this->_boletimServices))
      $this->_boletimServices = array();

    if (! isset($this->_boletimServices[$matriculaId]) || $reload) {
      // set service
      try {
        $params = array('matricula' => $matriculaId, 'usuario' => $this->getSession()->id_pessoa);
        $this->_boletimServices[$matriculaId] = new Avaliacao_Service_Boletim($params);
      }
      catch (Exception $e) {
        $this->messenger->append("Erro ao instanciar serviço boletim para matricula {$matriculaId}: " .
                                 $e->getMessage(), 'error', true);
      }
    }

    // validates service
    if (is_null($this->_boletimServices[$matriculaId]))
      throw new CoreExt_Exception("Não foi possivel instanciar o serviço boletim para a matricula $matriculaId.");

    return $this->_boletimServices[$matriculaId];
  }

  protected function getNota($etapa = null, $componenteCurricularId){
    $nota = urldecode($this->boletimService()->getNotaComponente($componenteCurricularId, $etapa)->nota);
    return str_replace(',', '.', $nota);
  }

  protected function getEtapaParecer($etapaDefault) {
    if($etapaDefault != 'An' && ($this->boletimService()->getRegra()->get('parecerDescritivo') ==
                                 RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE ||
                                 $this->boletimService()->getRegra()->get('parecerDescritivo') ==
                                 RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL)) {
      $etapaDefault = 'An';
    }

    return $etapaDefault;
  }

  protected function getParecerDescritivo($etapa, $componenteCurricularId) {
    if ($this->boletimService()->getRegra()->get('parecerDescritivo') ==
        RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE ||
        $this->boletimService()->getRegra()->get('parecerDescritivo') ==
        RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE) {
      return $this->boletimService()->getParecerDescritivo($this->getEtapaParecer($etapa), $componenteCurricularId);
    }
    else
      return $this->boletimService()->getParecerDescritivo($this->getEtapaParecer($etapa));
  }


  protected function lancarFaltasNaoLancadas($matriculaId){
    $defaultValue            = 0;
    $cnsPresenca             = RegraAvaliacao_Model_TipoPresenca;
    $tpPresenca              = $this->boletimService()->getRegra()->get('tipoPresenca');

    $componentesCurriculares = $this->loadComponentesCurriculares($matriculaId);

    if($tpPresenca == $cnsPresenca::GERAL) {
      foreach(range(1, $this->boletimService()->getOption('etapas')) as $etapa){
        $hasNotaOrParecerInEtapa = false;

        foreach($componentesCurriculares as $cc){
          $nota    = $this->getNota($etapa, $cc['id']);
          $parecer = $this->getParecerDescritivo($etapa, $cc['id']);

          if(! $hasNotaOrParecerInEtapa && (trim($nota) != '' || trim($parecer) != '')) {
            $hasNotaOrParecerInEtapa = true;
            break;
          }
        }

        if($hasNotaOrParecerInEtapa) {
          $falta = $this->boletimService()->getFalta($etapa)->quantidade;

          if(is_null($falta)){
            $notaFalta = new Avaliacao_Model_FaltaGeral(array(
                    'quantidade' => $defaultValue,
                    'etapa' => $etapa
                ));

            $this->boletimService()->addFalta($notaFalta);
            $this->messenger->append("Lançado falta geral (valor $defaultValue) para etapa $etapa (matrícula $matriculaId)", 'notice');
          }
        }
      }//for etapa

    }
    elseif($tpPresenca == $cnsPresenca::POR_COMPONENTE){
      foreach(range(1, $this->boletimService()->getOption('etapas')) as $etapa){
        foreach($componentesCurriculares as $cc){
          $nota    = $this->getNota($etapa, $cc['id']);
          $parecer = $this->getParecerDescritivo($etapa, $cc['id']);

          if(trim($nota) != '' || trim($parecer) != ''){
            $falta = $this->boletimService()->getFalta($etapa, $cc['id'])->quantidade;

            if(is_null($falta)){
              $this->boletimService()->addFalta(
              $this->getFaltaComponente($etapa, $cc['id'], $defaultValue));

              $this->messenger->append("Lançado falta (valor $defaultValue) para etapa $etapa e componente curricular {$cc['id']} - {$cc['nome']} (matricula $matriculaId)", 'notice');
            }
          }
        }
      }

    }
    else
      throw new Exception("Tipo de presença desconhecido método lancarFaltasNaoLancadas");
  }

  protected function matriculaId(){
    return (isset($this->_matriculaId) ? $this->_matriculaId : $this->getRequest()->matricula_id);
  }


  protected function setMatriculaId($id){
    $this->_matriculaId = $id;
  }

  // api responders

  protected function getQuantidadeMatriculas(){
    if($this->canGetQuantidadeMatriculas()) {
      $sql = "select count(m.cod_matricula) from pmieducar.matricula as m,
              pmieducar.matricula_turma as mt where m.ano = $1 and
              m.ativo = 1 and m.aprovado = 3 and mt.ref_cod_matricula = m.cod_matricula and mt.ativo = 1";

      $options = array('params' => $this->getRequest()->ano_escolar, 'return_only' => 'first-field');
      return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
    }
  }

  protected function postPromocaoMatricula() {
    if ($this->canPostPromocaoMatricula()) {
      $proximoMatriculaId = $this->loadNextMatriculaId($this->matriculaId());
      $situacaoAnterior   = '';
      $novaSituacao       = '';

      if($this->matriculaId() == 0) {

        if (is_numeric($proximoMatriculaId)){
          $this->setMatriculaId($proximoMatriculaId);
          $proximoMatriculaId = $this->loadNextMatriculaId($this->matriculaId());
        }
        else
          $this->messenger->append('Sem matrículas em andamento para a seleção informada.', 'notice');

      }

      if($this->matriculaId() != 0 &&  is_numeric($this->matriculaId())) {
        $situacaoAnterior = $this->loadSituacaoArmazenadaMatricula($this->matriculaId());

        $this->lancarFaltasNaoLancadas($this->matriculaId());
        //$this->convertParecerToLatin1($matriculaId);

        $this->trySaveBoletimService();
        $novaSituacao = $this->loadSituacaoArmazenadaMatricula($this->matriculaId());

        if($situacaoAnterior != $novaSituacao) {
          if($novaSituacao == 1)
            $this->messenger->append("Matrícula {$this->matriculaId()} foi aprovada (situaçao antiga $situacaoAnterior)", 'success');
          elseif($novaSituacao == 2)
            $this->messenger->append("Matrícula {$this->matriculaId()} foi reprovada (situaçao antiga $situacaoAnterior)", 'success');
          else
            $this->messenger->append("Matrícula {$this->matriculaId()} teve a situação alterada de $novaSituacao para $situacaoAnterior)", 'notice');
        }
      }

      return array('proximo_matricula_id' => $proximoMatriculaId,
                   'situacao_anterior'    => $situacaoAnterior,
                   'nova_situacao'        => $novaSituacao);
    }
  }

  /* remove notas, medias notas e faltas lnçadas para componentes curriculares não mais vinculados
    as das turmas / séries para que os alunos destas possam ser promovidos */
  protected function deleteOldComponentesCurriculares() {
    if ($this->canDeleteOldComponentesCurriculares()) {
      CleanComponentesCurriculares::destroyOldResources($this->getRequest()->ano_escolar);

      $this->messenger->append("Removido notas, medias notas e faltas de antigos componentes curriculares, " .
                               "vinculados a turmas / séries.", 'notice');
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'quantidade_matriculas'))
      $this->appendResponse('quantidade_matriculas', $this->getQuantidadeMatriculas());

    elseif ($this->isRequestFor('post', 'promocao'))
      $this->appendResponse('result', $this->postPromocaoMatricula());

    elseif ($this->isRequestFor('delete', 'old_componentes_curriculares'))
      $this->appendResponse('result', $this->deleteOldComponentesCurriculares());

    else
      $this->notImplementedOperationError();
  }
}
