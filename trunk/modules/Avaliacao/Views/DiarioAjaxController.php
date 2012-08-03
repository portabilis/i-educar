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
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/Controller/ApiCoreController.php';

class DiarioAjaxController  extends ApiCoreController
{
  protected $_dataMapper  = 'Avaliacao_Model_NotaComponenteDataMapper';
  protected $_processoAp  = 644;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';


  // validations

  protected function validatesEtapaParecer() {
    $isValid           = false;
    $etapa             = $this->getRequest()->etapa;    

    $tiposParecerAnual = array(RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
                               RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL);

    $parecerAnual      = in_array($this->serviceBoletim()->getRegra()->get('parecerDescritivo'), 
                                  $tiposParecerAnual);
    
    if($parecerAnual && $etapa != 'An')
      $this->messenger->append("Valor inválido para o atributo 'etapa', é esperado 'An' e foi recebido '{$etapa}'.");
    elseif(! $parecerAnual && $etapa == 'An')
      $this->messenger->append("Valor inválido para o atributo 'etapa', é esperado um valor diferente de 'An'.");
    else
      $isValid = true;

    return $isValid;
  }


  # TODO refatorar para não receber parametro $raiseExceptionOnError
  protected function validatesValueOfAttValueIsInOpcoesNotas($raiseExceptionOnError) {
    $expectedValues = array_keys($this->getOpcoesNotas());
    return $this->validator->validatesValueInSetOf($this->getRequest()->att_value, $expectedValues, 'att_value', $raiseExceptionOnError);
  }


  protected function validatesCanChangeDiarioForAno() {
    $escola = App_Model_IedFinder::getEscola($this->getRequest()->escola_id);

    $ano                 = new clsPmieducarEscolaAnoLetivo();
    $ano->ref_cod_escola = $this->getRequest()->escola_id;
    $ano->ano            = $this->getRequest()->ano;
    $ano                 = $ano->detalhe();

    $anoLetivoEncerrado = is_array($ano)     && count($ano) > 0    && 
                          $ano['ativo'] == 1 && $ano['andamento'] == 2;

    if ($escola['bloquear_lancamento_diario_anos_letivos_encerrados'] == '1' && $anoLetivoEncerrado) {
      $this->messenger->append("O ano letivo '{$this->getRequest()->ano}' está encerrado, esta escola está configurada para não permitir alterar o diário de anos letivos encerrados.");
      return false;
    }

    return true;
  }


  // post nota validations

  protected function validatesRegraAvaliacaoHasNota() {
    $isValid = $this->serviceBoletim()->getRegra()->get('tipoNota') != RegraAvaliacao_Model_Nota_TipoValor::NENHUM;

    if (! $isValid)
      $this->messenger->append("Nota não lançada, pois a regra de avaliação não utiliza nota.");

    return $isValid;
  }


  protected function validatesRegraAvaliacaoHasFormulaRecuperacao() {
    $isValid = $this->getRequest()->etapa != 'Rc' ||
               ! is_null($this->serviceBoletim()->getRegra()->formulaRecuperacao);

    if (! $isValid)
      $this->messenger->append("Nota de recuperação não lançada, pois a fórmula de recuperação não possui fórmula de recuperação.");

    return $isValid;
  }


  protected function validatesRegraAvaliacaoHasFormulaRecuperacaoWithTypeRecuperacao() {
    $isValid = $this->getRequest()->etapa != 'Rc' ||
               ($this->serviceBoletim()->getRegra()->formulaRecuperacao->get('tipoFormula') ==
                FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO);

    if (! $isValid)
      $this->messenger->append("Nota de recuperação não lançada, pois a fórmula de recuperação é diferente do tipo média recuperação.");

    return $isValid;
  }


  protected function validatesPreviousNotasHasBeenSet() {
    $hasPreviousNotas   = true;
    $etapasWithoutNotas = array();

    if($this->getRequest()->etapa == 'Rc')
      $etapaRequest = $this->serviceBoletim()->getOption('etapas');
    else
      $etapaRequest = $this->getRequest()->etapa;


    for($etapa = 1; $etapa <= $etapaRequest; $etapa++) {
      $nota = $this->getNotaAtual($etapa);

      if(($etapa != $this->getRequest()->etapa || $this->getRequest()->etapa == 'Rc') &&
         empty($nota) && ! is_numeric($nota)) {
        $hasPreviousNotas     = false;
        $etapasWithoutNotas[] = $etapa;
      }
    }

    if (! $hasPreviousNotas) {
      $this->messenger->append("Nota somente pode ser lançada após lançar notas nas etapas: " . 
                               join(', ', $etapasWithoutNotas) . ' deste componente curricular.');
    }

    return $hasPreviousNotas;
  }


