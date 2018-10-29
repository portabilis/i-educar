<?php
 //error_reporting(E_ALL);
 //ini_set("display_errors", 1);
/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
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
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'Avaliacao/Fixups/CleanComponentesCurriculares.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Utils/CustomLabel.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';
require_once 'lib/App/Model/Educacenso/TipoMediacaoDidaticoPedagogico.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Turma');
    $this->processoAp = 586;
    $this->addEstilo("localizacaoSistema");
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $cod_turma;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_serie;
  var $ref_cod_serie_;
  var $ref_ref_cod_escola;
  var $ref_cod_infra_predio_comodo;
  var $nm_turma;
  var $sgl_turma;
  var $max_aluno;
  var $multiseriada;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_cod_turma_tipo;
  var $hora_inicial;
  var $hora_final;
  var $hora_inicio_intervalo;
  var $hora_fim_intervalo;

  var $ref_cod_instituicao;
  var $ref_cod_curso;
  var $ref_cod_escola_;

  var $padrao_ano_escolar;

  var $ref_cod_regente;
  var $ref_cod_instituicao_regente;

  var $ref_cod_serie_mult;

  // Inclui módulo
  var $turma_modulo = [];
  var $incluir_modulo;
  var $excluir_modulo;

  var $visivel;

  var $tipo_atendimento;
  var $turma_mais_educacao;
  var $atividades_complementares;
  var $atividades_aee;
  var $cod_curso_profissional;
  var $etapa_educacenso;
  var $ref_cod_disciplina_dispensada;
  var $codigo_inep_educacenso;
  var $tipo_mediacao_didatico_pedagogico;
  var $dias_semana;
  var $tipo_boletim;
  var $tipo_boletim_diferenciado;
  var $sequencial;
  var $ref_cod_modulo;
  var $data_inicio;
  var $data_fim;
  var $dias_letivos;

  var $etapas_especificas;
  var $etapas_utilizadas;
  var $definirComponentePorEtapa;

  var $modulos = [];

  var $retorno;

  var $dias_da_semana = array(
    '' => 'Selecione',
    1  => 'Domingo',
    2  => 'Segunda',
    3  => 'Ter&ccedil;a',
    4  => 'Quarta',
    5  => 'Quinta',
    6  => 'Sexta',
    7  => 'S&aacute;bado'
  );

  var $nao_informar_educacenso;

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->cod_turma = $_GET['cod_turma'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(586, $this->pessoa_logada, 7, 'educar_turma_lst.php');

    if (is_numeric($this->cod_turma)) {
      $obj_turma = new clsPmieducarTurma($this->cod_turma);
      $registro  = $obj_turma->detalhe();
      $obj_esc   = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
      $det_esc   = $obj_esc->detalhe();
      $obj_ser   = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
      $det_ser   = $obj_ser->detalhe();

      $regra_avaliacao_id = $det_ser["regra_avaliacao_id"];
      if ($regra_avaliacao_id) {
        $regra_avaliacao_mapper = new RegraAvaliacao_Model_RegraDataMapper();
        $regra_avaliacao = $regra_avaliacao_mapper->find($regra_avaliacao_id);

        $this->definirComponentePorEtapa = ($regra_avaliacao->definirComponentePorEtapa == 1);
      }

      $this->dependencia_administrativa = $det_esc['dependencia_administrativa'];
      $this->ref_cod_escola      = $det_esc['cod_escola'];
      $this->ref_cod_instituicao = $det_esc['ref_cod_instituicao'];
      $this->ref_cod_curso       = $det_ser['ref_cod_curso'];
      $this->ref_cod_serie       = $det_ser['cod_serie'];

      $obj_curso = new clsPmieducarCurso(($this->ref_cod_curso));
      $det_curso = $obj_curso->detalhe();
      $this->padrao_ano_escolar = $det_curso['padrao_ano_escolar'];
      $this->modalidade_curso = $det_curso['modalidade_curso'];

      $inep = $obj_turma->getInep();

      if ($inep) {
        $this->codigo_inep_educacenso = $inep;
      }

      if ($registro) {
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

         $objTurma = new clsPmieducarTurma($this->cod_turma);
         $possuiAlunosVinculados = $objTurma->possuiAlunosVinculados();

        if($possuiAlunosVinculados)
          $this->script_excluir = "excluir_turma_com_matriculas();";

        $this->fexcluir = $obj_permissoes->permissao_excluir(
          586, $this->pessoa_logada, 7, 'educar_turma_lst.php'
        );

        $retorno = 'Editar';
      }
    }

    if (is_string($this->dias_semana)) {
      $this->dias_semana = explode(',',str_replace(array('{', "}"), '', $this->dias_semana));
    }

    if (is_string($this->atividades_complementares)) {
      $this->atividades_complementares = explode(',',str_replace(array('{', "}"), '', $this->atividades_complementares));
    }

    if (is_string($this->atividades_aee)) {
      $this->atividades_aee = explode(',',str_replace(array('{', "}"), '', $this->atividades_aee));
    }

    if (is_string($this->cod_curso_profissional)) {
      $this->cod_curso_profissional = explode(',',str_replace(array('{', "}"), '', $this->cod_curso_profissional));
    }

    $this->url_cancelar      = $retorno == 'Editar' ?
      'educar_turma_det.php?cod_turma=' . $registro['cod_turma'] : 'educar_turma_lst.php';

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""        => "{$nomeMenu} turma"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    $this->nome_url_cancelar = 'Cancelar';

    $this->retorno = $retorno;

    return $retorno;
  }

  function Gerar()
  {
    if ($_POST) {
      foreach ($_POST as $campo => $val) {
        $this->$campo = $this->$campo ? $this->$campo : $val;
      }
    }

    $obrigarCamposCenso = $this->validarCamposObrigatoriosCenso();

    if (is_numeric($this->ano_letivo)) $this->ano = $this->ano_letivo;

    $this->campoOculto('obrigar_campos_censo', (int) $obrigarCamposCenso);
    $this->campoOculto('cod_turma', $this->cod_turma);
    $this->campoOculto('dependencia_administrativa', $this->dependencia_administrativa);
    $this->campoOculto('modalidade_curso', $this->modalidade_curso);
    $this->campoOculto('retorno', $this->retorno);

    $bloqueia = false;
    $anoVisivel = false;
    if (! isset($this->ano) || isset($this->cod_turma) ){
      $anoVisivel=true;
    }
    if(! isset($this->cod_turma)){
      $bloqueia = false;
    }else{
      if (is_numeric($this->cod_turma)) {
        $obj_matriculas_turma = new clsPmieducarMatriculaTurma();
        $obj_matriculas_turma->setOrderby('nome_aluno');
        $lst_matriculas_turma = $obj_matriculas_turma->lista(NULL, $this->cod_turma,
         NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL,
         array(1, 2, 3), NULL, NULL, NULL, NULL, TRUE, NULL, 1, TRUE);

        if (is_array($lst_matriculas_turma) && count($lst_matriculas_turma)>0) {
            $bloqueia = true;
            $anoVisivel=false;
        }
      }
    }

    $desabilitado = $bloqueia;

    $this->inputsHelper()->dynamic('instituicao', array('value' => $this->ref_cod_instituicao, 'disabled' => $desabilitado));
    $this->inputsHelper()->dynamic('escola', array('value' => $this->ref_cod_escola, 'disabled' => $desabilitado));
    $this->inputsHelper()->dynamic('curso', array('value' => $this->ref_cod_curso, 'disabled' => $desabilitado));
    $this->inputsHelper()->dynamic('serie', array('value' => $this->ref_cod_serie, 'disabled' => $desabilitado));
    $this->inputsHelper()->dynamic('anoLetivo', array('value' => $this->ano, 'disabled' => $desabilitado));
    // Infra prédio cômodo
    $opcoes = array('' => 'Selecione');

    // Editar
    if ($this->ref_ref_cod_escola) {
      $obj_infra_predio = new clsPmieducarInfraPredio();
      $obj_infra_predio->setOrderby('nm_predio ASC');
      $lst_infra_predio = $obj_infra_predio->lista(NULL, NULL, NULL,
        $this->ref_ref_cod_escola, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

      if (is_array($lst_infra_predio) && count($lst_infra_predio)) {
        foreach ($lst_infra_predio as $predio) {
          $obj_infra_predio_comodo = new clsPmieducarInfraPredioComodo();
          $lst_infra_predio_comodo = $obj_infra_predio_comodo->lista(NULL, NULL,
            NULL, NULL, $predio['cod_infra_predio'], NULL, NULL, NULL, NULL, NULL,
            NULL, NULL, 1);

          if (is_array($lst_infra_predio_comodo) && count($lst_infra_predio_comodo)) {
            foreach ($lst_infra_predio_comodo as $comodo) {
              $opcoes[$comodo['cod_infra_predio_comodo']] = $comodo['nm_comodo'];
            }
          }
        }
      }
    }

    $this->campoLista('ref_cod_infra_predio_comodo', 'Sala', $opcoes,
      $this->ref_cod_infra_predio_comodo, NULL, NULL, NULL, NULL, NULL, FALSE);

    $array_servidor = array( '' => 'Selecione um servidor' );
    if ($this->ref_cod_regente) {
      $obj_pessoa = new clsPessoa_($this->ref_cod_regente);
      $det = $obj_pessoa->detalhe();
      $array_servidor[$this->ref_cod_regente] = $det['nome'];
    }

    $this->campoListaPesq('ref_cod_regente', 'Professor/Regente', $array_servidor, $this->ref_cod_regente, '', '', FALSE, '', '', NULL, NULL, '', TRUE, FALSE, FALSE);

    // Turma tipo
    $opcoes = array('' => 'Selecione');

    // Editar
    $objTemp = new clsPmieducarTurmaTipo();
    $objTemp->setOrderby('nm_tipo ASC');
    $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, 1, $this->ref_cod_instituicao);

    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $opcoes[$registro['cod_turma_tipo']] = $registro['nm_tipo'];
      }
    }

    $script = "javascript:showExpansivelIframe(520, 170, 'educar_turma_tipo_cad_pop.php');";

    if ($this->ref_cod_instituicao && $this->ref_cod_escola && $this->ref_cod_curso) {
      $script = sprintf("<div id='img_turma' border='0' onclick='%s'>",
                  $script);
    }
    else {
      $script = sprintf("<div id='img_turma' border='0' onclick='%s'>",
                  $script);
    }

    $this->campoLista('ref_cod_turma_tipo', 'Tipo de turma', $opcoes,
      $this->ref_cod_turma_tipo, '', FALSE, '', $script);

    $this->campoTexto('nm_turma', 'Nome da turma', $this->nm_turma, 30, 255, TRUE);

    $this->campoTexto('sgl_turma', _cl('turma.detalhe.sigla'), $this->sgl_turma, 15, 15, FALSE);

    $this->campoNumero('max_aluno', 'M&aacute;ximo de Alunos', $this->max_aluno, 3, 3, TRUE);

    unset($opcoes);
    if (!is_null($this->ref_cod_serie)){
        $anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
        $opcaoPadrao = array(null => 'Selecione');
        $listaComponentes = $anoEscolar->findComponentePorSerie($this->ref_cod_serie);
        if(!empty($listaComponentes)){
            foreach($listaComponentes as $componente){
                $componente->nome = ucwords(strtolower($componente->nome));
                $opcoes["{$componente->id}"] = "{$componente->nome}";
            }
        $opcoes = $opcaoPadrao + $opcoes;
        $this->campoLista('ref_cod_disciplina_dispensada', 'Disciplina dispensada', $opcoes, $this->ref_cod_disciplina_dispensada, '', FALSE, '', '', FALSE, FALSE);
        }
    }

    $ativo = isset($this->cod_turma) ? dbBool($this->visivel) : true;
    $this->campoCheck('visivel', 'Ativo', $ativo);

    $this->campoCheck('multiseriada', 'Multi-Seriada', $this->multiseriada, '',
      FALSE, FALSE);

    $this->campoLista('ref_cod_serie_mult','S&eacute;rie', array('' => 'Selecione'),
      '', '', FALSE, '', '', '', FALSE);

    $this->campoOculto('ref_cod_serie_mult_',$this->ref_ref_cod_serie_mult);

    $this->campoQuebra2();

    // hora
    if (!$this->obrigaCamposHorario()) {
        $this->hora_inicial = "";
        $this->hora_final = "";
        $this->hora_inicio_intervalo = "";
        $this->hora_fim_intervalo = "";
        $this->dias_semana = array();
    }
    $this->campoHora('hora_inicial', 'Hora inicial', $this->hora_inicial, FALSE, NULL, NULL, NULL);

    $this->campoHora('hora_final', 'Hora final', $this->hora_final, FALSE, NULL, NULL, NULL);

    $this->campoHora('hora_inicio_intervalo', 'Hora início intervalo',
      $this->hora_inicio_intervalo, FALSE, NULL, NULL, NULL);

    $this->campoHora( 'hora_fim_intervalo', 'Hora fim intervalo', $this->hora_fim_intervalo, FALSE, NULL, NULL, NULL);

    $helperOptions = array('objectName'  => 'dias_semana');
    $options       = array('label' => 'Dias da semana',
                            'size' => 50,
                            'required' => FALSE,
                            'disabled' => !$this->obrigaCamposHorario(),
                            'options' => array('values' => $this->dias_semana,
                                              'all_values' => array(1 => 'Domingo',
                                                                    2  => 'Segunda',
                                                                    3  => 'Terça',
                                                                    4  => 'Quarta',
                                                                    5  => 'Quinta',
                                                                    6  => 'Sexta',
                                                                    7  => 'Sábado')));
    $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

    $this->inputsHelper()->turmaTurno();

    // modelos boletim
    require_once 'Reports/Tipos/TipoBoletim.php';
    require_once 'Portabilis/Array/Utils.php';

    $tiposBoletim = Portabilis_Model_Report_TipoBoletim::getInstance()->getEnums();
    $tiposBoletim = Portabilis_Array_Utils::insertIn(null, "Selecione um modelo", $tiposBoletim);

    $this->campoLista('tipo_boletim', 'Modelo relat&oacute;rio boletim', $tiposBoletim, $this->tipo_boletim);
    $this->campoLista('tipo_boletim_diferenciado', 'Modelo relat&oacute;rio boletim diferenciado', $tiposBoletim, $this->tipo_boletim_diferenciado, '', FALSE, '', '', FALSE, FALSE);

    $this->montaListaComponentesSerieEscola();

    $objTemp = new clsPmieducarModulo();
    $objTemp->setOrderby('nm_tipo ASC');

    $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, 1, $ref_cod_instituicao);

    $opcoesCampoModulo = [];

    if (is_array($lista) && count($lista)) {
      $this->modulos = $lista;
      foreach ($lista as $registro) {
        $opcoesCampoModulo[$registro['cod_modulo']] = sprintf('%s - %d etapa(s)', $registro['nm_tipo'], $registro['num_etapas']);
      }
    }

    $registros = [];

    if (is_numeric($this->cod_turma)) {
      $objTurma = new clsPmieducarTurmaModulo();
      $objTurma->setOrderBy('sequencial ASC');

      $registros = $objTurma->lista($this->cod_turma);
    }

    if (
      empty($registros)
      && is_numeric($this->ano)
      && is_numeric($this->ref_cod_escola)
    ) {
      $objAno = new clsPmieducarAnoLetivoModulo();
      $objAno->setOrderBy('sequencial ASC');

      $registros = $objAno->lista($this->ano, $this->ref_cod_escola);
    }

    if ($this->padrao_ano_escolar != 1) {

      $qtd_registros = 0;
      $moduloSelecionado = 0;

      if( $registros )
      {
        $moduloSelecionado = $registros[0]['ref_cod_modulo'];

        foreach ( $registros AS $campo )
        {
          $this->turma_modulo[$qtd_registros][] = dataFromPgToBr($campo['data_inicio']);
          $this->turma_modulo[$qtd_registros][] = dataFromPgToBr($campo['data_fim']);
          $this->turma_modulo[$qtd_registros][] = $campo["dias_letivos"];
          $qtd_registros++;
        }
      }
    }

    $this->campoQuebra2();

    $this->campoRotulo('etapas_cabecalho', '<b>Etapas da turma</b>');

    $this->campoLista(
      'ref_cod_modulo',
      'Etapa',
      $opcoesCampoModulo,
      $moduloSelecionado,
      null,
      null,
      null,
      null,
      null,
      true
    );

    $this->campoTabelaInicio("turma_modulo", "Etapas", array("Data inicial", "Data final", "Dias Letivos"), $this->turma_modulo);

    $this->campoData('data_inicio', 'Data In&iacute;cio', $this->data_inicio, FALSE);
    $this->campoData('data_fim', 'Data Fim', $this->data_fim, FALSE);
    $this->campoTexto('dias_letivos', 'Dias Letivos', $this->dias_letivos_, 9);

    $this->campoTabelaFim();

    $this->campoOculto('padrao_ano_escolar', $this->padrao_ano_escolar);

    $this->acao_enviar = 'valida()';

    $this->inputsHelper()->integer('codigo_inep_educacenso', array('label' => 'Código INEP',
                                                                   'label_hint' => 'Somente números',
                                                                   'placeholder' => 'INEP',
                                                                   'required' => false,
                                                                   'max_length' => 14,
                                                                   'value' => $this->codigo_inep_educacenso));

    $resources = array( NULL => 'Selecione',
                        0 => Portabilis_String_Utils::toLatin1('Não se aplica'),
                        1 => 'Classe hospitalar',
                        2 => Portabilis_String_Utils::toLatin1('Unidade de internação socioeducativa'),
                        3 => 'Unidade prisional',
                        4 => 'Atividade complementar',
                        5 => 'Atendimento educacional especializado (AEE)');

    $options = array('label' => 'Tipo de atendimento', 'resources' => $resources, 'value' => $this->tipo_atendimento, 'required' => $obrigarCamposCenso, 'size' => 70,);
    $this->inputsHelper()->select('tipo_atendimento', $options);

    $atividadesComplementares = loadJson('educacenso_json/atividades_complementares.json');
    $helperOptions = array('objectName'  => 'atividades_complementares');
    $options       = array('label' => 'Tipos de atividades complementares',
                            'size' => 50,
                            'required' => false,
                            'options' => array('values' => $this->atividades_complementares,
                                              'all_values' => $atividadesComplementares));
    $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

    $helperOptions = array('objectName'  => 'atividades_aee');
    $options       = array('label' => 'Atividades do Atendimento Educacional Especializado - AEE',
                            'size' => 50,
                            'required' => false,
                            'options' => array('values' => $this->atividades_aee,
                                              'all_values' => array( 1 => 'Ensino do Sistema Braille',
                                                                     2 => 'Ensino de uso de recursos ópticos e não ópticos',
                                                                     3 => 'Estratégias para o desenvolvimento de processos mentais',
                                                                     4 => 'Técnica de orientações a mobilidade',
                                                                     5 => 'Ensino da Língua Brasileira de Sinais - LIBRAS',
                                                                     6 => 'Ensino de uso da Comunicação Alternativa e Aumentativa - CAA',
                                                                     7 => 'Estratégias para enriquecimento curricular',
                                                                     8 => 'Ensino do uso do Soroban',
                                                                     9 => 'Ensino da usabilidade e das funcionalidades de informática acessível',
                                                                    10 => 'Ensino da Língua Portuguesa na modalidade escrita',
                                                                    11 => 'Estratégias para autonomia no ambiente escolar')));
    $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

    $resources = Portabilis_Utils_Database::fetchPreparedQuery('SELECT id,nome FROM modules.etapas_educacenso');
    $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'id', 'nome');
    $resources = Portabilis_Array_Utils::merge($resources, array('null' => 'Selecione'));

    $etapas_educacenso = loadJson('educacenso_json/etapas_ensino.json');
    $etapas_educacenso = array_replace(array(null => 'Selecione'), $etapas_educacenso);

    $options = array('label' => 'Etapa de ensino', 'resources' => $etapas_educacenso, 'value' => $this->etapa_educacenso, 'required' => false, 'size' => 70,);
    $this->inputsHelper()->select('etapa_educacenso', $options);

    $cursos = loadJson('educacenso_json/cursos_da_educacao_profissional.json');
    $helperOptions = array('objectName'  => 'cod_curso_profissional',
                           'type' => 'single');
    $options       = array('label' => 'Curso técnico',
                            'size' => 50,
                            'required' => false,
                            'options' => array('values' => $this->cod_curso_profissional,
                                               'all_values' => $cursos));
    $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

    $resources = App_Model_TipoMediacaoDidaticoPedagogico::getInstance()->getEnums();

    $options = array('label' => 'Tipo de mediação didático pedagógico', 'resources' => $resources, 'value' => $this->tipo_mediacao_didatico_pedagogico, 'required' => false, 'size' => 70,);
    $this->inputsHelper()->select('tipo_mediacao_didatico_pedagogico', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Não informar esta turma no Censo escolar'),
                     'value' => $this->nao_informar_educacenso,
                     'label_hint' => Portabilis_String_Utils::toLatin1('Caso este campo seja selecionado, esta turma e todas as matrículas vinculadas a mesma, não serão informadas no arquivo de exportação do Censo escolar'));
    $this->inputsHelper()->checkbox('nao_informar_educacenso', $options);


    $options = array(
        'label' => 'Turma participante do programa Mais Educação/Ensino Médio Inovador',
        'resources' => $resources,
        'value' => $this->turma_mais_educacao,
        'required' => false,
        'prompt' => 'Selecione'
    );
    $this->inputsHelper()->booleanSelect('turma_mais_educacao', $options);

    $scripts = array(
      '/modules/Cadastro/Assets/Javascripts/Turma.js',
      '/intranet/scripts/etapas.js'
    );

    Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

    $styles = array ('/modules/Cadastro/Assets/Stylesheets/Turma.css');

    Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
  }

    protected function obrigaCamposHorario()
    {
        return $this->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL;

    }

  function montaListaComponentesSerieEscola(){
    $this->campoQuebra2();

    if ($this->ref_cod_serie) {

      $disciplinas = '';
      $conteudo    = '';

      try {
        $lista = App_Model_IedFinder::getEscolaSerieDisciplina(
            $this->ref_cod_serie, $this->ref_cod_escola, null, null, null, true, $this->ano
        );
      }  catch (App_Model_Exception $e) {
          $this->mensagem = $e->getMessage();
          return;
      }

      // Instancia o mapper de turma
      $componenteTurmaMapper = new ComponenteCurricular_Model_TurmaDataMapper();
      $componentesTurma = array();

      if (isset($this->cod_turma) && is_numeric($this->cod_turma)) {
        $componentesTurma = $componenteTurmaMapper->findAll(
          array(), array('turma' => $this->cod_turma)
        );
      }

      $componentes = array();
      foreach ($componentesTurma as $componenteTurma) {
        $componentes[$componenteTurma->get('componenteCurricular')] = $componenteTurma;
      }
      unset($componentesTurma);

      $instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
      $instituicao = $instituicao->detalhe();

      $podeCadastrarComponenteDiferenciado = dbBool($instituicao['componente_curricular_turma']);

      if ($podeCadastrarComponenteDiferenciado) {
        $checkDefinirComponente = ($componentes == true);
        $disableDefinirComponente = false;
      } else {
        $disableDefinirComponente = true;

      }

      $this->campoCheck('definir_componentes_diferenciados',
        'Definir componentes curriculares diferenciados',
        $checkDefinirComponente,
        NULL,
        FALSE,
        FALSE,
        $disableDefinirComponente,
        Portabilis_String_Utils::toLatin1('Está opção poderá ser utilizada, somente se no cadastro da instituição o parâmetro de permissão estiver habilitado'));

      $this->escola_serie_disciplina = array();

      if (is_array($lista) && count($lista)) {
        $conteudo .= '<div style="margin-bottom: 10px;">';
        $conteudo .= '  <span style="display: block; float: left; width: 250px;">Nome</span>';
        $conteudo .= '  <span style="display: block; float: left; width: 100px;">Nome abreviado</span>';
        $conteudo .= '  <span style="display: block; float: left; width: 100px;">Carga hor&aacute;ria</span>';
        $conteudo .= '  <span style="display: block; float: left;width: 100px;">Usar padr&atilde;o do componente?</span>';
        if($this->definirComponentePorEtapa){
          $conteudo .= '  <span style="display: block; float: left;width: 150px;">Usar etapas espec&iacute;ficas?</span>';
        }
        $conteudo .= '  <span style="display: block; float: left">Possui docente v&iacute;nculado?</span>';
        $conteudo .= '</div>';
        $conteudo .= '<br style="clear: left" />';

        foreach ($lista as $registro) {
          $checked = '';
          $usarComponente = FALSE;
          $docenteVinculado = FALSE;
          $checkedEtapaEspecifica = '';
          $etapaUtilizada = '';

          if($componentes[$registro->id]->etapasEspecificas == "1"){
            $checkedEtapaEspecifica = 'checked="checked"';
            $etapaUtilizada = $componentes[$registro->id]->etapasUtilizadas;
          }

          if (isset($componentes[$registro->id])) {
            $checked = 'checked="checked"';
          }

          if (is_null($componentes[$registro->id]->cargaHoraria) ||
            0 == $componentes[$registro->id]->cargaHoraria) {
            $usarComponente = TRUE;
          }
          else {
            $cargaHoraria = $componentes[$registro->id]->cargaHoraria;
          }
          $cargaComponente = $registro->cargaHoraria;

          if (1 == $componentes[$registro->id]->docenteVinculado) {
            $docenteVinculado = TRUE;
          }

          $conteudo .= '<div style="margin-bottom: 10px; float: left" class="linha-disciplina" >';
          $conteudo .= "  <label style='display: block; float: left; width: 250px'><input type=\"checkbox\" $checked name=\"disciplinas[$registro->id]\" class='check-disciplina' id=\"disciplinas[]\" value=\"{$registro->id}\">{$registro}</label>";
          $conteudo .= "  <span style='display: block; float: left; width: 100px'>{$registro->abreviatura}</span>";
          $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='text' name='carga_horaria[$registro->id]' value='{$cargaHoraria}' size='5' maxlength='7'></label>";
          $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='checkbox' name='usar_componente[$registro->id]' value='1' ". ($usarComponente == TRUE ? $checked : '') .">($cargaComponente h)</label>";
          if($this->definirComponentePorEtapa){
            $conteudo .= "  <input style='float:left;' type='checkbox' id='etapas_especificas[]' name='etapas_especificas[$registro->id]' value='1' ". $checkedEtapaEspecifica ."></label>";
            $conteudo .= "  <label style='display: block; float: left; width: 150px;'>Etapas utilizadas: <input type='text' class='etapas_utilizadas' name='etapas_utilizadas[$registro->id]' value='{$etapaUtilizada}' size='5' maxlength='7'></label>";
          }
          $conteudo .= "  <label style='display: block; float: left'><input type='checkbox' name='docente_vinculado[$registro->id]' value='1' ". ($docenteVinculado == TRUE ? $checked : '') ."></label>";
          $conteudo .= '</div>';
          $conteudo .= '<br style="clear: left" />';

          $cargaHoraria = '';
        }

        $disciplinas  = '<table cellspacing="0" cellpadding="0" border="0">';
        $disciplinas .= sprintf('<tr align="left"><td>%s</td></tr>', $conteudo);
        $disciplinas .= '</table>';
      }
      else {
        $disciplinas = 'A s&eacute;rie/ano escolar n&atilde;o possui componentes curriculares cadastrados.';
      }
    }

    $help = [];

    $label = 'Componentes curriculares definidos em s&eacute;ries da escola';

    $label = sprintf($label, $help);

    $this->campoRotulo('disciplinas_', $label,
      "<div id='disciplinas'>$disciplinas</div>");

  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    if (!$this->canCreateTurma($this->ref_cod_escola, $this->ref_cod_serie, $this->turma_turno_id)) {
      return false;
    }

    if (!$this->verificaModulos()) {
      return false;
    }

    if (!$this->verificaCamposCenso()) {
      return FALSE;
    }

    $this->ref_cod_instituicao_regente = $this->ref_cod_instituicao;

    $this->multiseriada = isset($this->multiseriada) ? 1 : 0;
    $this->visivel = isset($this->visivel);

    $objTurma = $this->montaObjetoTurma(null, $this->pessoa_logada);
    $this->cod_turma = $cadastrou = $objTurma->cadastra();

    if (!$cadastrou) {

      $this->mensagem = 'Cadastro não realizado.';
      echo "<!--\nErro ao cadastrar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";

      return false;
    }

    $turma = new clsPmieducarTurma($this->cod_turma);
    $turma = $turma->detalhe();
    $auditoria = new clsModulesAuditoriaGeral("turma", $this->pessoa_logada, $this->cod_turma);
    $auditoria->inclusao($turma);

    $this->atualizaComponentesCurriculares(
        $this->ref_cod_serie,
        $this->ref_cod_escola,
        $this->cod_turma,
        $this->disciplinas,
        $this->carga_horaria,
        $this->usar_componente,
        $this->docente_vinculado
    );

    $this->cadastraInepTurma($this->cod_turma, $this->codigo_inep_educacenso);

    $this->atualizaModulos();

    $this->mensagem .= 'Cadastro efetuado com sucesso.';
    header('Location: educar_turma_lst.php');
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    if (!$this->verificaModulos()) {
      return false;
    }

    if (!$this->verificaCamposCenso()) {
      return FALSE;
    }

    $turmaDetalhe = new clsPmieducarTurma($this->cod_turma);
    $turmaDetalhe = $turmaDetalhe->detalhe();

    if (is_null($this->ref_cod_instituicao)) {
      $this->ref_cod_instituicao = $turmaDetalhe["ref_cod_instituicao"];
      $this->ref_cod_instituicao_regente = $turmaDetalhe["ref_cod_instituicao"];
    } else {
      $this->ref_cod_instituicao_regente = $this->ref_cod_instituicao;
    }

    $this->multiseriada = isset($this->multiseriada) ? 1 : 0;
    $this->visivel = isset($this->visivel);

    $objTurma = $this->montaObjetoTurma($this->cod_turma, null, $this->pessoa_logada);
    $editou = $objTurma->edita();

    // Caso tenham sido selecionadas discplinas, como se trata de uma edição de turma será rodado uma consulta
    // que limpa os Componentes Curriculares antigos.
    if ($this->disciplinas != 1) {
      $anoLetivo = $this->ano ? $this->ano : date("Y");
      CleanComponentesCurriculares::destroyOldResources($anoLetivo);
    }

    if (!$editou) {
        $this->mensagem = 'Edição não realizada.';
        echo "<!--\nErro ao editar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";

        return false;
    }

    $auditoria = new clsModulesAuditoriaGeral("turma", $this->pessoa_logada, $this->cod_turma);
    $auditoria->alteracao($turmaDetalhe, $objTurma->detalhe());

    $this->atualizaComponentesCurriculares(
        $turmaDetalhe['ref_ref_cod_serie'],
        $turmaDetalhe['ref_ref_cod_escola'],
        $this->cod_turma,
        $this->disciplinas,
        $this->carga_horaria,
        $this->usar_componente,
        $this->docente_vinculado
    );

    $this->cadastraInepTurma($this->cod_turma, $this->codigo_inep_educacenso);

    $this->atualizaModulos();

    $this->mensagem .= 'Edição efetuada com sucesso.';
    header('Location: educar_turma_lst.php');
    die();
  }

  protected function validaCamposHorario()
  {
    if (!$this->obrigaCamposHorario()) {
        return TRUE;
    }
    if (empty($this->hora_inicial)) {
        $this->mensagem = "O campo hora inicial é obrigatório";
        return FALSE;
    }
    if (empty($this->hora_final)) {
        $this->mensagem = "O campo hora final é obrigatório";
        return FALSE;
    }
    if (empty($this->hora_inicio_intervalo)) {
        $this->mensagem = "O campo hora início intervalo é obrigatório";
        return FALSE;
    }
    if (empty($this->hora_fim_intervalo)) {
        $this->mensagem = "O campo hora fim intervalo é obrigatório";
        return FALSE;
    }
    if (empty($this->dias_semana)) {
        $this->mensagem = "O campo dias da semana é obrigatório";
        return FALSE;
    }
    return TRUE;

  }

  protected function validaCampoAtividadesComplementares()
  {
    if ($this->tipo_atendimento == 4 && empty($this->atividades_complementares)) {
        $this->mensagem = "Campo atividades complementares é obrigatório";
        return FALSE;
    }
    return TRUE;
  }

  protected function validaCampoAEE()
  {
    if ($this->tipo_atendimento == 5 && empty($this->atividades_aee)) {
        $this->mensagem = "Campo atividades do Atendimento Educacional Especializado - AEE é obrigatório";
        return FALSE;
    }
    return TRUE;
  }

  protected function validaCampoEtapaEnsino()
  {
    if (!empty($this->tipo_atendimento) &&
            $this->tipo_atendimento != -1 &&
            $this->tipo_atendimento != 4 &&
            $this->tipo_atendimento != 5) {
        $this->mensagem = "Campo etapa de ensino é obrigatório";
        return FALSE;
    }
    return TRUE;
  }

  protected function verificaCamposCenso()
  {
    if (!$this->validarCamposObrigatoriosCenso()) {
        return TRUE;
    }
    if (!$this->validaCamposHorario()) {
        return FALSE;
    }
    if (!$this->validaCampoAtividadesComplementares()) {
        return FALSE;
    }
    if (!$this->validaCampoAEE()) {
        return FALSE;
    }
    if (!$this->validaCampoEtapaEnsino()) {
        return FALSE;
    }
    return TRUE;
  }


  function montaObjetoTurma($codTurma = null, $usuarioCad = null, $usuarioExc = null)
  {
      $this->dias_semana = '{' . implode(',', $this->dias_semana) . '}';
      $this->atividades_complementares = '{' . implode(',', $this->atividades_complementares) . '}';
      $this->atividades_aee = '{' . implode(',', $this->atividades_aee) . '}';
      $this->cod_curso_profissional = $this->cod_curso_profissional[0];

      if ($this->tipo_atendimento != 4) {
        $this->atividades_complementares = '{}';
      }

      if ($this->tipo_atendimento != 5) {
        $this->atividades_aee = '{}';
      }

      $etapasCursoTecnico = array(30, 31, 32, 33, 34, 39, 40, 64, 74);

      if (!in_array($this->etapa_educacenso, $etapasCursoTecnico)) {
        $this->cod_curso_profissional = NULL;
      }

      $objTurma = new clsPmieducarTurma($codTurma);
      $objTurma->ref_usuario_cad = $usuarioCad;
      $objTurma->ref_usuario_exc = $usuarioExc;
      $objTurma->ref_ref_cod_serie = $this->ref_cod_serie;
      $objTurma->ref_ref_cod_escola = $this->ref_cod_escola;
      $objTurma->ref_cod_infra_predio_comodo = $this->ref_cod_infra_predio_comodo;
      $objTurma->nm_turma = $this->nm_turma;
      $objTurma->sgl_turma = $this->sgl_turma;
      $objTurma->max_aluno = $this->max_aluno;
      $objTurma->multiseriada = $this->multiseriada;
      $objTurma->ativo = 1;
      $objTurma->ref_cod_turma_tipo = $this->ref_cod_turma_tipo;
      $objTurma->hora_inicial = $this->hora_inicial;
      $objTurma->hora_final = $this->hora_final;
      $objTurma->hora_inicio_intervalo = $this->hora_inicio_intervalo;
      $objTurma->hora_fim_intervalo = $this->hora_fim_intervalo;
      $objTurma->ref_cod_regente = $this->ref_cod_regente;
      $objTurma->ref_cod_instituicao_regente = $this->ref_cod_instituicao_regente;
      $objTurma->ref_cod_instituicao = $this->ref_cod_instituicao;
      $objTurma->ref_cod_curso = $this->ref_cod_curso;
      $objTurma->ref_ref_cod_serie_mult = $this->ref_cod_serie_mult;
      $objTurma->ref_ref_cod_escola_mult = $this->ref_cod_escola;
      $objTurma->visivel = $this->visivel;
      $objTurma->turma_turno_id = $this->turma_turno_id;
      $objTurma->tipo_boletim = $this->tipo_boletim;
      $objTurma->tipo_boletim_diferenciado = $this->tipo_boletim_diferenciado;
      $objTurma->ano = $this->ano_letivo;
      $objTurma->tipo_atendimento = $this->tipo_atendimento;
      $objTurma->turma_mais_educacao = $this->turma_mais_educacao;
      $objTurma->cod_curso_profissional = $this->cod_curso_profissional;
      $objTurma->etapa_educacenso = $this->etapa_educacenso == "" ? NULL : $this->etapa_educacenso;
      $objTurma->ref_ref_cod_serie_mult = $this->ref_cod_serie_mult == "" ? NULL : $this->ref_cod_serie_mult;
      $objTurma->ref_cod_disciplina_dispensada = $this->ref_cod_disciplina_dispensada == "" ? NULL : $this->ref_cod_disciplina_dispensada;
      $objTurma->nao_informar_educacenso = $this->nao_informar_educacenso == 'on' ? 1 : 0;
      $objTurma->tipo_mediacao_didatico_pedagogico = $this->tipo_mediacao_didatico_pedagogico;
      $objTurma->dias_semana = $this->dias_semana;
      $objTurma->atividades_complementares = $this->atividades_complementares;
      $objTurma->atividades_aee = $this->atividades_aee;

      return $objTurma;
  }

  function atualizaModulos()
  {
      $objModulo = new clsPmieducarTurmaModulo();
      $excluiu = $objModulo->excluirTodos($this->cod_turma);
      $modulos = $this->montaModulos();

      if (!$excluiu){
          $this->mensagem = 'Edição não realizada.';
          return false;
      }

      foreach ($modulos as $modulo) {
          $this->cadastraModulo($modulo);
      }

      return true;
  }

  function montaModulos()
  {
      // itera pelo campo `data_inicio`, um dos campos referentes às etapas,
      // para definir sequencialmente os dados de cada etapa
      foreach ($this->data_inicio as $key => $modulo) {
          $turmaModulo[$key]['sequencial'] = $key + 1;
          $turmaModulo[$key]['ref_cod_modulo'] = $this->ref_cod_modulo;
          $turmaModulo[$key]['data_inicio'] = $this->data_inicio[$key];
          $turmaModulo[$key]['data_fim'] = $this->data_fim[$key];
          $turmaModulo[$key]['dias_letivos'] = $this->dias_letivos[$key];
      }

      return $turmaModulo;
  }

  function cadastraModulo($modulo)
  {
      $modulo['data_inicio'] = dataToBanco($modulo['data_inicio']);
      $modulo['data_fim'] = dataToBanco($modulo['data_fim']);

      $objModulo = new clsPmieducarTurmaModulo($this->cod_turma);
      $objModulo->ref_cod_modulo = $modulo['ref_cod_modulo'];
      $objModulo->sequencial = $modulo['sequencial'];
      $objModulo->data_inicio = $modulo['data_inicio'];
      $objModulo->data_fim = $modulo['data_fim'];
      $objModulo->dias_letivos = $modulo['dias_letivos'];

      $cadastrou = $objModulo->cadastra();

      if (!$cadastrou) {
          echo "<!--\nErro ao editar clsPmieducarTurmaModulo\nvalores obrigatorios\nis_numeric( $this->cod_turma ) && is_numeric( {$modulo["ref_cod_modulo_"]} ) \n-->";
      }

      return true;
  }

  function verificaModulos()
  {
      $cursoPadraoAnoEscolar = $this->padrao_ano_escolar == 1;
      $possuiModulosInformados = (count($this->data_inicio) > 1 || $this->data_inicio[0] != '');

      if ($cursoPadraoAnoEscolar) {
          return true;
      }

      if (!$possuiModulosInformados) {
          $this->mensagem = 'Edição não realizada.';
          return false;
      }

      return true;
  }

  function atualizaComponentesCurriculares($codSerie, $codEscola, $codTurma, $componentes, $cargaHoraria, $usarComponente, $docente)
  {
    require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';
    $mapper = new ComponenteCurricular_Model_TurmaDataMapper();

    $componentesTurma = array();

    foreach ($componentes as $key => $value) {
      $carga = isset($usarComponente[$key]) ?
        NULL : $cargaHoraria[$key];

      $docente_ = isset($docente[$key]) ?
        1 : 0;

      $etapasEspecificas = isset($this->etapas_especificas[$key]) ?
        1 : 0;

      $etapasUtilizadas = ($etapasEspecificas == 1) ? $this->etapas_utilizadas[$key] : NULL;

      $componentesTurma[] = array(
        'id'           => $value,
        'cargaHoraria' => $carga,
        'docenteVinculado' => $docente_,
        'etapasEspecificas' => $etapasEspecificas,
        'etapasUtilizadas' => $etapasUtilizadas
      );
    }

    $mapper->bulkUpdate($codSerie, $codEscola, $codTurma, $componentesTurma);
  }

  function cadastraInepTurma($cod_turma, $codigo_inep_educacenso) {
    $turma = new clsPmieducarTurma($cod_turma);
    $turma->updateInep($codigo_inep_educacenso);
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj = new clsPmieducarTurma($this->cod_turma, $this->pessoa_logada, null,
      null, null, null, null, null, null, null, null, null, 0);

    if ($obj->possuiAlunosVinculados()) {
      $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.';

      return false;
    }

    $turma = $obj->detalhe();

    $excluiu = $obj->excluir();

    if ($excluiu) {
      $obj      = new clsPmieducarTurmaModulo();
      $excluiu1 = $obj->excluirTodos($this->cod_turma);

      if ($excluiu1) {
        $auditoria = new clsModulesAuditoriaGeral("turma", $this->pessoa_logada, $this->cod_turma);
        $auditoria->exclusao($turma);

        $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.';
        header('Location: educar_turma_lst.php');
        die();
      }
      else
      {
        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.';
        echo "<!--\nErro ao excluir clsPmieducarTurma\nvalores obrigatorios\nif( is_numeric( $this->cod_turma ) && is_numeric( $this->pessoa_logada ) )\n-->";

        return FALSE;
      }
    }

    $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.';
    echo "<!--\nErro ao excluir clsPmieducarTurma\nvalores obrigatorios\nif( is_numeric( $this->cod_turma ) && is_numeric( $this->pessoa_logada ) )\n-->";

    return FALSE;
  }


  protected function getDb() {
    if (! isset($this->db))
      $this->db = new clsBanco();

    return $this->db;
  }

  protected function getEscolaSerie($escolaId, $serieId) {
    $escolaSerie = new clsPmieducarEscolaSerie();
    $escolaSerie->ref_cod_escola = $escolaId;
    $escolaSerie->ref_cod_serie  = $serieId;

    return $escolaSerie->detalhe();
  }


  protected function getAnoEscolarEmAndamento($escolaId) {
    return $this->getDb()->CampoUnico("select ano from pmieducar.escola_ano_letivo where ativo = 1 and andamento = 1 and ref_cod_escola = $escolaId");
  }


  protected function getCountMatriculas($escolaId, $turmaId) {
    $ano = $this->getAnoEscolarEmAndamento($escolaId);

    if (! is_numeric($ano)) {
      $this->mensagem = "N&atilde;o foi possivel obter um ano em andamento, por favor, inicie um ano para a escola ou desative a configura&ccedil;&atilde;o (para s&eacute;rie e escola) 'Bloquear cadastro de novas turmas antes de atingir limite de vagas (no mesmo turno)'.";

      return false;
    }

    $sql = "select count(cod_matricula) as matriculas from pmieducar.matricula, pmieducar.matricula_turma where ano = $ano and matricula.ativo = 1 and matricula_turma.ativo = matricula.ativo and cod_matricula = ref_cod_matricula and ref_cod_turma = $turmaId";

    return $this->getDb()->CampoUnico($sql);
  }


  protected function canCreateTurma($escolaId, $serieId, $turnoId) {
    $escolaSerie = $this->getEscolaSerie($escolaId, $serieId);

    if($escolaSerie['bloquear_cadastro_turma_para_serie_com_vagas'] == 1) {
      $turmas = new clsPmieducarTurma();

      $turmas = $turmas->lista(null, null, null, $serieId, $escolaId, null, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, true, $turnoId, null, null, true);

      foreach($turmas as $turma) {
        $countMatriculas = $this->getCountMatriculas($escolaId, $turma['cod_turma']);

        // countMatriculas retorna false e adiciona mensagem, se não obter ano em andamento
        if ($countMatriculas === false)
          return false;

        elseif($turma['max_aluno'] - $countMatriculas > 0) {
          $vagas = $turma['max_aluno'] - $countMatriculas;
          $this->mensagem = "N&atilde;o &eacute; possivel cadastrar turmas, pois ainda existem $vagas vagas em aberto na turma '{$turma['nm_turma']}' desta serie e turno.\n\nTal limita&ccedil;&atilde;o ocorre devido defini&ccedil;&atilde;o feita para esta escola e s&eacute;rie.";
          return false;
        }
      }
    }

    return true;
  }

  public function gerarJsonDosModulos()
  {
    $retorno = [];

    foreach ($this->modulos as $modulo) {
      $retorno[$modulo['cod_modulo']] = [
        'label' => $modulo['nm_tipo'],
        'etapas' => (int)$modulo['num_etapas']
      ];
    }

    return json_encode($retorno);
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type='text/javascript'>
var modulosDisponiveis = <?php echo $miolo->gerarJsonDosModulos(); ?>;

function getComodo()
{
  var campoEscola      = document.getElementById('ref_cod_escola').value;
  var campoComodo      = document.getElementById('ref_cod_infra_predio_comodo');
  campoComodo.disabled = true;

  campoComodo.length = 1;
  campoComodo.options[0] = new Option('Selecione uma sala', '', false, false);

  var xml1 = new ajax(atualizaTurmaCad_TipoComodo);
  strURL   = 'educar_escola_comodo_xml.php?esc=' + campoEscola;
  xml1.envia(strURL);
}

function atualizaTurmaCad_TipoComodo(xml)
{
  var campoComodo      = document.getElementById('ref_cod_infra_predio_comodo');
  campoComodo.disabled = false;

  var tipo_comodo = xml.getElementsByTagName('item');

  if (tipo_comodo.length) {
    for (var i = 0; i < tipo_comodo.length; i += 2) {
      campoComodo.options[campoComodo.options.length] = new Option(
        tipo_comodo[i + 1].firstChild.data, tipo_comodo[i].firstChild.data, false, false
      );
    }
  }
  else {
    campoComodo.length = 1;
    campoComodo.options[0] = new Option('A escola n\u00e3o possui nenhuma sala', '', false, false);
  }
}

function getTipoTurma()
{
  var campoInstituicao    = document.getElementById('ref_cod_instituicao').value;
  var campoTipoTurma      = document.getElementById('ref_cod_turma_tipo');
  campoTipoTurma.disabled = true;

  campoTipoTurma.length = 1;
  campoTipoTurma.options[0] = new Option('Selecione um tipo de turma', '', false, false);

  var xml1 = new ajax(atualizaTurmaCad_TipoTurma);
  strURL = 'educar_tipo_turma_xml.php?ins=' + campoInstituicao;
  xml1.envia(strURL);
}

function atualizaTurmaCad_TipoTurma(xml)
{
  var tipo_turma          = xml.getElementsByTagName('item');
  var campoTipoTurma      = document.getElementById('ref_cod_turma_tipo');
  campoTipoTurma.disabled = false;

  if (tipo_turma.length) {
    for (var i = 0; i < tipo_turma.length; i += 2) {
      campoTipoTurma.options[campoTipoTurma.options.length] = new Option(
        tipo_turma[i + 1].firstChild.data, tipo_turma[i].firstChild.data, false, false
      );
    }
  }
  else {
    campoTipoTurma.length     = 1;
    campoTipoTurma.options[0] = new Option(
      'A institui\u00e7\u00e3o n\u00e3o possui nenhum tipo de turma', '', false, false
    );
  }
}

function getModulo()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var campoEscola      = document.getElementById('ref_cod_instituicao').value;
  var campoModulo      = document.getElementById('ref_cod_modulo');

  var url  = 'educar_modulo_instituicao_xml.php';
  var pars = '?inst=' + campoInstituicao;

  var xml1 = new ajax(getModulo_xml);
  strURL = url + pars;
  xml1.envia(strURL);
}

