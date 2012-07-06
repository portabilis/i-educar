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
require_once 'lib/Portabilis/Message.php';

class DiarioAjaxController extends Core_Controller_Page_EditController
{
  protected $_dataMapper  = 'Avaliacao_Model_NotaComponenteDataMapper';
  protected $_processoAp  = 644;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';


  protected function _preConstruct()
  {
    $this->messages = new Message();
  }

  protected function validatesPresenceOf(&$value, $name, $raiseExceptionOnEmpty = false, $msg = '', $addMsgOnEmpty = true){
    if (! isset($value) || empty($value)){
      if ($addMsgOnEmpty)
      {
        $msg = empty($msg) ? "É necessário receber uma variavel '$name'" : $msg;
        $this->messages->append($msg);
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
        $this->messages->append($msg);
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
      $this->messages->append($msg);

      if ($raiseExceptionOnError)
         throw new Exception($msg);

      return false;
    }
    return true;
  }


  protected function requiresLogin($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getSession()->id_pessoa, '', $raiseExceptionOnEmpty, 'Usuário deve estar logado');
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

  protected function validatesPresenceOfAno($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getRequest()->ano, 'ano', $raiseExceptionOnEmpty);
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

    $this->messages->append($msg);

    if ($raiseExceptionOnEmpty)
       throw new Exception($msg);

    return false;
  }

  protected function validatesValueOfAttValueIsNumeric($raiseExceptionOnError){
    return $this->validatesValueIsNumeric($this->getRequest()->att_value, 'att_value', $raiseExceptionOnError);
  }

  protected function validatesValueOfAttValueIsInOpcoesNotas($raiseExceptionOnError){
    $expectedValues = array_keys($this->getOpcoesNotas());
    return $this->validatesValueInSetOf($this->getRequest()->att_value, $expectedValues, 'att_value', $raiseExceptionOnError);
  }

  protected function validatesPresenceOfAttValue($raiseExceptionOnEmpty){
    return $this->validatesPresenceOf($this->getRequest()->att_value, 'att_value', $raiseExceptionOnEmpty);
  }