  // post falta validations

  protected function validatesPreviousFaltasHasBeenSet() {
    $hasPreviousFaltas   = true;
    $etapasWithoutFaltas = array();

    for($etapa = 1; $etapa <= $this->getRequest()->etapa; $etapa++) {
      $falta = $this->getFaltaAtual($etapa);

      if($etapa != $this->getRequest()->etapa && empty($falta) && ! is_numeric($falta)) {
        $hasPreviousFaltas     = false;
        $etapasWithoutFaltas[] = $etapa;
      }
    }

    if (! $hasPreviousFaltas) {
      if ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
        $this->messenger->append("Falta somente pode ser lançada após lançar faltas nas etapas anteriores: " . 
                                 join(', ', $etapasWithoutFaltas) . ' deste componente curricular.');
      }
      else{
        $this->messenger->append("Falta somente pode ser lançada após lançar faltas nas etapas anteriores: " .
                                 join(', ', $etapasWithoutFaltas) . '.');
      }
    }

    return $hasPreviousFaltas;
  }


  // post parecer validations

  protected function validatesRegraAvaliacaoHasParecer() {
    $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
    $isValid   = $tpParecer != RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM;

    if (! $isValid)
      $this->messenger->append("Parecer descritivo não lançado, pois a regra de avaliação não utiliza parecer.");

    return $isValid;
  }

  
  protected function validatesPresenceOfComponenteCurricularIdIfParecerComponente() {
    $tiposParecerComponente = array(RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
                                    RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE);

    $parecerPorComponente   = in_array($this->serviceBoletim()->getRegra()->get('parecerDescritivo'),
                                       $tiposParecerComponente);

    return (! $parecerPorComponente) || $this->validatesPresenceOf('componente_curricular_id');
  }


  // delete nota validations

  protected function validatesInexistenceOfNotaExame() {
    $isValid = true;

    if ($this->getRequest()->etapa != 'Rc') {
      $notaExame = $this->getNotaAtual($etapa = 'Rc');
      $isValid   = empty($notaExame);

      if(! $isValid)
        $this->messenger->append('Nota da matrícula '. $this->getRequest()->matricula_id .' somente pode ser removida, após remover nota do exame.', 'error');
    }

    return $isValid;
  }


  protected function validatesInexistenceNotasInNextEtapas() {
    $etapasComNota = array();

    if (is_numeric($this->getRequest()->etapa)) {
      $etapas = $this->serviceBoletim()->getOption('etapas');
      $etapa  = $this->getRequest()->etapa + 1;

      for($etapa; $etapa <= $etapas; $etapa++) {
        $nota = $this->getNotaAtual($etapa);

        if (! empty($nota))
          $etapasComNota[] = $etapa;
      }

      if (! empty($etapasComNota)) {
        $msg = "Nota somente pode ser removida, após remover as notas lançadas nas etapas posteriores: " .
               join(', ', $etapasComNota) . '.';
        $this->messenger->append($msg, 'error');
      }
    }

    return empty($etapasComNota);
  }


  // delete falta validations


  protected function validatesInexistenceFaltasInNextEtapas() {
    $etapasComFalta = array();  

    if (is_numeric($this->getRequest()->etapa)) {
      $etapas = $this->serviceBoletim()->getOption('etapas');
      $etapa  = $this->getRequest()->etapa + 1;

      for($etapa; $etapa <= $etapas; $etapa++) {
        $falta = $this->getFaltaAtual($etapa);

        if(! empty($falta))
          $etapasComFalta[] = $etapa;

      }

      if (! empty($etapasComFalta))
        $this->messenger->append("Falta somente pode ser removida, após remover as faltas lançadas nas etapas posteriores: " . join(', ', $etapasComFalta) . '.', 'error');
    }

    return empty($etapasComFalta);
  }


  // responders validations


  protected function canGetMatriculas() {
    return $this->validatesPresenceOf(array('instituicao_id',
                                     'escola_id',
                                     'curso_id',
                                     'curso_id',
                                     'serie_id',
                                     'turma_id',
                                     'ano',
                                     'etapa')) &&

          $this->validatesCanChangeDiarioForAno();
  }


  protected function canPost() {
    return $this->validatesPresenceOf('etapa') &&
           $this->validatesPresenceOf('matricula_id');
  }


  protected function canPostNota() {
    return $this->canPost() &&
           $this->validatesIsNumeric('att_value') &&
           $this->validatesValueOfAttValueIsInOpcoesNotas(false) &&
           $this->validatesPresenceOf('componente_curricular_id') &&
           $this->validatesRegraAvaliacaoHasNota() &&
           $this->validatesRegraAvaliacaoHasFormulaRecuperacao() &&
           $this->validatesRegraAvaliacaoHasFormulaRecuperacaoWithTypeRecuperacao() &&
           $this->validatesPreviousNotasHasBeenSet();
  }


  protected function canPostFalta() {
    return $this->canPost() &&
           $this->validatesIsNumeric('att_value') &&
           $this->validatesPreviousFaltasHasBeenSet();
  }


  protected function canPostParecer() {

    return $this->canPost() &&
           $this->validatesPresenceOf('att_value') &&
           $this->validatesEtapaParecer() &&
           $this->validatesRegraAvaliacaoHasParecer() &&
           $this->validatesPresenceOfComponenteCurricularIdIfParecerComponente();
  }


  protected function canDelete() {
    return $this->validatesPresenceOf('etapa');
  }


  protected function canDeleteNota() {
    return $this->canDelete() &&
           $this->validatesPresenceOf('componente_curricular_id') &&
           $this->validatesInexistenceOfNotaExame() &&
           $this->validatesInexistenceNotasInNextEtapas();
  }

  protected function canDeleteFalta() {
    return $this->canDelete() &&
           $this->validatesInexistenceFaltasInNextEtapas();
  }


  protected function canDeleteParecer() {
    return $this->canDelete() &&
           $this->validatesEtapaParecer() &&
           $this->validatesPresenceOfComponenteCurricularIdIfParecerComponente();
  }


  // responders

  protected function deleteNota() {
    if ($this->canDeleteNota()) {

      $nota = $this->getNotaAtual();
      if (empty($nota) && ! is_numeric($nota))
        $this->messenger->append('Nota matrícula '. $this->getRequest()->matricula_id .' inexistente ou já removida.', 'notice');
      else
      {
        $this->serviceBoletim()->deleteNota($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id);
        $this->saveService();
        $this->messenger->append('Nota matrícula '. $this->getRequest()->matricula_id .' removida com sucesso.', 'success');
      }
    }

    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());
  }


  protected function deleteFalta() {
    $canDelete = $this->canDeleteFalta();
    $cnsPresenca = RegraAvaliacao_Model_TipoPresenca;
    $tpPresenca = $this->serviceBoletim()->getRegra()->get('tipoPresenca');

    if ($canDelete && $tpPresenca == $cnsPresenca::POR_COMPONENTE) {
      $canDelete = $this->validatesPresenceOfComponenteCurricularId(false);
      $componenteCurricularId = $this->getRequest()->componente_curricular_id;
    }
    else
      $componenteCurricularId = null;

    if ($canDelete && is_null($this->getFaltaAtual())) {
      $this->messenger->append('Falta matrícula '. $this->getRequest()->matricula_id .' inexistente ou já removida.', 'notice');
    }
    elseif ($canDelete) {
      $this->serviceBoletim()->deleteFalta($this->getRequest()->etapa, $componenteCurricularId);
      $this->saveService();
      $this->messenger->append('Falta matrícula '. $this->getRequest()->matricula_id .' removida com sucesso.', 'success');
    }

    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());
  }


  protected function deleteParecer() {
    if ($this->canDeleteParecer()) {
      $parecerAtual = $this->getParecerAtual();

      if ((is_null($parecerAtual) || $parecerAtual == '')) {
        $this->messenger->append('Parecer descritivo matrícula '. $this->getRequest()->matricula_id .' inexistente ou já removido.', 'notice');
      }
      else{
        $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
        $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

        if ($tpParecer == $cnsParecer::ANUAL_COMPONENTE || $tpParecer == $cnsParecer::ETAPA_COMPONENTE)
          $this->serviceBoletim()->deleteParecer($this->getRequest()->etapa, $this->getRequest()->componente_curricular_id);
        else
          $this->serviceBoletim()->deleteParecer($this->getRequest()->etapa);

        $this->saveService();
        $this->messenger->append('Parecer descritivo matrícula '. $this->getRequest()->matricula_id .' removido com sucesso.', 'success');
      }
    }

    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());
  }


  protected function postNota() {
    if ($this->canPostNota()) {

      $nota = new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => $this->getRequest()->componente_curricular_id,
        'nota' => urldecode($this->getRequest()->att_value),
        'etapa' => $this->getRequest()->etapa
        ));

      $this->serviceBoletim()->addNota($nota);
      $this->saveService();
      $this->messenger->append('Nota matrícula '. $this->getRequest()->matricula_id .' alterada com sucesso.', 'success');
    }

    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());
  }


  protected function getQuantidadeFalta() {
    $quantidade = (int) $this->getRequest()->att_value;

    if ($quantidade < 0)
      $quantidade = 0;

    return $quantidade;
  }


  protected function getFaltaGeral() {
    return new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => $this->getQuantidadeFalta(),
        'etapa' => $this->getRequest()->etapa
    ));
  }


  protected function getFaltaComponente() {
    return new Avaliacao_Model_FaltaComponente(array(
            'componenteCurricular' => $this->getRequest()->componente_curricular_id,
            'quantidade' => $this->getQuantidadeFalta(),
            'etapa' => $this->getRequest()->etapa
    ));
  }


  protected function postFalta() {

    $canPost = $this->canPostFalta();
    if ($canPost && $this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
      $canPost = $this->validatesPresenceOfComponenteCurricularId(false);

    if ($canPost) {
      if ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
        $falta = $this->getFaltaComponente();
      elseif ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
        $falta = $this->getFaltaGeral();

      $this->serviceBoletim()->addFalta($falta);
      $this->saveService();
      $this->messenger->append('Falta matrícula '. $this->getRequest()->matricula_id .' alterada com sucesso.', 'success');
    }

    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());
  }


  protected function getParecerComponente() {
    return new Avaliacao_Model_ParecerDescritivoComponente(array(
              'componenteCurricular' => $this->getRequest()->componente_curricular_id,
              'parecer'  => addslashes($this->getRequest()->att_value),
              'etapa'  => $this->getRequest()->etapa
    ));
  }


  protected function getParecerGeral() {
    return new Avaliacao_Model_ParecerDescritivoGeral(array(
              'parecer' => addslashes($this->getRequest()->att_value),
              'etapa'   => $this->getRequest()->etapa
    ));
  }


  protected function postParecer() {

    if ($this->canPostParecer()) {
      $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');
      $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;

      if ($tpParecer == $cnsParecer::ETAPA_COMPONENTE || $tpParecer == $cnsParecer::ANUAL_COMPONENTE)
        $parecer = $this->getParecerComponente();
      else
        $parecer = $this->getParecerGeral();

      $this->serviceBoletim()->addParecer($parecer);
      $this->saveService();
      $this->messenger->append('Parecer descritivo matricula '. $this->getRequest()->matricula_id .' alterado com sucesso.', 'success');
    }

    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());
  }


  protected function getSituacaoMatricula() {
    $situacao = 'Situação não recuperada';
    $ccId     = $this->getRequest()->componente_curricular_id;

    try {
      $componente = $this->serviceBoletim()->getSituacaoComponentesCurriculares()->componentesCurriculares[$ccId];
      $situacao   = App_Model_MatriculaSituacao::getInstance()->getValue($componente->situacao);
    }
    catch (Exception $e) {
      $matriculaId = $this->getRequest()->matricula_id;
      $this->messenger->append("Erro ao recuperar situação da matrícula '$matriculaId': " . $e->getMessage());
    }

    return $this->safeString($situacao);
  }


  protected function getMatriculas() {
    $matriculas = array();

    if ($this->canGetMatriculas()) {
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
        $matricula   = array();
        $matriculaId = $aluno['ref_cod_matricula'];

        $this->setService($matriculaId);

        $matricula['componentes_curriculares'] = $this->loadComponentesCurricularesForMatricula($matriculaId);

        $matricula['situacao'] = $this->getSituacaoMatricula();
        $matricula['nota_atual'] = '-1'; #$this->getNotaAtual();
        $matricula['nota_exame'] = $this->getNotaExame();
        $matricula['falta_atual'] = '-1';#$this->getFaltaAtual();
        $matricula['parecer_atual'] = '-1'; #$this->getParecerAtual();

        foreach($requiredFields as $f)
          $matricula[$f[0]] = $aluno[$f[1]];

        $matricula['nome'] = $this->safeString($aluno['nome_aluno']);

        $matriculas[] = $matricula;
      }
    }

    // adiciona regras de avaliacao
    if(! empty($matriculas))
      $this->appendResponse('regra_avaliacao', $this->getRegraAvaliacao());

    $this->appendResponse('matricula_id', $this->getRequest()->matricula_id);
    $this->appendResponse('situacao',     $this->getSituacaoMatricula());

    return $matriculas;
  }


  protected function loadComponentesCurricularesForMatricula($matriculaId) {
    $componentesCurriculares = array();

    // caso não receba, carrega componentes que possuem nota, falta ou parecer lançado para a matricula
    if (! is_numeric($this->getRequest()->componente_curricular_id)) {
      $componentesCurricularesNotas     = array_keys($this->serviceBoletim()->getNotasComponentes());
      $componentesCurricularesFaltas    = array_keys($this->serviceBoletim()->getFaltasComponentes());
      $componentesCurricularesPareceres = array_keys($this->serviceBoletim()->getPareceresComponentes());

      $componentesCurricularesIds       = array($componentesCurricularesNotas, 
                                                $componentesCurricularesFaltas, 
                                                $componentesCurricularesPareceres);

      $componentesCurricularesIds = Portabilis_Array_Utils::mergeValues($componentesCurricularesIds);
    }
    else
      $componentesCurricularesIds = array($this->getRequest()->componente_curricular_id);


    // monta lista de componentes curriculares
    foreach($componentesCurricularesIds as $ccId) {
      $componenteCurricular         = array();
      $componenteCurricular['id']   = $ccId;

      $ccDataMapper = $this->getDataMapperFor('componenteCurricular', 'componente');
      $cc = $ccDataMapper->find($ccId);


      $componenteCurricular['nome'] = $this->safeString($cc->get('nome'));

      $componenteCurricular['nota_atual']    = $this->getNotaAtual($etapa = null, $ccId);
      $componenteCurricular['falta_atual']   = $this->getFaltaAtual($etapa = null, $ccId);
      $componenteCurricular['parecer_atual'] = $this->getParecerAtual($ccId);

      $componentesCurriculares[] = $componenteCurricular;
    }

    //print_r($componentesCurriculares);
    return $componentesCurriculares;
  }


  protected function getNotaAtual($etapa = null, $componenteCurricularId = null) {
    // defaults
    if (is_null($componenteCurricularId))
      $componenteCurricularId = $this->getRequest()->componente_curricular_id;

    if (is_null($etapa))
      $etapa = $this->getRequest()->etapa;

    // validacao
    if (! is_numeric($componenteCurricularId)) {
      var_dump($componenteCurricularId);
      throw new Exception('Não foi possivel obter a nota atual, pois não foi recebido o id do componente curricular.');
    }

    $nota = urldecode($this->serviceBoletim()->getNotaComponente($componenteCurricularId, $etapa)->nota);
    return str_replace(',', '.', $nota);
  }


  protected function getNotaExame() {

  /* removido checagem se usa nota e se a formula recuperacao é do tipo media recuperacao,
     pois se existe nota lançada mostrará.

    $this->serviceBoletim()->getRegra()->get('tipoNota') != RegraAvaliacao_Model_Nota_TipoValor::NENHUM &&
    $this->serviceBoletim()->getRegra()->formulaRecuperacao->get('tipoFormula') == FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO */

    //se é a ultima etapa
    if($this->getRequest()->etapa == $this->serviceBoletim()->getOption('etapas'))
      $nota = $this->getNotaAtual($etapa='Rc');
    else
      $nota = '';

    return $nota;
  }


  protected function getFaltaAtual($etapa = null, $componenteCurricularId = null) {
    // defaults
    if (is_null($componenteCurricularId))
      $componenteCurricularId = $this->getRequest()->componente_curricular_id;

    if (is_null($etapa))
      $etapa = $this->getRequest()->etapa;

    // validacao
    if (! is_numeric($componenteCurricularId))
      throw new Exception('Não foi possivel obter a falta atual, pois não foi recebido o id do componente curricular.');


    if ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)
      $falta = $this->serviceBoletim()->getFalta($etapa, $this->getRequest()->componente_curricular_id)->quantidade;

    elseif ($this->serviceBoletim()->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::GERAL)
      $falta = $this->serviceBoletim()->getFalta($etapa)->quantidade;

    return $falta;
  }


  protected function getEtapaParecer() {
    if($this->getRequest()->etapa != 'An' && ($this->serviceBoletim()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE || $this->serviceBoletim()->getRegra()->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL)) {
      return 'An';
    }
    else
      return $this->getRequest()->etapa;
  }


  protected function getParecerAtual($componenteCurricularId = null) {
    // defaults
    if (is_null($componenteCurricularId))
      $componenteCurricularId = $this->getRequest()->componente_curricular_id;

    // validacao
    if (! is_numeric($componenteCurricularId))
      throw new Exception('Não foi possivel obter o parecer descritivo atual, pois não foi recebido o id do componente curricular.');


    $etapaComponente = $this->serviceBoletim()->getRegra()->get('parecerDescritivo') ==
                       RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE;

    $anualComponente = $this->serviceBoletim()->getRegra()->get('parecerDescritivo') ==
                       RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE;

    if ($etapaComponente or $anualComponente)
      return utf8_encode($this->serviceBoletim()->getParecerDescritivo($this->getEtapaParecer(), $componenteCurricularId));
    else
      return utf8_encode($this->serviceBoletim()->getParecerDescritivo($this->getEtapaParecer()));
  }


  protected function getOpcoesFaltas() {
    $opcoes = array();

    foreach (range(0, 100, 1) as $f)
      $opcoes[$f] = $f;

    return $opcoes;
  }


  protected function canGetOpcoesNotas() {
    return $this->validatesPresenceOf('matricula_id');
  }


  protected function getOpcoesNotas() {
    $opcoes = array();

    if ($this->canGetOpcoesNotas()) {
      $tpNota  = $this->serviceBoletim()->getRegra()->get('tipoNota');
      $cnsNota = RegraAvaliacao_Model_Nota_TipoValor;

      if ($tpNota != $cnsNota::NENHUM) {
        $tabela = $this->serviceBoletim()->getRegra()->tabelaArredondamento->findTabelaValor();

        foreach ($tabela as $item) {
          if ($tpNota == $cnsNota::NUMERICA)
            $opcoes[(string) $item->nome] = (string) $item->nome;
          else
            $opcoes[(string) $item->valorMaximo] = safeString($item->nome . ' (' . $item->descricao .  ')');
        }
      }
    }

    return $opcoes;
  }


  protected function canGetRegraAvaliacao() {
    return $this->validatesPresenceOf('matricula_id');
  }


  protected function getRegraAvaliacao() {
    $itensRegra = array();

    if ($this->canGetRegraAvaliacao()) {
      $regra              = $this->serviceBoletim()->getRegra();
      $itensRegra['id']   = $regra->get('id');
      $itensRegra['nome'] = $this->safeString($regra->get('nome'));


      // tipo presença
      $cnsPresenca = RegraAvaliacao_Model_TipoPresenca;
      $tpPresenca  = $this->serviceBoletim()->getRegra()->get('tipoPresenca');

      if($tpPresenca == $cnsPresenca::GERAL)
        $itensRegra['tipo_presenca'] = 'geral';

      elseif($tpPresenca == $cnsPresenca::POR_COMPONENTE)
        $itensRegra['tipo_presenca'] = 'por_componente';

      else
        $itensRegra['tipo_presenca'] = $tpPresenca;


      // tipo nota
      $cnsNota = RegraAvaliacao_Model_Nota_TipoValor;
      $tpNota  = $this->serviceBoletim()->getRegra()->get('tipoNota');

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


      // tipo parecer
      $cnsParecer = RegraAvaliacao_Model_TipoParecerDescritivo;
      $tpParecer = $this->serviceBoletim()->getRegra()->get('parecerDescritivo');

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

      // opcoes notas
      $itensRegra['opcoes_notas']      = $this->getOpcoesNotas();

      // etapas
      $itensRegra['quantidade_etapas'] = $this->serviceBoletim()->getOption('etapas');
    }

    return $itensRegra;
  }


  protected function serviceBoletim($matriculaId = null, $reload = false) {
    // defaults
    if (is_null($matriculaId))
      $matriculaId = $this->getRequest()->matricula_id;

    if (! isset($this->_boletimServiceInstances))
      $this->_boletimServiceInstances = array();

    // set service
    if (! isset($this->_boletimServiceInstances[$matriculaId]) || $reload) {
      try {
        $params = array('matricula' => $matriculaId, 'usuario' => $this->getSession()->id_pessoa);
        $this->_boletimServiceInstances[$matriculaId] = new Avaliacao_Service_Boletim($params);
      }
      catch (Exception $e){
        $this->messenger->append("Erro ao instanciar serviço boletim para matricula {$matriculaId}: " . $e->getMessage());
      }
    }

    // validates service
    if (is_null($this->_boletimServiceInstances[$matriculaId]))
      throw new CoreExt_Exception("Não foi possivel instanciar o serviço boletim para a matricula $matriculaId.");

    return $this->_boletimServiceInstances[$matriculaId];
  }


  protected function trySaveService($service) {
    try {
      $service->save();
    }
    catch (CoreExt_Service_Exception $e) {
      // excecoes ignoradas :( pois servico lanca excecoes de alertas, que não são exatamente erros.
      // error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
    }
  }



  protected function saveService() {
    try {
      $this->serviceBoletim()->save();
    }
    catch (CoreExt_Service_Exception $e) {
      //excecoes ignoradas :( servico lanca excecoes de alertas, que não são exatamente erros.
      //error_log('CoreExt_Service_Exception ignorada: ' . $e->getMessage());
    }
  }


  protected function getService($raiseExceptionOnErrors = false, $appendMsgOnErrors = true) {
    if (isset($this->service) && ! is_null($this->service))
      return $this->service;

    $msg = 'Erro ao recuperar serviço boletim: serviço não definido.';
    if($appendMsgOnErrors)
      $this->messenger->append($msg);

    if ($raiseExceptionOnErrors)
      throw new Exception($msg);

    return null;
  }


  protected function setService($matriculaId = null) {
    if (! $matriculaId)
      $matriculaId = $this->getRequest()->matricula_id;

    $params = array('matricula' => $matriculaId, 'usuario' => $this->getSession()->id_pessoa);

    try {
      $this->service = new Avaliacao_Service_Boletim($params);
    }
    catch (Exception $e) {
      $this->messenger->append('Exception ao instanciar serviço boletim: ' . $e->getMessage(), 'error', $encodeToUtf8 = true);

      return false;
    }

    return true;
  }


  // TODO implementar modo para informar responders oper
  //      adicionar validacao em canAcceptRequest para ver se oper e resource match
  //      nesta funcao (gerar) chamar $this->operResource(), se oper == get, appendResponse(resource, ...) && canOperResource
  public function Gerar() {
    if ($this->isRequestFor('get', 'matriculas'))
      $this->appendResponse('matriculas', $this->getMatriculas());

    elseif ($this->isRequestFor('get', 'opcoes_notas'))
      $this->appendResponse('opcoes_notas', $this->getOpcoesNotas());

    elseif ($this->isRequestFor('get', 'opcoes_faltas'))
      $this->appendResponse('opcoes_faltas', $this->getOpcoesFaltas());

    elseif ($this->isRequestFor('get', 'regra_avaliacao'))
      $this->appendResponse('regra_avaliacao', $this->getRegraAvaliacao());

    elseif ($this->isRequestFor('post', 'nota') || $this->isRequestFor('post', 'nota_exame'))
      $this->postNota();

    elseif ($this->isRequestFor('post', 'falta'))
      $this->postFalta();

    elseif ($this->isRequestFor('post', 'parecer'))
      $this->postParecer();

    elseif ($this->isRequestFor('delete', 'nota') || $this->isRequestFor('delete', 'nota_exame'))
        $this->deleteNota();

    elseif ($this->isRequestFor('delete', 'falta'))
        $this->deleteFalta();

    elseif ($this->isRequestFor('delete', 'parecer'))
        $this->deleteParecer();

    else
      $this->notImplementedOperationError();
  }
}