function getModulo_xml(xml)
{
  var campoModulo      = document.getElementById('ref_cod_modulo');
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

  campoModulo.length     = 1;
  campoModulo.options[0] = new Option('Selecione um m\u00f3dulo', '', false, false);

  var DOM_modulos = xml.getElementsByTagName('item');

  for (var j = 0; j < DOM_modulos.length; j += 2) {
    campoModulo.options[campoModulo.options.length] = new Option(
      DOM_modulos[j + 1].firstChild.nodeValue, DOM_modulos[j].firstChild.nodeValue,
      false, false
    );
  }

  if (campoModulo.length == 1 && campoInstituicao != '') {
    campoModulo.options[0] = new Option(
      'A institui\u00e7\u00e3o n\u00e3o possui nenhum m\u00f3dulo', '', false, false
    );
  }
}

var evtOnLoad = function()
{
  setVisibility('tr_hora_inicial',false);
  setVisibility('tr_hora_final',false);
  setVisibility('tr_hora_inicio_intervalo',false);
  setVisibility('tr_hora_fim_intervalo',false);

  if (!document.getElementById('ref_cod_serie').value) {
    setVisibility('tr_multiseriada',false);
    setVisibility('tr_ref_cod_serie_mult', document.getElementById('multiseriada').checked ? true : false);
    setVisibility('ref_cod_serie_mult', document.getElementById('multiseriada').checked ? true : false);
  }
  else {
    if(document.getElementById('multiseriada').checked){
      changeMultiSerie();
      document.getElementById('ref_cod_serie_mult').value =
        document.getElementById('ref_cod_serie_mult_').value;
    }
    else {
      setVisibility('tr_ref_cod_serie_mult', document.getElementById('multiseriada').checked ? true : false);
      setVisibility('ref_cod_serie_mult', document.getElementById('multiseriada').checked ? true : false);
    }
  }

  // HIDE quebra de linha
  var hr_tag = document.getElementsByTagName('hr');

  for (var ct = 0; ct < hr_tag.length; ct++) {
    setVisibility(hr_tag[ct].parentNode.parentNode, false);
  }

  setVisibility('tr_hora_inicial', true);
  setVisibility('tr_hora_final', true);
  setVisibility('tr_hora_inicio_intervalo', true);
  setVisibility('tr_hora_fim_intervalo', true);
  if (document.getElementById('padrao_ano_escolar').value == '') {
    setModuleAndPhasesVisibility(false);
  }else if (document.getElementById('padrao_ano_escolar').value == 0) {
    setModuleAndPhasesVisibility(true);

    var hr_tag = document.getElementsByTagName('hr');
    for (var ct = 0;ct < hr_tag.length; ct++) {
      setVisibility(hr_tag[ct].parentNode.parentNode, true);
    }
  }else {
    setModuleAndPhasesVisibility(false);
  }
}

