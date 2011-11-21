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

require_once 'Core/Controller/Page/EditController.php';
require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once 'Avaliacao/Service/Boletim.php';
require_once 'App/Model/MatriculaSituacao.php';
require_once 'RegraAvaliacao/Model/TipoPresenca.php';
require_once 'RegraAvaliacao/Model/TipoParecerDescritivo.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
require_once 'include/portabilis/dal.php';

class PromocaoAjaxController extends Core_Controller_Page_EditController
{
  protected $_dataMapper  = 'Avaliacao_Model_NotaComponenteDataMapper';
  protected $_processoAp  = 644;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';


  protected function validatesPresenceOf(&$value, $name, $raiseExceptionOnEmpty = false, $msg = '', $addMsgOnEmpty = true){
    if (! isset($value) || (empty($value) && !is_numeric($value))){
      if ($addMsgOnEmpty)
      {
        $msg = empty($msg) ? "É necessário receber uma variavel '$name'" : $msg;
        $this->appendMsg($msg);
      }

      if ($raiseExceptionOnEmpty)
         throw new Exception($msg);

      return false;
    }
    return true;
  }

  protected function validatesValueIsNumeric(&$value, $name, $raiseExceptionOnError = false, $msg = '', $addMsgOnError = true){
    if (! is_numeric($value)){
      if ($addMsgOnError)
      {
        $msg = empty($msg) ? "O valor recebido para variavel '$name' deve ser numerico" : $msg;
        $this->appendMsg($msg);
      }

      if ($raiseExceptionOnError)
         throw new Exception($msg);

      return false;
    }
    return true;
  }

  protected function validatesValueInSetOf(&$value, $setExpectedValues, $name, $raiseExceptionOnError = false, $msg = ''){
    if (! in_array($value, $setExpectedValues)){
      $msg = empty($msg) ? "Valor recebido na variavel '$name' é invalido" : $msg;
      $this->appendMsg($msg);

      if ($raiseExceptionOnError)
         throw new Exception($msg);

      return false;
    }
    return true;
  }


