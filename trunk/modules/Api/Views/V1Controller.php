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
           $this->validatesPresenceOf(array('aluno_id', 'escola_id'));
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
      $this->messenger->append('Erro ao instanciar serviço boletim: ' . $e->getMessage());
    }

    return $service;
  }

  
  protected function reportBoletimTemplateForMatricula($id) {
    $template = '';

    $templates = array('bimestral'                     => 'portabilis_boletim',
                       'trimestral'                    => 'portabilis_boletim_trimestral',
                       'trimestral_conceitual'         => 'portabilis_boletim_primeiro_ano_trimestral',
                       'semestral'                     => 'portabilis_boletim_semestral',
                       'semestral_conceitual'          => 'portabilis_boletim_conceitual_semestral',
                       'semestral_educacao_infantil'   => 'portabilis_boletim_educ_infantil_semestral',
                       'parecer_descritivo_componente' => 'portabilis_boletim_parecer',
                       'parecer_descritivo_geral'      => 'portabilis_boletim_parecer_geral');
                        
    $service                            = $this->serviceBoletimForMatricula($id);

    if ($service != null) {
      # FIXME perguntar service se nota é conceitual?
      $notaConceitual                     = false;
      $qtdEtapasModulo                    = $service->getOption('etapas');

      # FIXME veriificar se é educação infantil?
      $educacaoInfantil                   = false;


      // parecer

      $flagParecerGeral          = array(RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,                   
                                     RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL);

      $flagParecerComponente = array(RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
                                     RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE);

      $parecerAtual                = $service->getRegra()->get('parecerDescritivo');
      $parecerDescritivoGeral      = in_array($parecerAtual, $flagParecerGeral);
      $parecerDescritivoComponente = in_array($parecerAtual, $flagParecerComponente);


      // decide qual templete usar

      if ($parecerDescritivoGeral)
        $template = 'parecer_descritivo_geral';

      elseif ($parecerDescritivoComponente)
        $template = 'parecer_descritivo_componente';

      elseif ($qtdEtapasModulo > 5 && $educacaoInfantil)
        $template = 'semestral_educacao_infantil';

      elseif ($qtdEtapasModulo > 5 && $notaConceitual)
        $template = 'semestral_conceitual';

      elseif ($qtdEtapasModulo > 5)
        $template = 'semestral';

      elseif ($qtdEtapasModulo > 2 && $notaConceitual)
        $template = 'trimestral_conceitual';

      elseif ($qtdEtapasModulo > 2)
        $template = 'trimestral';

      else
        $template = 'bimestral';

      $template = $templates[$template];
    }

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


  protected function loadNomeSerie($serieId){
    $sql = "select nm_serie from pmieducar.serie where cod_serie = $1";
    $nome = $this->fetchPreparedQuery($sql, $serieId, false, 'first-field');

    return utf8_encode(strtoupper($nome));
  }


  protected function loadMatriculaTurma($matricula) {
    $matriculaTurma = new clsPmieducarMatriculaTurma();

    // atualmente somente carrega as matriculas de determinada escola
    $matriculaTurma = $matriculaTurma->lista(
      $matricula['id'],
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      1,
      $matricula['serie_id'],
      $matricula['curso_id'],
      $matricula['escola_id'],
      null,
      $matricula['aluno_id']
    );

    $matriculaTurma = $matriculaTurma[0];

    if (is_array($matriculaTurma) && count($matriculaTurma) > 0){
      $attrs = array('ref_cod_turma'       => 'turma_id',
                     'ref_cod_instituicao' => 'instituicao_id',
                     'ref_ref_cod_escola'  => 'escola_id',
                     'ref_ref_cod_serie'   => 'serie_id',
                     'ref_cod_matricula'   => 'id',
                     'nm_curso'            => 'nome_instituicao',
                     'nm_curso'            => 'nome_curso',
                     'nm_turma'            => 'nome_turma');

      $matriculaTurma                            = Portabilis_Array_Utils::filter($matriculaTurma, $attrs);

      $matriculaTurma['ano']                     = $matricula['ano'];
      $matriculaTurma['curso_id']                = $matricula['curso_id'];
      $matriculaTurma['nome_escola']             = $this->loadNomeEscola();
      $matriculaTurma['nome_curso']              = utf8_decode(strtoupper($matriculaTurma['nome_curso']));
      $matriculaTurma['nome_serie']              = $this->loadNomeSerie($matriculaTurma['serie_id']);
      $matriculaTurma['nome_turma']              = utf8_decode(strtoupper($matriculaTurma['nome_turma']));
      $matriculaTurma['report_boletim_template'] = $this->reportBoletimTemplateForMatricula($matricula['id']);
    }
    else{
      throw new Exception("Não foi possivel recuperar a matricula: {$matricula['id']}.");
    }

    return $matriculaTurma;
  }


  protected function loadMatriculas($loadMatriculaTurma = false) {
    $sql = "select ano, cod_matricula as id, ref_ref_cod_escola as escola_id, ref_cod_curso as curso_id, ref_ref_cod_serie as serie_id from pmieducar.matricula where ref_cod_aluno = $1 and ref_ref_cod_escola = $2 and ativo = 1 order by ano desc, id";

    $params      = array($this->getRequest()->aluno_id, $this->getRequest()->escola_id);
    $_matriculas = $this->fetchPreparedQuery($sql, $params, false);

    if (is_array($_matriculas) && $loadMatriculaTurma) {
      $matriculas = array();

      foreach($_matriculas as $matricula)
        $matriculas[] = $this->loadMatriculaTurma($matricula);
    }
    else
      $matriculas = $_matriculas;

    return $matriculas;
  }


  protected function loadOcorrenciasDisciplinares() {
    $ocorrenciasAluno              = array();
    $matriculas                    = $this->loadMatriculas();

    $attrsFilter                   = array('data_cadastro' => 'data_hora', 'observacao' => 'descricao');
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
                                                                    1);

      if (is_array($ocorrenciasMatricula)) {
        $ocorrenciasMatricula = Portabilis_Array_Utils::filterSet($ocorrenciasMatricula, $attrsFilter);

        foreach($ocorrenciasMatricula as $ocorrenciaMatricula) {
          $ocorrenciaMatricula['data_hora']      = date('d/m/Y H:i:s', strtotime($ocorrenciaMatricula['data_hora']));
          $ocorrenciaMatricula['descricao']      = utf8_encode($ocorrenciaMatricula['descricao']);
        }

        $ocorrenciasAluno[] = $ocorrenciaMatricula;
      }
    }  

    return $ocorrenciasAluno;
  }


  // api responder

  protected function getAluno() {
    $aluno = array('id'         => $this->getRequest()->aluno_id, 
                   'nome'       => $this->loadNomeAluno(), 
                   'matriculas' => $this->loadMatriculas(true));

    return $aluno;
  }


  protected function getOcorrenciasDisciplinares() {
    return $this->loadOcorrenciasDisciplinares();
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'aluno'))
      $this->appendResponse('aluno', $this->getAluno());
    if ($this->isRequestFor('get', 'ocorrencias_disciplinares'))
      $this->appendResponse('ocorrencias_disciplinares', $this->getOcorrenciasDisciplinares());
    else
      $this->notImplementedOperationError();
  }
}