if (window.addEventListener) {
  // Mozilla
  window.addEventListener('load', evtOnLoad, false);
}
else if (window.attachEvent) {
  // IE
  window.attachEvent('onload', evtOnLoad);
}

document.getElementById('ref_cod_curso').onchange = function()
{
  setVisibility('tr_multiseriada', document.getElementById('ref_cod_serie').value ? true : false);
  setVisibility('tr_ref_cod_serie_mult', document.getElementById('multiseriada').checked ? true : false);
  setVisibility('ref_cod_serie_mult', document.getElementById('multiseriada').checked ? true : false);

  hideMultiSerie();
  getEscolaCursoSerie();

  PadraoAnoEscolar_xml();
}

function PadraoAnoEscolar_xml()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var xml1 = new ajax(PadraoAnoEscolar);
  strURL   = 'educar_curso_xml.php?ins=' + campoInstituicao;
  xml1.envia(strURL);
}

function changeMultiSerie()
{
  var campoCurso = document.getElementById('ref_cod_curso').value;
  var campoSerie = document.getElementById('ref_cod_serie').value;

  var xml1 = new ajax(atualizaMultiSerie);
  strURL   = 'educar_sequencia_serie_xml.php?cur=' + campoCurso + '&ser_dif=' + campoSerie;

  xml1.envia(strURL);
}