  protected function requiresLogin($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getSession()->id_pessoa, '', $raiseExceptionOnEmpty, 'Usuário deve estar logado');
  }

  protected function requiresUserIsAdmin($raiseExceptionOnError){

    if($this->getSession()->id_pessoa != 1){
      $msg = "O usuário logado deve ser o admin";
      $this->appendMsg($msg);

      if ($raiseExceptionOnError)
         throw new Exception($msg);

      return false;
    }
    return true;
  }

  protected function validatesPresenceOfInstituicaoId($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getRequest()->instituicao_id, 'instituicao_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfEscolaId($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getRequest()->escola_id, 'escola_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfCursoId($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getRequest()->curso_id, 'curso_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfSerieId($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getRequest()->serie_id, 'serie_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfTurmaId($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getRequest()->turma_id, 'turma_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfAnoEscolar($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getRequest()->ano_escolar, 'ano_escolar', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfAlunoId($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getRequest()->aluno_id, 'aluno_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfMatriculaId($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getRequest()->matricula_id, 'matricula_id', $raiseExceptionOnEmpty);
  }

  protected function validatesPresenceOfEtapa($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getRequest()->etapa, 'etapa', $raiseExceptionOnEmpty);
  }

  protected function validatesValueOfEtapaForParecer($raiseExceptionOnError)
  {
    if($this->getRequest()->etapa != 'An' && ($this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE || $this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL)){
      $msg = "Valor inválido para o atributo 'etapa', é esperado o valor 'An' e foi recebido '{$this->getRequest()->etapa}'.";
    }
    elseif($this->getRequest()->etapa == 'An' && ($this->getService()->getRegra()->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE && $this->getService()->getRegra()->get('parecerDescritivo') != RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL)){
      $msg = "Valor inválido para o atributo 'etapa', é esperado o valor diferente de 'An'.";
    }
    else
      return true;

    $this->appendMsg($msg);

    if ($raiseExceptionOnEmpty)
       throw new Exception($msg);

    return false;
  }

  protected function validatesValueOfAttValueIsNumeric($raiseExceptionOnError){
    return $this->validatesValueIsNumeric($this->getRequest()->att_value, 'att_value', $raiseExceptionOnError);
  }

  protected function validatesPresenceOfAttValue($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getRequest()->att_value, 'att_value', $raiseExceptionOnEmpty);
  }


  protected function validatesPresenceAndValueInSetOfAtt($raiseExceptionOnError){
    $result = $this->validatesPresenceOf($this->getRequest()->att, 'att', $raiseExceptionOnError);

    if ($result){
      $expectedAtts = array('matriculas', 'quantidade_matriculas', 'promocao');
      /*'nota', 'nota_exame', 'falta', 'parecer', 'opcoes_notas', 'opcoes_faltas', 'regra_avaliacao'*/
      $result = $this->validatesValueInSetOf($this->getRequest()->att, $expectedAtts, 'att', $raiseExceptionOnError);
    }
    return $result;
  }


  protected function validatesPresenceAndValueInSetOfOper($raiseExceptionOnError){
    $result = $this->validatesPresenceOf($this->getRequest()->oper, 'oper', $raiseExceptionOnError);

    if ($result){
      $expectedOpers = array('post', 'get', 'delete');
      $result = $this->validatesValueInSetOf($this->getRequest()->oper, $expectedOpers, 'oper', $raiseExceptionOnError);
    }
    return $result;
  }

  /* esta funcao só pode ser chamada após setar $this->getService() */
  protected function validatesPresenceOfComponenteCurricularId($raiseExceptionOnEmpty, $addMsgOnEmpty = true)
  {
    return $this->validatesPresenceOf($this->getRequest()->componente_curricular_id, 'componente_curricular_id', $raiseExceptionOnEmpty, $msg = '', $addMsgOnEmpty);
  }

  protected function canAcceptRequest()
  {
    try {
      $this->requiresLogin(true);
      $this->requiresUserIsAdmin(true);
      $this->validatesPresenceAndValueInSetOfAtt(true);
      $this->validatesPresenceAndValueInSetOfOper(true);
    }
    catch (Exception $e){
      return false;
    }
    return true;
  }

  protected function canGetMatriculas(){
    try {
      $this->validatesPresenceOfInstituicaoId(true);
      #$this->validatesPresenceOfEscolaId(true);
      #$this->validatesPresenceOfCursoId(true);
      #$this->validatesPresenceOfSerieId(true);
      #$this->validatesPresenceOfTurmaId(true);
      $this->validatesPresenceOfAnoEscolar(true);
      #$this->validatesPresenceOfComponenteCurricularId(true);
      #$this->validatesPresenceOfEtapa(true);
    }
    catch (Exception $e){
      return false;
    }
    return true;
  }


  protected function canGetQuantidadeMatriculas(){
    try {
      $this->validatesPresenceOfInstituicaoId(true);
      $this->validatesPresenceOfAnoEscolar(true);
    }
    catch (Exception $e){
      return false;
    }
    return true;
  }


  protected function canPost(){
    return $this->validatesPresenceOfEtapa(false);
  }

  protected function canPostNota(){

    $canPost = $this->setService() &&
               $this->canPost() &&                
               $this->validatesValueOfAttValueIsNumeric(false) &&
               $this->validatesPresenceOfComponenteCurricularId(false);

    if ($canPost && $this->getService()->getRegra()->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NENHUM)
    {
      $canPost = false;
      $this->appendMsg("Nota não lançada, pois a regra de avaliação não utiliza nota.");
    }
    elseif ($canPost && $this->getRequest()->etapa == 'Rc' && is_null($this->getService()->getRegra()->formulaRecuperacao))
    {
      $canPost = false;
      $this->appendMsg("Nota de recuperação não lançada, pois a fórmula de recuperação não possui fórmula de recuperação.");
    }
    elseif ($canPost && $this->getRequest()->etapa == 'Rc' && $this->getService()->getRegra()->formulaRecuperacao->get('tipoFormula') != FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO)
    {
      $canPost = false;
      $this->appendMsg("Nota de recuperação não lançada, pois a fórmula de recuperação é diferente do tipo média recuperação.");
    }
    
    return $canPost;
  }


  protected function canPostFalta(){
    return $this->canPost() && $this->validatesValueOfAttValueIsNumeric(false);
  }


  protected function canPostParecer(){
    $canPost = $this->canPost() &&
               $this->validatesPresenceOfAttValue(false) &&
               $this->setService() && 
               $this->validatesValueOfEtapaForParecer();

    if ($canPost){
      $tpParecer = $this->getService()->getRegra()->get('parecerDescritivo');
      $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

      if ($tpParecer == $cnsParecer::NENHUM){
        $canPost = false;
        $this->appendMsg("Parecer descritivo não lançado, pois a regra de avaliação não utiliza parecer.");
      } 
      elseif ($tpParecer == $cnsParecer::ETAPA_COMPONENTE || $tpParecer == $cnsParecer::ANUAL_COMPONENTE){
        $canPost = $this->validatesPresenceOfComponenteCurricularId(false);
      }
    }

    return $canPost;
  }


  protected function canPostPromocaoMatricula(){
      return $this->validatesPresenceOfInstituicaoId(false) &&
             $this->validatesPresenceOfAnoEscolar(false) &&
             $this->validatesPresenceOfMatriculaId(false);
  }


  protected function canDelete(){
    try {
      $this->validatesPresenceOfEtapa(true);
    }
    catch (Exception $e){
      return false;
    }
    return true;
  }


  protected function canDeleteNota(){
    return $this->canDelete();
  }


  protected function canDeleteFalta(){
    return $this->canDelete();
  }


  protected function canDeleteParecer(){
    $canDelete = $this->canDelete() &&
                 $this->setService() && 
                 $this->validatesValueOfEtapaForParecer();

    if ($canDelete)
    {
      $tpParecer = $this->getService()->getRegra()->get('parecerDescritivo');
      $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

      if (($tpParecer == $cnsParecer::ANUAL_COMPONENTE || $tpParecer == $cnsParecer::ETAPA_COMPONENTE))
        $canDelete = $this->validatesPresenceOfComponenteCurricularId(false);
    }
    return $canDelete;
  }


  protected function deleteNota(){
    if ($this->canDeleteNota() &&
        $this->setService() &&
        $this->validatesPresenceOfComponenteCurricularId(false)){
      if (is_null($this->getNotaAtual()))
        $this->appendMsg('Nota matricula '. $this->getRequest()->matricula_id .' inexistente ou já removida.', 'notice');
      else
      {
        $this->getService()->deleteNota($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id);
        $this->saveService();
        $this->appendMsg('Nota matricula '. $this->getRequest()->matricula_id .' removida com sucesso.', 'success');
      }
    }
  }


  protected function deleteFalta(){
    $canDelete = $this->canDeleteFalta() && $this->setService();
    $cnsPresenca = RegraAvaliacao_Model_TipoPresenca;
    $tpPresenca = $this->getService()->getRegra()->get('tipoPresenca');

    if ($canDelete && $tpPresenca == $cnsPresenca::POR_COMPONENTE){
      $canDelete = $this->validatesPresenceOfComponenteCurricularId(false);
      $componenteCurricularId = $this->getRequest()->componente_curricular_id;
    }
    else
      $componenteCurricularId = null;

    if ($canDelete && is_null($this->getFaltaAtual())){
      $this->appendMsg('Falta matricula '. $this->getRequest()->matricula_id .' inexistente ou já removida.', 'notice');
    }
    elseif ($canDelete){
      $this->getService()->deleteFalta($this->getRequest()->etapa, $componenteCurricularId);
      $this->saveService();
      $this->appendMsg('Falta matricula '. $this->getRequest()->matricula_id .' removida com sucesso.', 'success');
    }
  }


  protected function deleteParecer(){
    if ($this->canDeleteParecer()){
      $parecerAtual = $this->getParecerAtual();

      if ((is_null($parecerAtual) || $parecerAtual == '')){
        $this->appendMsg('Parecer descritivo matricula '. $this->getRequest()->matricula_id .' inexistente ou já removido.', 'notice');
      }
      else{
        $tpParecer = $this->getService()->getRegra()->get('parecerDescritivo');
        $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

        if ($tpParecer == $cnsParecer::ANUAL_COMPONENTE || $tpParecer == $cnsParecer::ETAPA_COMPONENTE)
          $this->getService()->deleteParecer($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id);
        else
          $this->getService()->deleteParecer($this->getRequest()->etapa);

        $this->saveService();
        $this->appendMsg('Parecer descritivo matricula '. $this->getRequest()->matricula_id .' removido com sucesso.', 'success');
      }
    }
  }


  protected function postNota(){
    if ($this->canPostNota()){

      $nota = new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => $this->getRequest()->componente_curricular_id,
        'nota' => urldecode($this->getRequest()->att_value),
        'etapa' => $this->getRequest()->etapa
        ));

      $this->getService()->addNota($nota);
      $this->saveService();
      $this->appendMsg('Nota matricula '. $this->getRequest()->matricula_id .' alterada com sucesso.', 'success');
    }
  }


  protected function getFaltaGeral($etapa, $quantidade){
    return new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => $quantidade,
        'etapa' => $etapa
    ));
  }


  protected function getFaltaComponente($etapa, $componenteCurricularId, $quantidade){
    return new Avaliacao_Model_FaltaComponente(array(
            'componenteCurricular' => $componenteCurricularId,
            'quantidade' => $quantidade,
            'etapa' => $etapa
    ));
  }


  protected function postFalta(){

    $canPost = $this->canPostFalta() && $this->setService();
    if ($canPost && $this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
      $canPost = $this->validatesPresenceOfComponenteCurricularId(false);

    if ($canPost){
      if ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
        $falta = $this->getFaltaComponente();
      elseif ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
        $falta = $this->getFaltaGeral();

      $this->getService()->addFalta($falta);
      $this->saveService();
      $this->appendMsg('Falta matricula '. $this->getRequest()->matricula_id .' alterada com sucesso.', 'success');
    }
  }


  protected function getParecerComponente(){
    return new Avaliacao_Model_ParecerDescritivoComponente(array(
              'componenteCurricular' => $this->getRequest()->componente_curricular_id,
              'parecer'  => addslashes($this->getRequest()->att_value),
              'etapa'  => $this->getRequest()->etapa
    ));
  }


  protected function getParecerGeral(){
    return new Avaliacao_Model_ParecerDescritivoGeral(array(
              'parecer' => addslashes($this->getRequest()->att_value),
              'etapa'   => $this->getRequest()->etapa
    ));
  }


  protected function postParecer(){

    if ($this->canPostParecer()){
      $tpParecer = $this->getService()->getRegra()->get('parecerDescritivo');
      $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

      if ($tpParecer == $cnsParecer::ETAPA_COMPONENTE || $tpParecer == $cnsParecer::ANUAL_COMPONENTE)
        $parecer = $this->getParecerComponente();
      else
        $parecer = $this->getParecerGeral();

      $this->getService()->addParecer($parecer);
      $this->saveService();
      $this->appendMsg('Parecer descritivo matricula '. $this->getRequest()->matricula_id .' alterado com sucesso.', 'success');
    }
  }

  protected function getComponentesCurriculares($matriculaId){

    $dadosMatricula = $this->getDadosMatricula($matriculaId);

    $anoEscolar = $dadosMatricula['ano'];
    $escolaId = $dadosMatricula['escola_id'];
    $turmaId = $dadosMatricula['turma_id'];

    $sqlTurma = "select cc.id, cc.nome from modules.componente_curricular_turma as cct, modules.componente_curricular as cc, pmieducar.escola_ano_letivo as al where cct.turma_id = $turmaId and cct.escola_id = $escolaId and cct.componente_curricular_id = cc.id and al.ano = $anoEscolar and cct.escola_id = al.ref_cod_escola and cc.instituicao_id = {$this->getRequest()->instituicao_id}";

    $db = new Db();
    $componentesCurricularesTurma = $db->select($sqlTurma);
 
  if (count($componentesCurricularesTurma))
    return $componentesCurricularesTurma;

    $sqlSerie = "select cc.id, cc.nome from pmieducar.turma as t, pmieducar.escola_serie_disciplina as esd,	modules.componente_curricular as cc, pmieducar.escola_ano_letivo as al	where t.cod_turma = $turmaId and esd.ref_ref_cod_escola = $escolaId and t.ref_ref_cod_serie = esd.ref_ref_cod_serie and esd.ref_cod_disciplina = cc.id and al.ano = $anoEscolar and cc.instituicao_id = {$this->getRequest()->instituicao_id} and esd.ref_ref_cod_escola = al.ref_cod_escola and t.ativo = 1 and esd.ativo = 1 and al.ativo = 1";

    $db = new Db();
    $componentesCurricularesSerie = $db->select($sqlSerie);
    return $componentesCurricularesSerie;

  }


  protected function lancarFaltasNaoLancadas($matriculaId){

    $defaultValue = 0;
    $cnsPresenca = RegraAvaliacao_Model_TipoPresenca;
    $tpPresenca = $this->getService()->getRegra()->get('tipoPresenca');
    $componentesCurriculares = $this->getComponentesCurriculares($matriculaId);

    if($tpPresenca == $cnsPresenca::GERAL){

      foreach(range(1, $this->getService()->getOption('etapas')) as $etapa){
        $hasNotaOrParecerInEtapa = false;
        foreach($componentesCurriculares as $cc){
          $nota = $this->getNota($etapa, $cc['id']);
          $parecer = $this->getParecerDescritivo($etapa, $cc['id']);

          if(! $hasNotaOrParecerInEtapa && (trim($nota) != '' || trim($parecer) != '')){
            $hasNotaOrParecerInEtapa = true;
            break;
          }
        }

        if($hasNotaOrParecerInEtapa){
          $falta = $this->getService()->getFalta($etapa)->quantidade;
          if(is_null($falta)){
            $notaFalta = new Avaliacao_Model_FaltaGeral(array(
                    'quantidade' => $defaultValue,
                    'etapa' => $etapa
                ));

            $this->getService()->addFalta($notaFalta);
            $this->appendMsg("Lançado falta geral (valor $defaultValue) para etapa $etapa (matricula $matriculaId)", 'notice');
          }
        }
      }//for etapa

    }
    elseif($tpPresenca == $cnsPresenca::POR_COMPONENTE){

      foreach(range(1, $this->getService()->getOption('etapas')) as $etapa){

        foreach($componentesCurriculares as $cc){
          
          $nota = $this->getNota($etapa, $cc['id']);
          $parecer = $this->getParecerDescritivo($etapa, $cc['id']);

          if(trim($nota) != '' || trim($parecer) != ''){
            $falta = $this->getService()->getFalta($etapa, $cc['id'])->quantidade;

            if(is_null($falta)){
              $this->getService()->addFalta(
                $this->getFaltaComponente($etapa, $cc['id'], $defaultValue));

              $this->appendMsg("Lançado falta (valor $defaultValue) para etapa $etapa e componente curricular {$cc['id']} - {$cc['nome']} (matricula $matriculaId)", 'notice');
            }
          }
        }
      }

    }
    else
      throw new Exception("Tipo de presença desconhecido metodo lancarFaltasNaoLancadas");

  }


  protected function postPromocaoMatricula()  {

    if ($this->canPostPromocaoMatricula()){

      $matriculaId = $this->getRequest()->matricula_id;
      $proximoMatriculaId = $this->getProximoMatriculaId($matriculaId);
      $situacaoAnterior = '';
      $novaSituacao = '';

      if($matriculaId == 0){
        if (is_numeric($proximoMatriculaId)){
          $matriculaId = $proximoMatriculaId;
          $proximoMatriculaId = $this->getProximoMatriculaId($matriculaId);
        }
        else
          $this->appendMsg('Sem matriculas em andamento para a seleção informada.', 'notice');
      }

      if($matriculaId != 0 && 
         is_numeric($matriculaId) &&
         $this->setService($matriculaId)){

         $situacaoAnterior = $this->getSituacaoArmazenadaMatricula($matriculaId);

      /*

        enquanto etapa 1 .. etapas regra
          - setar falta como 0 caso não exista

        recalcurar media

      */

        $this->lancarFaltasNaoLancadas($matriculaId);

        $this->saveService();

        $novaSituacao = $this->getSituacaoArmazenadaMatricula($matriculaId);

        $type = 'success';
        $msg = "Matricula $matriculaId";
        if($situacaoAnterior == $novaSituacao){
          $type = 'notice';
          $msg .= ' não mudou de situação';
        }
        elseif($novaSituacao == 1)
          $msg .= " foi aprovada (situaçao antiga $situacaoAnterior)";
        elseif($novaSituacao == 2)
          $msg .= " foi reprovada (situaçao antiga $situacaoAnterior)";
        else
          $msg .= " teve a situação alterada de $novaSituacao para $situacaoAnterior)";

        $this->appendMsg($msg, $type);
      }

      return array('proximo_matricula_id' => $proximoMatriculaId, 'situacao_anterior' => $situacaoAnterior, 'nova_situacao' => $novaSituacao);
    }
  }


  protected function getSituacaoMatricula($raiseExceptionOnErrors = true, $appendMsgOnErrors = true){
    $service = $this->getService($raiseExceptionOnErrors, $appendMsgOnErrors);
    $situacao = 'Situação não recuperada';
    if ($service){
      try {
        $situacao = App_Model_MatriculaSituacao::getInstance()->getValue($service->getSituacaoComponentesCurriculares()->componentesCurriculares[$this->getRequest()->componente_curricular_id]->situacao);
      }
      catch (Exception $e){
        $this->appendMsg("Erro ao recuperar situação matricula: " . $e->getMessage());
      }
    }
    return utf8_encode($situacao);
  }


  protected function getDadosMatricula($matriculaId){
    $sql = "select m.cod_matricula as matricula_id, m.ref_cod_aluno as aluno_id, m.ref_ref_cod_escola as escola_id, m.ref_cod_curso as curso_id, m.ref_ref_cod_serie as serie_id, mt.ref_cod_turma as turma_id, m.ano, m.aprovado from pmieducar.matricula  as m, pmieducar.matricula_turma as mt where mt.ref_cod_matricula = m.cod_matricula and mt.ativo = 1 and cod_matricula = $matriculaId limit 1";

    $db = new Db();
    $quantidadeMatriculas = $db->select($sql);

    return $quantidadeMatriculas[0];
  }


  protected function getQuantidadeMatriculas(){
    if($this->canGetQuantidadeMatriculas())
    {

      $sql = "select count(m.cod_matricula) from pmieducar.matricula as m, pmieducar.matricula_turma as mt where m.ano = {$this->getRequest()->ano_escolar} and m.ativo = 1 and m.aprovado = 3 and mt.ref_cod_matricula = m.cod_matricula and mt.ativo = 1";
  
      $db = new Db();
      $quantidadeMatriculas = $db->select($sql);

      return $quantidadeMatriculas[0]['count'];
    }
  }


  protected function getProximoMatriculaId($currentMatriculaId){
    $sql = "select m.cod_matricula from pmieducar.matricula as m, pmieducar.matricula_turma as mt where m.ano = {$this->getRequest()->ano_escolar} and m.ativo = 1 and m.aprovado = 3 and mt.ref_cod_matricula = m.cod_matricula and mt.ativo = 1 and ref_cod_matricula > $currentMatriculaId order by ref_cod_matricula limit 1";

    $db = new Db();
    $proximoMatriculaId = $db->select($sql);
    return $proximoMatriculaId[0]['cod_matricula'];
  }


  function getSituacaoArmazenadaMatricula($matriculaId)
  {
    $sql = "select aprovado from pmieducar.matricula where cod_matricula = $matriculaId limit 1";

    $db = new Db();
    $proximoMatriculaId = $db->select($sql);
    return $proximoMatriculaId[0]['aprovado'];
  }

  protected function getMatriculas(){
    $matriculas = array();

    if ($this->canGetMatriculas()){

      
      $alunos = new clsPmieducarMatriculaTurma();
      $alunos->setOrderby('nome');

      $alunos = $alunos->lista(
        $this->getRequest()->matricula_id,
        $this->getRequest()->turma_id,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        1,
        $this->getRequest()->serie_id,
        $this->getRequest()->curso_id,
        $this->getRequest()->escola_id,
        $this->getRequest()->instituicao_id,
        $this->getRequest()->aluno_id,
        NULL,
        NULL,
        NULL,
        NULL,
        $this->getRequest()->ano_escolar,
        NULL,
        TRUE,
        NULL,
        NULL,
        TRUE,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL
      );
      

      if (! is_array($alunos))
        $alunos = array();

      $requiredFields = array(
        array('matricula_id', 'ref_cod_matricula'), 
        array('aluno_id', 'ref_cod_aluno'),
      );

      foreach($alunos as $aluno)
      {
        $matricula = array();
        $this->setService($matriculaId = $aluno['ref_cod_matricula']);

        $matricula['situacao'] = $this->getSituacaoMatricula($raiseExceptionOnErrors = false);
        //$matricula['nota_atual'] = $this->getNotaAtual();
        //$matricula['nota_exame'] = $this->getNotaExame();
        //$matricula['falta_atual'] = $this->getFaltaAtual();
        //$matricula['parecer_atual'] = $this->getParecerAtual();

        foreach($requiredFields as $f)
          $matricula[$f[0]] = $aluno[$f[1]];

        $matricula['nome'] = ucwords(strtolower(utf8_encode($aluno['nome_aluno'])));

        $matriculas[] = $matricula;
      }
    }
    return $matriculas;
  }

  protected function getNota($etapa = null, $componenteCurricularId){

    $nota = urldecode($this->getService()->getNotaComponente($componenteCurricularId, $etapa)->nota);
    return str_replace(',', '.', $nota);
  }


  protected function getNotaExame(){
  
  /* removido checagem se usa nota e se a formula recuperacao é do tipo media recuperacao,
     pois se existe nota lançada mostrará.

    $this->getService()->getRegra()->get('tipoNota') != RegraAvaliacao_Model_Nota_TipoValor::NENHUM && 
    $this->getService()->getRegra()->formulaRecuperacao->get('tipoFormula') == FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO */

    //se é a ultima etapa
    if($this->getRequest()->etapa == $this->getService()->getOption('etapas'))
      $nota = $this->getNotaAtual($etapa='Rc'); 
    else
      $nota = '';

    return $nota;
  }


  protected function getFaltaAtual()
  {
    if ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
    {
      $falta = $this->getService()->getFalta($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id)->quantidade;
    }
    elseif ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
    {
      $falta = $this->getService()->getFalta($this->getRequest()->etapa)->quantidade;
    }

    return $falta;
  }


  protected function getEtapaParecer($etapaDefault)
  {

    if($etapaDefault != 'An' && ($this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE || $this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL)){
      return 'An';
    }
    else
      return $etapaDefault;
  }


  protected function getParecerDescritivo($etapa, $componenteCurricularId)
  {
    if ($this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE or
      $this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE){
      return utf8_encode($this->getService()->getParecerDescritivo($this->getEtapaParecer($etapa), $componenteCurricularId));
    }
    else
      return utf8_encode($this->getService()->getParecerDescritivo($this->getEtapaParecer($etapa)));
  }


  protected function getOpcoesFaltas()
  {
    $opcoes = array();
    foreach (range(0, 100, 1) as $f)
      $opcoes[$f] = $f;
    return $opcoes;
  }


  protected function canGetOpcoesNotas()
  {
    return $this->validatesPresenceOfMatriculaId(false);
  }  


  protected function getOpcoesNotas($useCurrentService = False)
  {
    $opcoes = array();
    if (($useCurrentService && $this->getService()) || $this->canGetOpcoesNotas() && $this->setService()){
      $tabela = $this->getService()->getRegra()->tabelaArredondamento->findTabelaValor();
      foreach ($tabela as $item)
      {
        if ($this->getService()->getRegra()->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA)
          $opcoes[(string) $item->nome] = (string) $item->nome;
        else
          $opcoes[(string) $item->valorMaximo] = utf8_encode($item->nome . ' (' . $item->descricao .  ')');
      }
    }
    return $opcoes;
  }


  protected function canGetRegraAvaliacao()
  {
    return $this->validatesPresenceOfMatriculaId(false);
  }  


  protected function getRegraAvaliacao($useCurrentService = False)
  {
    $itensRegra = array();
    if (($useCurrentService && $this->getService()) || $this->canGetRegraAvaliacao() && $this->setService()){
      $regra = $this->getService()->getRegra();
      $itensRegra['id'] = utf8_encode($regra->get('id'));
      $itensRegra['nome'] = utf8_encode($regra->get('nome'));

      $cnsPresenca = RegraAvaliacao_Model_TipoPresenca;
      $tpPresenca = $this->getService()->getRegra()->get('tipoPresenca');
      if($tpPresenca == $cnsPresenca::GERAL)
        $itensRegra['tipo_presenca'] = 'geral';
      elseif($tpPresenca == $cnsPresenca::POR_COMPONENTE)
        $itensRegra['tipo_presenca'] = 'por_componente';
      else
        $itensRegra['tipo_presenca'] = $tpPresenca;

      $cnsNota = RegraAvaliacao_Model_Nota_TipoValor;
      $tpNota = $this->getService()->getRegra()->get('tipoNota');
      if ($tpNota == $cnsNota::NENHUM)
        $itensRegra['tipo_nota'] = 'nenhum';
      elseif ($tpNota == $cnsNota::NUMERICA)
        $itensRegra['tipo_nota'] = 'numerica';
      elseif ($tpNota == $cnsNota::CONCEITUAL)
      {
        $itensRegra['tipo_nota'] = 'conceitual';
        //incluido opcoes notas, pois notas conceituais requer isto para visualizar os nomes
        $itensRegra['opcoes_notas'] = $this->getOpcoesNotas($useCurrentService = $useCurrentService);
      }
      else
        $itensRegra['tipo_nota'] = $tpNota;

      $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;
      $tpParecer = $this->getService()->getRegra()->get('parecerDescritivo');
      if ($tpParecer == $cnsParecer::NENHUM)
        $itensRegra['tipo_parecer_descritivo'] = 'nenhum';
      elseif ($tpParecer == $cnsParecer::ETAPA_COMPONENTE)
        $itensRegra['tipo_parecer_descritivo'] = 'etapa_componente';
      elseif ($tpParecer == $cnsParecer::ETAPA_GERAL)
        $itensRegra['tipo_parecer_descritivo'] = 'etapa_geral';
      elseif ($tpParecer == $cnsParecer::ANUAL_COMPONENTE)
        $itensRegra['tipo_parecer_descritivo'] = 'anual_componente';
      elseif ($tpParecer == $cnsParecer::ANUAL_GERAL)
        $itensRegra['tipo_parecer_descritivo'] = 'anual_geral';
      else
        $itensRegra['tipo_parecer_descritivo'] = $tpParecer;

      $itensRegra['quantidade_etapas'] = $this->getService()->getOption('etapas');

    }
    
    return $itensRegra;
  }


  protected function saveService()
  {
    try {
      $this->getService()->save();   
    }
    catch (CoreExt_Service_Exception $e){
      //excecoes ignoradas :( servico lanca excecoes de alertas, que não são exatamente erros.
      error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
    }
  }

  protected function getService($raiseExceptionOnErrors = false, $appendMsgOnErrors = true){
    if (isset($this->service) && ! is_null($this->service))
      return $this->service;

    $msg = 'Erro ao recuperar serviço boletim: serviço não definido.';
    if($appendMsgOnErrors)
      $this->appendMsg($msg);

    if ($raiseExceptionOnErrors)
      throw new Exception($msg);

    return null;
  }

  protected function canSetService($validatesPresenceOfMatriculaId = true)
  {
    try {
      $this->requiresLogin(true);
      if ($validatesPresenceOfMatriculaId)
        $this->validatesPresenceOfMatriculaId(true);
    }
    catch (Exception $e){
      return false;
    }
    return true;
  }

  protected function setService($matriculaId = null){
    if ($this->canSetService($validatesPresenceOfMatriculaId = is_null($matriculaId))){
      try {

        if (! $matriculaId)
          $matriculaId = $this->getRequest()->matricula_id;

        $this->service = new Avaliacao_Service_Boletim(array(
            'matricula' => $matriculaId,
            'usuario'   => $this->getSession()->id_pessoa
        ));

      return true;
      }
      catch (Exception $e){
        $this->appendMsg('Exception ao instanciar serviço boletim: ' . $e->getMessage(), 'error', $encodeToUtf8 = true);
      }
    }
    return false;
  }


  protected function notImplementedError()
  {
    $this->appendMsg("Operação '{$this->getRequest()->oper}' inválida para o att '{$this->getRequest()->att}'");    
  }


  public function Gerar(){
    $this->msgs = array();
    $this->response = array();

    if ($this->canAcceptRequest()){
      try {
        if ($this->getRequest()->oper == 'get')
        {
          if ($this->getRequest()->att == 'matriculas')
          {
            $matriculas = $this->getMatriculas();          
            $this->appendResponse('matriculas', $matriculas);
          }
          if ($this->getRequest()->att == 'quantidade_matriculas')
          {
            $matriculas = $this->getQuantidadeMatriculas();          
            $this->appendResponse('quantidade_matriculas', $matriculas);
          }    
          else
            $this->notImplementedError();

        }
        elseif ($this->getRequest()->oper == 'post')
        {
          if ($this->getRequest()->att == 'promocao')
          {
            $this->appendResponse('result', $this->postPromocaoMatricula());
          }
          else
            $this->notImplementedError();  
        }
        elseif ($this->getRequest()->oper == 'delete')
        {
            $this->notImplementedError();
        }
      }
      catch (Exception $e){
        $this->appendMsg('Exception: ' . $e->getMessage(), $type = 'error', $encodeToUtf8 = true);
      }
    }
    echo $this->prepareResponse();
  }

  protected function appendResponse($name, $value){
    $this->response[$name] = $value;
  }

  protected function prepareResponse(){
    $msgs = array();
    $this->appendResponse('att', isset($this->getRequest()->att) ? $this->getRequest()->att : '');

    if (isset($this->getRequest()->matricula_id) && 
              $this->getRequest()->att != 'quantidade_matriculas' &&
              $this->getRequest()->att != 'promocao' &&
              $this->getRequest()->att != 'matriculas'){
      $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
      $this->appendResponse('situacao', $this->getSituacaoMatricula($raiseExceptionOnErrors = false, $appendMsgOnErrors = false));
    }

    foreach($this->msgs as $m)
      $msgs[] = array('msg' => $m['msg'], 'type' => $m['type']);
    $this->appendResponse('msgs', $msgs);

    echo json_encode($this->response);
  }

  protected function appendMsg($msg, $type="error", $encodeToUtf8 = false){
    if ($encodeToUtf8)
      $msg = utf8_encode($msg);

    error_log("$type msg: '$msg'");
    $this->msgs[] = array('msg' => $msg, 'type' => $type);
  }

  public function generate(CoreExt_Controller_Page_Interface $instance){
    header('Content-type: application/json');
    $instance->Gerar();
  }
}
