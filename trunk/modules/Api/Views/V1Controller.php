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
require_once 'Avaliacao/Service/Boletim.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'Portabilis/Model/Report/TipoBoletim.php';
require_once "Reports/Reports/BoletimReport.php";

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
           $this->validatesPresenceOf('escola_id') && 
           $this->validatesExistenceOf('escola', $this->getRequest()->escola_id);
  }


  protected function canGetAluno() {
    return $this->validatesPresenceOf('aluno_id');
  }


  protected function canGetOcorrenciasDisciplinares() {
    return $this->validatesPresenceOf('aluno_id') &&
           $this->validatesExistenceOf('aluno', $this->getRequest()->aluno_id);
  }


  protected function canGetRelatorioBoletim() {
    return $this->validatesPresenceOf(array('matricula_id', 'escola_id')) &&
           $this->validatesExistenceOf('matricula', $this->getRequest()->matricula_id);
  }


  protected function serviceBoletimForMatricula($id) {
    $service = null;

    # FIXME get $this->getSession()->id_pessoa se usuario logado
    # ou pegar id do ini config, se api request
    $userId = 1;

    try {
      $service = new Avaliacao_Service_Boletim(array('matricula' => $id, 'usuario' => $userId));
    }
    catch (Exception $e){
      $this->messenger->append("Erro ao instanciar serviço boletim para matricula {$id}: " . $e->getMessage());
    }

    return $service;
  }


  protected function reportBoletimTemplateFor($tipo_boletim) {
    $tiposBoletim = Portabilis_Model_Report_TipoBoletim;

    $templates = array($tiposBoletim::BIMESTRAL                     => 'portabilis_boletim',
                       $tiposBoletim::TRIMESTRAL                    => 'portabilis_boletim_trimestral',
                       $tiposBoletim::TRIMESTRAL_CONCEITUAL         => 'portabilis_boletim_primeiro_ano_trimestral',
                       $tiposBoletim::SEMESTRAL                     => 'portabilis_boletim_semestral',
                       $tiposBoletim::SEMESTRAL_CONCEITUAL          => 'portabilis_boletim_conceitual_semestral',
                       $tiposBoletim::SEMESTRAL_EDUCACAO_INFANTIL   => 'portabilis_boletim_educ_infantil_semestral',
                       $tiposBoletim::PARECER_DESCRITIVO_COMPONENTE => 'portabilis_boletim_parecer',
                       $tiposBoletim::PARECER_DESCRITIVO_GERAL      => 'portabilis_boletim_parecer_geral');

    $template = is_null($tipo_boletim) ? '' : $templates[$tipo_boletim];

    return $template;
  }
    
  // load resources

  protected function loadNomeEscola() {
    $sql = "select nome from cadastro.pessoa, pmieducar.escola where idpes = ref_idpes and cod_escola = $1";
    $nome = $this->fetchPreparedQuery($sql, $this->getRequest()->escola_id, false, 'first-field');

    return utf8_encode(strtoupper($nome));
  }


  protected function loadNomeAluno() {
    $sql = "select nome from cadastro.pessoa, pmieducar.aluno where idpes = ref_idpes and cod_aluno = $1";
    $nome = $this->fetchPreparedQuery($sql, $this->getRequest()->aluno_id, false, 'first-field');

    return utf8_encode(ucwords(strtolower($nome)));
  }


  protected function loadNameFor($resourceName, $id){
    $sql = "select nm_{$resourceName} from pmieducar.{$resourceName} where cod_{$resourceName} = $1";
    $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

    return utf8_encode(strtoupper($nome));
  }


  protected function tryLoadMatriculaTurma($matricula) {
    $sql            = "select ref_cod_turma as turma_id, turma.tipo_boletim from pmieducar.matricula_turma, pmieducar.turma where ref_cod_turma = cod_turma and ref_cod_matricula = $1 and matricula_turma.ativo = 1 limit 1";

    //var_dump($sql);
    $matriculaTurma = $this->fetchPreparedQuery($sql, $matricula['id'], false, 'first-row');

    if (is_array($matriculaTurma) and count($matriculaTurma) > 0) {
      $attrs                                     = array('turma_id', 'tipo_boletim');

      $matriculaTurma                            = Portabilis_Array_Utils::filter($matriculaTurma, $attrs);
      $matriculaTurma['nome_turma']              = $this->loadNameFor('turma', $matriculaTurma['turma_id']);
      $matriculaTurma['report_boletim_template'] = $this->reportBoletimTemplateFor($matriculaTurma['tipo_boletim']);
    }

    return $matriculaTurma;
  }


  // carrega dados matricula (instituicao_id, escola_id, curso_id, serie_id e (first) turma_id, ano) de uma matricula.
  protected function loadDadosForMatricula($matriculaId){
    $sql            = "select cod_matricula as id, matricula.ano, escola.ref_cod_instituicao as instituicao_id, matricula.ref_ref_cod_escola as escola_id, matricula.ref_cod_curso as curso_id, matricula.ref_ref_cod_serie as serie_id, matricula_turma.ref_cod_turma as turma_id from pmieducar.matricula_turma, pmieducar.matricula, pmieducar.escola where escola.cod_escola = matricula.ref_ref_cod_escola and ref_cod_matricula = cod_matricula and ref_cod_matricula = $1 and matricula.ativo = matricula_turma.ativo and matricula_turma.ativo = 1 order by matricula_turma.sequencial limit 1";

    $params         = array($matriculaId);
    $dadosMatricula = $this->fetchPreparedQuery($sql, $params, false, 'first-row');

    // filtra apenas chaves abaixo, deixando de fora os indices.
    $attrs          = array('id', 'ano', 'instituicao_id', 'escola_id', 'curso_id', 'serie_id', 'turma_id');
    $dadosMatricula = Portabilis_Array_Utils::filter($dadosMatricula, $attrs);

    return $dadosMatricula;
  }


  protected function loadMatriculasAluno() {
    #TODO mostrar o nome da situação da matricula
    $sql = "select cod_matricula as id, ano, ref_cod_instituicao as instituicao_id, ref_ref_cod_escola as escola_id, ref_cod_curso as curso_id, ref_ref_cod_serie as serie_id from pmieducar.matricula, pmieducar.escola where cod_escola = ref_ref_cod_escola and ref_cod_aluno = $1 and ref_ref_cod_escola = $2 and matricula.ativo = 1 order by ano desc, id";

    $params     = array($this->getRequest()->aluno_id, $this->getRequest()->escola_id);
    $matriculas = $this->fetchPreparedQuery($sql, $params, false);

    if (is_array($matriculas) && count($matriculas) > 0) {
      $attrs      = array('id', 'ano', 'instituicao_id', 'escola_id', 'curso_id', 'serie_id');
      $matriculas = Portabilis_Array_Utils::filterSet($matriculas, $attrs);

      foreach($matriculas as $key => $matricula) {
        $matriculas[$key]['nome_curso']                = $this->loadNameFor('curso', $matricula['curso_id']);
        $matriculas[$key]['nome_escola']               = $this->loadNomeEscola();
        $matriculas[$key]['nome_serie']                = $this->loadNameFor('serie', $matricula['serie_id']);
        $turma                                         = $this->tryLoadMatriculaTurma($matricula);

        if (is_array($turma) and count($turma) > 0) {
          $matriculas[$key]['turma_id']                = $turma['turma_id'];
          $matriculas[$key]['nome_turma']              = $turma['nome_turma'];
          $matriculas[$key]['report_boletim_template'] = $turma['report_boletim_template'];
        }
      }
    }

    return $matriculas;
  }


  protected function loadTipoOcorrenciaDisciplinar($id) {
    if (! isset($this->_tiposOcorrenciasDisciplinares))
      $this->_tiposOcorrenciasDisciplinares = array();

    if (! isset($this->_tiposOcorrenciasDisciplinares[$id])) {
      $ocorrencia                                  = new clsPmieducarTipoOcorrenciaDisciplinar;
      $ocorrencia->cod_tipo_ocorrencia_disciplinar = $id;
      $ocorrencia                                  = $ocorrencia->detalhe();

      $this->_tiposOcorrenciasDisciplinares[$id]   = utf8_encode($ocorrencia['nm_tipo']);
    }

    return $this->_tiposOcorrenciasDisciplinares[$id];
  }


  protected function loadOcorrenciasDisciplinares() {
    $ocorrenciasAluno              = array();
    $matriculas                    = $this->loadMatriculasAluno();

    $attrsFilter                   = array('ref_cod_tipo_ocorrencia_disciplinar' => 'tipo', 
                                           'data_cadastro'                       => 'data_hora',
                                           'observacao'                          => 'descricao');

    $ocorrenciasMatriculaInstance  = new clsPmieducarMatriculaOcorrenciaDisciplinar();

    foreach($matriculas as $matricula) {
      $ocorrenciasMatricula = $ocorrenciasMatriculaInstance->lista($matricula['id'], 
                                                                    null, 
                                                                    null, 
                                                                    null, 
                                                                    null, 
                                                                    null, 
                                                                    null, 
                                                                    null, 
                                                                    null, 
                                                                    null, 
                                                                    1,
                                                                    $visivel_pais = 1);

      if (is_array($ocorrenciasMatricula)) {
        $ocorrenciasMatricula = Portabilis_Array_Utils::filterSet($ocorrenciasMatricula, $attrsFilter);

        foreach($ocorrenciasMatricula as $ocorrenciaMatricula) {
          $ocorrenciaMatricula['tipo']      = $this->loadTipoOcorrenciaDisciplinar($ocorrenciaMatricula['tipo']);
          $ocorrenciaMatricula['data_hora'] = date('d/m/Y H:i:s', strtotime($ocorrenciaMatricula['data_hora']));
          $ocorrenciaMatricula['descricao'] = utf8_encode($ocorrenciaMatricula['descricao']);
          $ocorrenciasAluno[]               = $ocorrenciaMatricula;
        }
      }
    }  

    return $ocorrenciasAluno;
  }


  // api responder

  protected function getAluno() {
    if ($this->canGetAluno() && $this->validatesExistenceOf('aluno', $this->getRequest()->aluno_id, array('add_msg_on_error' => false))) {
      return array('id'         => $this->getRequest()->aluno_id, 
                   'nome'       => $this->loadNomeAluno(), 
                   'matriculas' => $this->loadMatriculasAluno(true));
    }
  }


  protected function getOcorrenciasDisciplinares() {
    if ($this->canGetOcorrenciasDisciplinares())
      return $this->loadOcorrenciasDisciplinares();
  }


  protected function getRelatorioBoletim() {
    if ($this->canGetRelatorioBoletim()) {
      $dadosMatricula = $this->loadDadosForMatricula($this->getRequest()->matricula_id);

      $boletimReport = new BoletimReport();
      
      $boletimReport->addArg('matricula',   (int)$dadosMatricula['id']);
      $boletimReport->addArg('ano',         (int)$dadosMatricula['ano']);
      $boletimReport->addArg('instituicao', (int)$dadosMatricula['instituicao_id']);
      $boletimReport->addArg('escola',      (int)$dadosMatricula['escola_id']);
      $boletimReport->addArg('curso',       (int)$dadosMatricula['curso_id']);
      $boletimReport->addArg('serie',       (int)$dadosMatricula['serie_id']);
      $boletimReport->addArg('turma',       (int)$dadosMatricula['turma_id']);


      $encoding     = 'base64';

      $dumpsOptions = array('options' => array('encoding' => $encoding));
      $encoded      = $boletimReport->dumps($dumpsOptions);

      return array('matricula_id' => $this->getRequest()->matricula_id,
                   'encoding'     => $encoding,
                   'encoded'      => $encoded);
    }
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'aluno'))
      $this->appendResponse('aluno', $this->getAluno());

    elseif ($this->isRequestFor('get', 'ocorrencias_disciplinares'))
      $this->appendResponse('ocorrencias_disciplinares', $this->getOcorrenciasDisciplinares());

    elseif ($this->isRequestFor('get', 'relatorio_boletim'))
      $this->appendResponse('relatorio_boletim', $this->getRelatorioBoletim());    

    else
      $this->notImplementedOperationError();
  }
}