function atualizaMultiSerie(xml)
{
  var campoMultiSeriada = document.getElementById('multiseriada');
  var checked = campoMultiSeriada.checked;

  var multiBool = (document.getElementById('multiseriada').checked == true &&
                   document.getElementById('ref_cod_serie').value != '') ? true : false;

  setVisibility('tr_ref_cod_serie_mult', multiBool);
  setVisibility('ref_cod_serie_mult', multiBool);

  if (!checked){
    document.getElementById('ref_cod_serie_mult').value = '';
    return;
  }

  var campoEscola     = document.getElementById('ref_cod_escola').value;
  var campoCurso      = document.getElementById('ref_cod_curso').value;
  var campoSerieMult  = document.getElementById('ref_cod_serie_mult');
  var campoSerie      = document.getElementById('ref_cod_serie');

  campoSerieMult.length = 1;
  campoSerieMult.options[0] = new Option('Selecione uma s\u00e9rie', '', false, false);

  var multi_serie = xml.getElementsByTagName('serie');

  if (multi_serie.length) {
    for (var i = 0; i < multi_serie.length; i++) {
      campoSerieMult.options[campoSerieMult.options.length] = new Option(
        multi_serie[i].firstChild.data, multi_serie[i].getAttribute('cod_serie'), false, false
      );
    }
  }

  if (campoSerieMult.length == 1 && campoCurso != '') {
    campoSerieMult.options[0] = new Option('O curso n\u00e3o possui nenhuma s\u00e9rie', '', false, false);
  }

  document.getElementById('ref_cod_serie_mult').value = document.getElementById('ref_cod_serie_mult_').value;
}