  protected function validatesPresenceAndValueInSetOfAtt($raiseExceptionOnError){
    $result = $this->validatesPresenceOf($this->getRequest()->att, 'att', $raiseExceptionOnError);

    if ($result){
      $expectedAtts = array('nota', 'nota_exame', 'falta', 'parecer', 'matriculas', 'opcoes_notas', 'opcoes_faltas', 'regra_avaliacao');
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


  protected function validatesCanChangeDiarioForAno() {
    $escola = App_Model_IedFinder::getEscola($this->getRequest()->escola_id);

    $ano                 = new clsPmieducarEscolaAnoLetivo();
    $ano->ref_cod_escola = $this->getRequest()->escola_id;
    $ano->ano            = $this->getRequest()->ano;
    $ano                 = $ano->detalhe();

    $anoLetivoEncerrado = is_array($ano)     && count($ano) > 0    && 
                          $ano['ativo'] == 1 && $ano['andamento'] == 2;

    if ($escola['bloquear_lancamento_diario_anos_letivos_encerrados'] == '1' and $anoLetivoEncerrado) {
      $this->messages->append("O ano letivo '{$this->getRequest()->ano}' está encerrado, esta escola está configurada para não permitir alterar o diário de anos letivos encerrados.");
      return false;
    }

    return true;
  }


  protected function canAcceptRequest()
  {
    try {
      $this->requiresLogin(true);
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
      $this->validatesPresenceOfEscolaId(true);
      $this->validatesPresenceOfCursoId(true);
      $this->validatesPresenceOfSerieId(true);
      $this->validatesPresenceOfTurmaId(true);
      $this->validatesPresenceOfAno(true);
      $this->validatesPresenceOfComponenteCurricularId(true);
      $this->validatesPresenceOfEtapa(true);
    }
    catch (Exception $e){
      return false;
    }

    return $this->validatesCanChangeDiarioForAno();
  }


  protected function canPost(){
    return $this->validatesPresenceOfEtapa(false);
  }

  protected function canPostNota(){

    $canPost = $this->setService() &&
               $this->canPost() &&
               $this->validatesValueOfAttValueIsNumeric(false) &&
               $this->validatesValueOfAttValueIsInOpcoesNotas(false) &&
               $this->validatesPresenceOfComponenteCurricularId(false);

    if ($canPost && $this->getService()->getRegra()->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NENHUM)
    {
      $canPost = false;
      $this->messages->append("Nota não lançada, pois a regra de avaliação não utiliza nota.");
    }
    elseif ($canPost && $this->getRequest()->etapa == 'Rc' && is_null($this->getService()->getRegra()->formulaRecuperacao))
    {
      $canPost = false;
      $this->messages->append("Nota de recuperação não lançada, pois a fórmula de recuperação não possui fórmula de recuperação.");
    }
    elseif ($canPost && $this->getRequest()->etapa == 'Rc' && $this->getService()->getRegra()->formulaRecuperacao->get('tipoFormula') != FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO)
    {
      $canPost = false;
      $this->messages->append("Nota de recuperação não lançada, pois a fórmula de recuperação é diferente do tipo média recuperação.");
    }
    elseif ($canPost)
    {
      $etapasWithoutNotas = array();
      $hasPreviousNotas = true;

      if($this->getRequest()->etapa == 'Rc')
        $etapaRequest = $this->getService()->getOption('etapas');
      else
        $etapaRequest = $this->getRequest()->etapa;

      for($etapa = 1; $etapa <= $etapaRequest; $etapa++){
        $nota = $this->getNotaAtual($etapa);

        if(($etapa != $this->getRequest()->etapa || $this->getRequest()->etapa == 'Rc') && empty($nota) && ! is_numeric($nota)){

          if($hasPreviousNotas){
            $hasPreviousNotas = false;
            $canPost = false;
          }
          $etapasWithoutNotas[] = $etapa;
        }
      }

      if (! $hasPreviousNotas){
        $this->messages->append("Nota somente pode ser lançada após lançar notas nas etapas: " . join(', ', $etapasWithoutNotas) . ' deste componente curricular.');
      }
    }
    return $canPost;
  }


  protected function canPostFalta(){
    $canPost = $this->canPost() &&
              $this->validatesValueOfAttValueIsNumeric(false) &&
              $this->setService();

    if ($canPost && is_numeric($this->getRequest()->etapa))
    {
      $etapasWithoutFaltas = array();
      $hasPreviousFaltas = true;
      for($etapa = 1; $etapa <= $this->getRequest()->etapa; $etapa++){
        $falta = $this->getFaltaAtual($etapa);

        if($etapa != $this->getRequest()->etapa && empty($falta) && ! is_numeric($falta)){
          if($hasPreviousFaltas){
            $hasPreviousFaltas = false;
            $canPost = false;
          }
          $etapasWithoutFaltas[] = $etapa;
        }
      }

      if (! $hasPreviousFaltas){
        if ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE){
          $this->messages->append("Falta somente pode ser lançada após lançar faltas nas etapas anteriores: " . join(', ', $etapasWithoutFaltas) . ' deste componente curricular.');
        }
        else{
          $this->messages->append("Falta somente pode ser lançada após lançar faltas nas etapas anteriores: " . join(', ', $etapasWithoutFaltas) . '.');
        }
      }
    }

    return $canPost;
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
        $this->messages->append("Parecer descritivo não lançado, pois a regra de avaliação não utiliza parecer.");
      }
      elseif ($tpParecer == $cnsParecer::ETAPA_COMPONENTE || $tpParecer == $cnsParecer::ANUAL_COMPONENTE){
        $canPost = $this->validatesPresenceOfComponenteCurricularId(false);
      }
    }

    return $canPost;
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
    $canDelete = $this->canDelete() &&
                 $this->setService() &&
                 $this->validatesPresenceOfComponenteCurricularId(false);