document.getElementById('multiseriada').onclick = function()
{
  changeMultiSerie();
}

document.getElementById('ref_cod_serie').onchange = function()
{
  if (this.value) {
    codEscola = document.getElementById('ref_cod_escola').value;
    getHoraEscolaSerie();
  }

  if (document.getElementById('multiseriada').checked == true) {
    changeMultiSerie();
  }

  hideMultiSerie();
}

function hideMultiSerie()
{
  setVisibility('tr_multiseriada', document.getElementById('ref_cod_serie').value != '' ? true : false);

  var multiBool = (document.getElementById('multiseriada').checked == true &&
                   document.getElementById('ref_cod_serie').value != '')  ? true : false;

  setVisibility('ref_cod_serie_mult', multiBool);
  setVisibility('tr_ref_cod_serie_mult',multiBool);
}

function PadraoAnoEscolar(xml)
{
  var escola_curso_ = new Array();

  if (xml != null) {
    escola_curso_ = xml.getElementsByTagName('curso');
  }

  campoCurso = document.getElementById('ref_cod_curso').value;

  for (var j = 0; j < escola_curso_.length; j++) {
    if (escola_curso_[j].getAttribute('cod_curso') == campoCurso) {
      document.getElementById('padrao_ano_escolar').value =
        escola_curso_[j].getAttribute('padrao_ano_escolar') ;
    }
  }

  setModuleAndPhasesVisibility(false);

  setVisibility('tr_hora_inicial', true);
  setVisibility('tr_hora_final', true);
  setVisibility('tr_hora_inicio_intervalo', true);
  setVisibility('tr_hora_fim_intervalo', true);

  if (campoCurso == '') {
    return;
  }

  var campoCurso = document.getElementById('ref_cod_curso').value;

  if (document.getElementById('padrao_ano_escolar').value == 0) {
    setModuleAndPhasesVisibility(true);
  }
}

function setModuleAndPhasesVisibility(show)
{
  setVisibility('tr_etapas_cabecalho', show);
  setVisibility('tr_ref_cod_modulo', show);
  setVisibility('tr_turma_modulo', show);
}

function getHoraEscolaSerie()
{
  var campoEscola = document.getElementById('ref_cod_escola').value;
  var campoSerie  = document.getElementById('ref_cod_serie').value;

  var xml1 = new ajax(atualizaTurmaCad_EscolaSerie);
  strURL   = 'educar_escola_serie_hora_xml.php?esc=' + campoEscola + '&ser=' +campoSerie;
  xml1.envia(strURL);
}

function atualizaTurmaCad_EscolaSerie(xml)
{
  var campoHoraInicial         = document.getElementById('hora_inicial');
  var campoHoraFinal           = document.getElementById('hora_final');
  var campoHoraInicioIntervalo = document.getElementById('hora_inicio_intervalo');
  var campoHoraFimIntervalo    = document.getElementById('hora_fim_intervalo');

  var DOM_escola_serie_hora = xml.getElementsByTagName('item');

  if (DOM_escola_serie_hora.length) {
    campoHoraInicial.value         = (DOM_escola_serie_hora[0].firstChild || {}).data;
    campoHoraFinal.value           = (DOM_escola_serie_hora[1].firstChild || {}).data;
    campoHoraInicioIntervalo.value = (DOM_escola_serie_hora[2].firstChild || {}).data;
    campoHoraFimIntervalo.value    = (DOM_escola_serie_hora[3].firstChild || {}).data;
  }
}