    if ($canDelete && $this->getRequest()->etapa != 'Rc' && is_numeric($this->getRequest()->etapa)){

      $notaExame = $this->getNotaAtual($etapa='Rc');
      if(! empty($notaExame) || is_numeric($notaExame)){
        $this->messages->append('Nota da matrícula '. $this->getRequest()->matricula_id .' somente pode ser removida, após remover nota do exame.', 'error');
        $canDelete = false;
      }
      else {
        $etapasComNota = array();
        for($etapa = $this->getRequest()->etapa + 1;
            $etapa <= $this->getService()->getOption('etapas');
            $etapa++) {
          $notaNextEtapa = $this->getNotaAtual($etapa);
          $this->messages->append("Verificando nota etapa $etapa", 'notice');

          if(! empty($notaNextEtapa) || is_numeric($notaNextEtapa)){
            $etapasComNota[] = $etapa;
            $canDelete = false;
          }
        }
        if (! empty($etapasComNota))
          $this->messages->append("Nota somente pode ser removida, após remover as notas lançadas nas etapas posteriores: " . join(', ', $etapasComNota) . '.', 'error');
      }
    }

    return $canDelete;
  }


  protected function canDeleteFalta(){
    $canDelete = $this->canDelete() && $this->setService();

    if ($canDelete && is_numeric($this->getRequest()->etapa)){
      $etapasComFalta = array();
      for($etapa = $this->getRequest()->etapa + 1;
          $etapa <= $this->getService()->getOption('etapas');
          $etapa++) {
        $faltaNextEtapa = $this->getFaltaAtual($etapa);

        if(! empty($faltaNextEtapa) || is_numeric($faltaNextEtapa)){
          $etapasComFalta[] = $etapa;
          $canDelete = false;
        }

      }

      if (! empty($etapasComFalta))
        $this->messages->append("Falta somente pode ser removida, após remover as faltas lançadas nas etapas posteriores: " . join(', ', $etapasComFalta) . '.', 'error');
    }

    return $canDelete;
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
    if ($this->canDeleteNota()){

      $nota = $this->getNotaAtual();
      if (empty($nota) && ! is_numeric($nota))
        $this->messages->append('Nota matrícula '. $this->getRequest()->matricula_id .' inexistente ou já removida.', 'notice');
      else
      {
        $this->getService()->deleteNota($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id);
        $this->saveService();
        $this->messages->append('Nota matrícula '. $this->getRequest()->matricula_id .' removida com sucesso.', 'success');
      }
    }
  }


  protected function deleteFalta(){
    $canDelete = $this->canDeleteFalta();
    $cnsPresenca = RegraAvaliacao_Model_TipoPresenca;
    $tpPresenca = $this->getService()->getRegra()->get('tipoPresenca');

    if ($canDelete && $tpPresenca == $cnsPresenca::POR_COMPONENTE){
      $canDelete = $this->validatesPresenceOfComponenteCurricularId(false);
      $componenteCurricularId = $this->getRequest()->componente_curricular_id;
    }
    else
      $componenteCurricularId = null;

    if ($canDelete && is_null($this->getFaltaAtual())){
      $this->messages->append('Falta matrícula '. $this->getRequest()->matricula_id .' inexistente ou já removida.', 'notice');
    }
    elseif ($canDelete){
      $this->getService()->deleteFalta($this->getRequest()->etapa, $componenteCurricularId);
      $this->saveService();
      $this->messages->append('Falta matrícula '. $this->getRequest()->matricula_id .' removida com sucesso.', 'success');
    }
  }


  protected function deleteParecer(){
    if ($this->canDeleteParecer()){
      $parecerAtual = $this->getParecerAtual();

      if ((is_null($parecerAtual) || $parecerAtual == '')){
        $this->messages->append('Parecer descritivo matrícula '. $this->getRequest()->matricula_id .' inexistente ou já removido.', 'notice');
      }
      else{
        $tpParecer = $this->getService()->getRegra()->get('parecerDescritivo');
        $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

        if ($tpParecer == $cnsParecer::ANUAL_COMPONENTE || $tpParecer == $cnsParecer::ETAPA_COMPONENTE)
          $this->getService()->deleteParecer($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id);
        else
          $this->getService()->deleteParecer($this->getRequest()->etapa);

        $this->saveService();
        $this->messages->append('Parecer descritivo matrícula '. $this->getRequest()->matricula_id .' removido com sucesso.', 'success');
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
      $this->messages->append('Nota matrícula '. $this->getRequest()->matricula_id .' alterada com sucesso.', 'success');
    }
  }


  protected function getQuantidadeFalta(){
    $quantidade = (int) $this->getRequest()->att_value;

    if ($quantidade < 0)
      $quantidade = 0;

    return $quantidade;
  }


  protected function getFaltaGeral(){
    return new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => $this->getQuantidadeFalta(),
        'etapa' => $this->getRequest()->etapa
    ));
  }


  protected function getFaltaComponente(){
    return new Avaliacao_Model_FaltaComponente(array(
            'componenteCurricular' => $this->getRequest()->componente_curricular_id,
            'quantidade' => $this->getQuantidadeFalta(),
            'etapa' => $this->getRequest()->etapa
    ));
  }


  protected function postFalta(){

    $canPost = $this->canPostFalta();
    if ($canPost && $this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
      $canPost = $this->validatesPresenceOfComponenteCurricularId(false);

    if ($canPost){
      if ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
        $falta = $this->getFaltaComponente();
      elseif ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
        $falta = $this->getFaltaGeral();

      $this->getService()->addFalta($falta);
      $this->saveService();
      $this->messages->append('Falta matrícula '. $this->getRequest()->matricula_id .' alterada com sucesso.', 'success');
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
      $this->messages->append('Parecer descritivo matricula '. $this->getRequest()->matricula_id .' alterado com sucesso.', 'success');
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
        $this->messages->append("Erro ao recuperar situação da matrícula: " . $e->getMessage());
      }
    }
    return utf8_encode($situacao);
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
        $this->getRequest()->ano,
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
        $matricula['nota_atual'] = $this->getNotaAtual();
        $matricula['nota_exame'] = $this->getNotaExame();
        $matricula['falta_atual'] = $this->getFaltaAtual();
        $matricula['parecer_atual'] = $this->getParecerAtual();

        foreach($requiredFields as $f)
          $matricula[$f[0]] = $aluno[$f[1]];

        $matricula['nome'] = ucwords(strtolower(utf8_encode($aluno['nome_aluno'])));

        $matriculas[] = $matricula;
      }
    }
    return $matriculas;
  }

  protected function getNotaAtual($etapa = null){
    if (is_null($etapa))
      $etapa = $this->getRequest()->etapa;

    $nota = urldecode($this->getService()->getNotaComponente($this->getRequest()->componente_curricular_id, $etapa)->nota);
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


  protected function getFaltaAtual($etapa = null)
  {

    if (is_null($etapa))
      $etapa = $this->getRequest()->etapa;

    if ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
    {
      $falta = $this->getService()->getFalta($etapa, $this->getRequest()->componente_curricular_id)->quantidade;
    }
    elseif ($this->getService()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
    {
      $falta = $this->getService()->getFalta($etapa)->quantidade;
    }

    return $falta;
  }


  protected function getEtapaParecer()
  {

    if($this->getRequest()->etapa != 'An' && ($this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE || $this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL)){
      return 'An';
    }
    else
      return $this->getRequest()->etapa;
  }


  protected function getParecerAtual()
  {
    if ($this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE or
      $this->getService()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE){
      return utf8_encode($this->getService()->getParecerDescritivo($this->getEtapaParecer(), $this->getRequest()->componente_curricular_id));
    }
    else
      return utf8_encode($this->getService()->getParecerDescritivo($this->getEtapaParecer()));
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

      $tpNota = $this->getService()->getRegra()->get('tipoNota');
      $cnsNota = RegraAvaliacao_Model_Nota_TipoValor;

      if ($tpNota != $cnsNota::NENHUM){
        $tabela = $this->getService()->getRegra()->tabelaArredondamento->findTabelaValor();
        foreach ($tabela as $item)
        {
          if ($tpNota == $cnsNota::NUMERICA)
            $opcoes[(string) $item->nome] = (string) $item->nome;
          else
            $opcoes[(string) $item->valorMaximo] = utf8_encode($item->nome . ' (' . $item->descricao .  ')');
        }
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
      }
      else
        $itensRegra['tipo_nota'] = $tpNota;

      $itensRegra['opcoes_notas'] = $this->getOpcoesNotas($useCurrentService = $useCurrentService);

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
      //error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
    }
  }

  protected function getService($raiseExceptionOnErrors = false, $appendMsgOnErrors = true){
    if (isset($this->service) && ! is_null($this->service))
      return $this->service;

    $msg = 'Erro ao recuperar serviço boletim: serviço não definido.';
    if($appendMsgOnErrors)
      $this->messages->append($msg);

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
        $this->messages->append('Exception ao instanciar serviço boletim: ' . $e->getMessage(), 'error', $encodeToUtf8 = true);
      }
    }
    return false;
  }


  protected function notImplementedError()
  {
    $this->messages->append("Operação '{$this->getRequest()->oper}' inválida para o att '{$this->getRequest()->att}'");
  }


  public function Gerar(){
    $this->response = array();

    if ($this->canAcceptRequest()){
      try {
        if ($this->getRequest()->oper == 'get')
        {
          if ($this->getRequest()->att == 'matriculas')
          {
            $matriculas = $this->getMatriculas();
            $this->appendResponse('matriculas', $matriculas);

            if(! empty($matriculas) && $this->getService(false, false))
              $regraAvaliacao = $this->getRegraAvaliacao($useCurrentService = true);
            else
              $regraAvaliacao = array();

            $this->appendResponse('regra_avaliacao', $regraAvaliacao);
          }
          elseif ($this->getRequest()->att == 'opcoes_notas')
          {
            $opcoesNotas = $this->getOpcoesNotas();
            $this->appendResponse('opcoes_notas', $opcoesNotas);
          }
          elseif ($this->getRequest()->att == 'opcoes_faltas')
          {
            $opcoesFaltas = $this->getOpcoesFaltas();
            $this->appendResponse('opcoes_faltas', $opcoesFaltas);
          }
          elseif ($this->getRequest()->att == 'regra_avaliacao')
          {
            $regraAvaliacao = $this->getRegraAvaliacao();
            $this->appendResponse('regra_avaliacao', $regraAvaliacao);
          }
          else
            $this->notImplementedError();
        }
        elseif ($this->getRequest()->oper == 'post')
        {
          if ($this->getRequest()->att == 'nota' || $this->getRequest()->att == 'nota_exame')
            $this->postNota();

          elseif ($this->getRequest()->att == 'falta')
            $this->postFalta();

          elseif ($this->getRequest()->att == 'parecer')
            $this->postParecer();
          else
            $this->notImplementedError();
        }
        elseif ($this->getRequest()->oper == 'delete')
        {
          if ($this->getRequest()->att == 'nota' || $this->getRequest()->att == 'nota_exame')
            $this->deleteNota();

          elseif ($this->getRequest()->att == 'falta')
            $this->deleteFalta();

          elseif ($this->getRequest()->att == 'parecer')
            $this->deleteParecer();
          else
            $this->notImplementedError();
        }
      }
      catch (Exception $e){
        $this->messages->append('Exception: ' . $e->getMessage(), $type = 'error', $encodeToUtf8 = true);
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
              $this->getRequest()->att != 'regra_avaliacao' &&
              $this->getRequest()->att != 'matriculas'){
      $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
      $this->appendResponse('situacao', $this->getSituacaoMatricula($raiseExceptionOnErrors = false, $appendMsgOnErrors = false));
    }

    foreach($this->messages->getMsgs() as $m)
      $msgs[] = array('msg' => $m['msg'], 'type' => $m['type']);
    $this->appendResponse('msgs', $msgs);

    echo json_encode($this->response);
  }

  public function generate(CoreExt_Controller_Page_Interface $instance){
    header('Content-type: application/json');
    $instance->Gerar();
  }
}