function valida()
{
  if (validaHorarioInicialFinal() && validaMinutos() && validaAtividadesComplementares()) {
    if (document.getElementById('padrao_ano_escolar').value == 1) {
      var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
      var campoEscola      = document.getElementById('ref_cod_escola').value;
      var campoTurma       = document.getElementById('cod_turma').value;
      var campoComodo      = document.getElementById('ref_cod_infra_predio_comodo').value;
      var campoCurso       = document.getElementById('ref_cod_curso').value;
      var campoSerie       = document.getElementById('ref_cod_serie').value;

      var url  = 'educar_turma_sala_xml.php';
      var pars = '?inst=' + campoInstituicao + '&esc=' + campoEscola + '&not_tur=' +
                campoTurma + '&com=' + campoComodo + '&cur=' + campoCurso+ '&ser=' + campoSerie;

      var xml1 = new ajax(valida_xml);
      strURL   = url + pars;

      xml1.envia(strURL);
    }
    else {
      valida_xml(null);
    }
  }
}

function valida_xml(xml)
{
  var DOM_turma_sala = new Array();

  if (xml != null) {
    DOM_turma_sala = xml.getElementsByTagName('item');
  }

  var campoCurso = document.getElementById('ref_cod_curso').value;

  if (document.getElementById('ref_cod_escola').value) {
    if (!document.getElementById('ref_cod_serie').value) {
      alert("Preencha o campo 'Serie' corretamente!");
      document.getElementById('ref_cod_serie').focus();
      return false;
    }
  }

  if (document.getElementById('multiseriada').checked) {
    if (!document.getElementById('ref_cod_serie_mult')){
      alert("Preencha o campo 'Serie Multi-seriada' corretamente!");
      document.getElementById('ref_cod_serie_mult').focus();
      return false;
    }
  }

  if (document.getElementById('padrao_ano_escolar').value == 1) {
    var campoHoraInicial = document.getElementById('hora_inicial').value;
    var campoHoraFinal = document.getElementById('hora_final').value;
    var campoHoraInicioIntervalo = document.getElementById('hora_inicio_intervalo').value;
    var campoHoraFimIntervalo = document.getElementById('hora_fim_intervalo').value;


  }

  if (document.getElementById('padrao_ano_escolar') == 1) {
    for (var j = 0; j < DOM_turma_sala.length; j += 2) {
      if (
        (DOM_turma_sala[j].firstChild.nodeValue <= document.getElementById('hora_inicial').value) &&
        (document.getElementById('hora_inicial').value <= DOM_turma_sala[j+1].firstChild.nodeValue)
        ||
        (DOM_turma_sala[j].firstChild.nodeValue <= document.getElementById('hora_final').value) &&
        (document.getElementById('hora_final').value <= DOM_turma_sala[j+1].firstChild.nodeValue)
      ) {
        alert("ATENÇÃO!\nA 'sala' ja esta alocada nesse horario!\nPor favor, escolha outro horario ou sala.");
        return false;
      }
    }
  }

  if (!acao()) {
    return false;
  }

  document.forms[0].submit();
}

function excluir_turma_com_matriculas(){

  document.formcadastro.reset();
  alert(stringUtils.toUtf8('Não foi possível excluir a turma, pois a mesma possui matrículas vinculadas.'));
}

function validaCampoServidor()
{
  if (document.getElementById('ref_cod_instituicao').value)
    ref_cod_instituicao = document.getElementById('ref_cod_instituicao').value;
  else {
    alert('Selecione uma instituicao');
    return false;
  }

  if (document.getElementById('ref_cod_escola').value) {
    ref_cod_escola = document.getElementById('ref_cod_escola').value;
  }
  else {
    alert('Selecione uma escola');
    return false;
  }

  pesquisa_valores_popless('educar_pesquisa_professor_lst.php?campo1=ref_cod_regente&professor=1&ref_cod_servidor=0&ref_cod_instituicao=' + ref_cod_instituicao + '&ref_cod_escola=' + ref_cod_escola, 'ref_cod_servidor');
}

document.getElementById('ref_cod_regente_lupa').onclick = function()
{
  validaCampoServidor();
}

function getEscolaCursoSerie()
{
  var campoCurso = document.getElementById('ref_cod_curso').value;

  if (document.getElementById('ref_cod_escola')) {
    var campoEscola = document.getElementById('ref_cod_escola').value;
  }
  else if (document.getElementById('ref_ref_cod_escola')) {
    var campoEscola = document.getElementById('ref_ref_cod_escola').value;
  }

  var campoSerie    = document.getElementById('ref_cod_serie');
  campoSerie.length = 1;

  if (campoEscola && campoCurso) {
    campoSerie.disabled = true;
    campoSerie.options[0].text = 'Carregando series';

    var xml = new ajax(atualizaLstEscolaCursoSerie);
    xml.envia('educar_escola_curso_serie_xml.php?esc=' + campoEscola + '&cur=' + campoCurso);
  }
  else {
    campoSerie.options[0].text = 'Selecione';
  }
}

function atualizaLstEscolaCursoSerie(xml)
{
  var campoSerie             = document.getElementById('ref_cod_serie');
  campoSerie.length          = 1;
  campoSerie.options[0].text = 'Selecione uma s\u00e9rie';
  campoSerie.disabled        = false;

  series = xml.getElementsByTagName('serie');

  if (series.length) {
    for (var i = 0; i < series.length; i++) {
      campoSerie.options[campoSerie.options.length] = new Option(
        series[i].firstChild.data, series[i].getAttribute('cod_serie'), false, false
      );
    }
  }
  else {
    campoSerie.options[0].text = 'A escola/curso n\u00e3o possui nenhuma s\u00e9rie';
  }
}


$j(document).ready( function(){
  $j('#scripts').closest('tr').hide();

  disableInputsDisciplinas();
});

$j('.etapas_utilizadas').mask("9,9,9,9", {placeholder: "1,2,3..."});

$j("#definir_componentes_diferenciados").on("click", function(){
  disableInputsDisciplinas();
});

$j('.check-disciplina').on('change', function(){
  var enabled = $j(this).prop('checked');
  $j(this).closest('.linha-disciplina').find('input:not(.check-disciplina)').attr("disabled", !enabled);
});

function disableInputsDisciplinas() {
  var disable = $j('#definir_componentes_diferenciados').prop('checked');

  $j("#disciplinas").find("input").attr("disabled", !disable);
  $j("#disciplinas").find('.check-disciplina').each(function(){
    $j(this).trigger("change");
  })
}
</script>
