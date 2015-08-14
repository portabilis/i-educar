<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
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
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'Avaliacao/Fixups/CleanComponentesCurriculares.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/String/Utils.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';

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
  var $ref_ref_cod_serie;
  var $ref_ref_cod_serie_;
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
  var $data_fechamento;

  var $ref_cod_instituicao;
  var $ref_cod_curso;
  var $ref_cod_escola_;

  var $padrao_ano_escolar;

  var $ref_cod_regente;
  var $ref_cod_instituicao_regente;

  var $ref_ref_cod_serie_mult;

  // Inclui módulo
  var $turma_modulo;
  var $incluir_modulo;
  var $excluir_modulo;

  // Inclui dia da semana
  var $dia_semana;
  var $ds_hora_inicial;
  var $ds_hora_final;
  var $turma_dia_semana;
  var $incluir_dia_semana;
  var $excluir_dia_semana;
  var $visivel;

  var $tipo_atendimento;
  var $turma_mais_educacao;
  var $atividade_complementar_1;
  var $atividade_complementar_2;
  var $atividade_complementar_3;
  var $atividade_complementar_4;
  var $atividade_complementar_5;
  var $atividade_complementar_6;
  var $aee_braille;
  var $aee_recurso_optico;
  var $aee_estrategia_desenvolvimento;
  var $aee_tecnica_mobilidade;
  var $aee_libras;
  var $aee_caa;
  var $aee_curricular;
  var $aee_soroban;
  var $aee_informatica;
  var $aee_lingua_escrita;
  var $aee_autonomia;
  var $etapa_id;
  var $cod_curso_profissional;
  var $turma_sem_professor;
  var $turma_unificada;
  var $etapa_educacenso;
  var $ref_cod_disciplina_dispensada;

  var $etapas_especificas;
  var $etapas_utilizadas;
  var $utilizaNotaGeralPorEtapa;

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
      $obj      = new clsPmieducarTurma($this->cod_turma);
      $registro = $obj->detalhe();
      $obj_esc  = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
      $det_esc  = $obj_esc->detalhe();
      $obj_ser  = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
      $det_ser  = $obj_ser->detalhe();

      $regra_avaliacao_id = $det_ser["regra_avaliacao_id"];
      $regra_avaliacao_mapper = new RegraAvaliacao_Model_RegraDataMapper();
      $regra_avaliacao = $regra_avaliacao_mapper->find($regra_avaliacao_id);

      $this->utilizaNotaGeralPorEtapa = ($regra_avaliacao->notaGeralPorEtapa == 1);

      $this->ref_cod_escola      = $det_esc['cod_escola'];
      $this->ref_cod_instituicao = $det_esc['ref_cod_instituicao'];
      $this->ref_cod_curso       = $det_ser['ref_cod_curso'];

      $obj_curso = new clsPmieducarCurso(($this->ref_cod_curso));
      $det_curso = $obj_curso->detalhe();
      $this->padrao_ano_escolar = $det_curso['padrao_ano_escolar'];

      if ($registro) {
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $obj_matricula_turma = new clsPmieducarMatriculaTurma();
        $detalhe_turma = $obj_matricula_turma->lista(NULL, $this->cod_turma);

        if($detalhe_turma)
          $this->script_excluir = "excluir_turma_com_matriculas();";

        $this->fexcluir = $obj_permissoes->permissao_excluir(
          586, $this->pessoa_logada, 7, 'educar_turma_lst.php'
        );

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar      = $retorno == 'Editar' ?
      'educar_turma_det.php?cod_turma=' . $registro['cod_turma'] : 'educar_turma_lst.php';

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""        => "{$nomeMenu} turma"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    $this->nome_url_cancelar = 'Cancelar';

    return $retorno;
  }

  function Gerar()
  {

    $scripts = array(
      '/modules/Cadastro/Assets/Javascripts/Turma.js'
      );

    Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

    $styles = array ('/modules/Cadastro/Assets/Stylesheets/Turma.css');

    Portabilis_View_Helper_Application::loadStylesheet($this, $styles);

    if ($_POST) {
      foreach ($_POST as $campo => $val) {
        $this->$campo = $this->$campo ? $this->$campo : $val;
      }
    }

    $this->campoOculto('cod_turma', $this->cod_turma);

    // foreign keys
    $obrigatorio              = FALSE;
    $instituicao_obrigatorio  = TRUE;
    $escola_curso_obrigatorio = TRUE;
    $curso_obrigatorio        = TRUE;
    $get_escola               = TRUE;
    $get_escola_curso_serie   = FALSE;
    $sem_padrao               = TRUE;
    $get_curso                = TRUE;

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

    include 'include/pmieducar/educar_campo_lista.php';

    $this->ref_cod_escola_ = $this->ref_cod_escola;
    $this->campoOculto('ref_cod_escola_', $this->ref_cod_escola_);

    if ($this->ref_cod_escola) {
      $this->ref_ref_cod_escola = $this->ref_cod_escola;
    }

    $opcoes_serie = array('' => 'Selecione');

    // Editar
    if ($this->ref_cod_curso) {
      $obj_serie = new clsPmieducarSerie();
      $obj_serie->setOrderby('nm_serie ASC');
      $lst_serie = $obj_serie->lista(NULL, NULL, NULL, $this->ref_cod_curso, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

      if (is_array($lst_serie) && count($lst_serie)) {
        foreach ($lst_serie as $serie) {
          $opcoes_serie[$serie['cod_serie']] = $serie['nm_serie'];
        }
      }
    }

    $script = "javascript:showExpansivelIframe(520, 550, 'educar_serie_cad_pop.php?ref_ref_cod_serie=sim');";

    if ($this->ref_cod_instituicao && $this->ref_cod_escola   && $this->ref_cod_curso) {
      $script = sprintf("<div id='img_colecao' border='0' onclick='%s'>",
                  $script);
    }
    else {
      $script = sprintf("<div id='img_colecao' border='0' onclick='%s'>",
                  $script);
    }

    $this->campoLista('ref_ref_cod_serie', 'S&eacute;rie', $opcoes_serie, $this->ref_ref_cod_serie,
      '', FALSE, '', $script, $bloqueia);

    $this->ref_ref_cod_serie_ = $this->ref_ref_cod_serie;
    $this->campoOculto('ref_ref_cod_serie_',$this->ref_ref_cod_serie_);

    if ($anoVisivel)
      $this->inputsHelper()->dynamic('anoLetivo');
    else
      $this->campoOculto('ano',$this->ano);

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

    $this->campoListaPesq('ref_cod_regente', 'Professor/Regente', $array_servidor,
      $this->ref_cod_regente, '', '', FALSE, '', '', NULL, NULL, '', TRUE, FALSE, FALSE);

    // Turma tipo
    $opcoes = array('' => 'Selecione');

    // Editar
    if ($this->ref_cod_instituicao) {
      $objTemp = new clsPmieducarTurmaTipo();
      $objTemp->setOrderby('nm_tipo ASC');
      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, 1, $this->ref_cod_instituicao);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes[$registro['cod_turma_tipo']] = $registro['nm_tipo'];
        }
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

    $this->campoTexto('nm_turma', 'Turma', $this->nm_turma, 30, 255, TRUE);

    $this->campoTexto('sgl_turma', $GLOBALS['coreExt']['Config']->app->mostrar_aplicacao == 'botucatu' ? 'C&oacute;digo da sala Prodesp/GDAE' : 'Sigla', $this->sgl_turma, 15, 15, FALSE);

    $this->campoNumero('max_aluno', 'M&aacute;ximo de Alunos', $this->max_aluno, 3, 3, TRUE);

    $this->campoData('data_fechamento', 'Data de fechamento', Portabilis_Date_Utils::pgSQLToBr($this->data_fechamento), false, '', false, '', false, '', Portabilis_String_Utils::toLatin1('Após essa data alunos enturmados não serão ordenados por ordem alfabética'));

    unset($opcoes);
    if (!is_null($this->ref_ref_cod_serie)){
    	$anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
    	$opcaoPadrao = array(null => 'Selecione');
    	$listaComponentes = $anoEscolar->findComponentePorSerie($this->ref_ref_cod_serie);
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

    $this->campoLista('ref_ref_cod_serie_mult','S&eacute;rie', array('' => 'Selecione'),
      '', '', FALSE, '', '', '', FALSE);

    $this->campoOculto('ref_ref_cod_serie_mult_',$this->ref_ref_cod_serie_mult);

    $this->campoQuebra2();

    // hora
    $this->campoHora('hora_inicial', 'Hora Inicial', $this->hora_inicial, FALSE);

    $this->campoHora('hora_final', 'Hora Final', $this->hora_final, FALSE);

    $this->campoHora('hora_inicio_intervalo', 'Hora In&iacute;cio Intervalo',
      $this->hora_inicio_intervalo, FALSE);

    $this->campoHora( 'hora_fim_intervalo', 'Hora Fim Intervalo', $this->hora_fim_intervalo, FALSE);

    $this->inputsHelper()->turmaTurno();

    // modelos boletim
    require_once 'Portabilis/Model/Report/TipoBoletim.php';
    require_once 'Portabilis/Array/Utils.php';

    $tiposBoletim = Portabilis_Model_Report_TipoBoletim::getInstance()->getEnums();
    $tiposBoletim = Portabilis_Array_Utils::insertIn(null, "Selecione um modelo", $tiposBoletim);

    $this->campoLista('tipo_boletim', 'Modelo relat&oacute;rio boletim', $tiposBoletim, $this->tipo_boletim);

    $this->campoQuebra2();

    if ($this->ref_ref_cod_serie) {

      $disciplinas = '';
      $conteudo    = '';

      // Instancia o mapper de componente curricular
      $mapper = new ComponenteCurricular_Model_ComponenteDataMapper();

      // Instancia o mapper de ano escolar
      $anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
      $lista = $anoEscolar->findComponentePorSerie($this->ref_ref_cod_serie);

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

      $this->escola_serie_disciplina = array();

      if (is_array($lista) && count($lista)) {
        $conteudo .= '<div style="margin-bottom: 10px;">';
        $conteudo .= '  <span style="display: block; float: left; width: 250px;">Nome</span>';
        $conteudo .= '  <span style="display: block; float: left; width: 100px;">Carga hor&aacute;ria</span>';
        $conteudo .= '  <span style="display: block; float: left;width: 100px;">Usar padr&atilde;o do componente?</span>';
        if($this->utilizaNotaGeralPorEtapa){
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

          $conteudo .= '<div style="margin-bottom: 10px; float: left">';
          $conteudo .= "  <label style='display: block; float: left; width: 250px'><input type=\"checkbox\" $checked name=\"disciplinas[$registro->id]\" id=\"disciplinas[]\" value=\"{$registro->id}\">{$registro}</label>";
          $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='text' name='carga_horaria[$registro->id]' value='{$cargaHoraria}' size='5' maxlength='7'></label>";
          $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='checkbox' name='usar_componente[$registro->id]' value='1' ". ($usarComponente == TRUE ? $checked : '') .">($cargaComponente h)</label>";
          if($this->utilizaNotaGeralPorEtapa){
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

    $componentes = $help = array();

    try {
      $componentes = App_Model_IedFinder::getEscolaSerieDisciplina(
        $this->ref_ref_cod_serie, $this->ref_cod_escola
      );
    }
    catch (Exception $e) {
    }

    foreach ($componentes as $componente) {
      $help[] = sprintf('%s (%.0f h)', $componente->nome, $componente->cargaHoraria);
    }

    if (count($componentes)) {
      $help = '<ul><li>' . implode('</li><li>', $help) . '</li></ul>';
    }
    else {
      $help = '';
    }

    $label = 'Componentes curriculares:<br />'
           . '<strong>Observa&ccedil;&atilde;o:</strong> caso n&atilde;o defina os componentes<br />'
           . 'curriculares para a turma, esta usar&aacute; a defini&ccedil;&atilde;o<br />'
           . 'da s&eacute;rie/ano escolar da escola:'
           . '<span id="_escola_serie_componentes">%s</span>';

    $label = sprintf($label, $help);

    $this->campoRotulo('disciplinas_', $label,
      "<div id='disciplinas'>$disciplinas</div>");

    $this->campoQuebra2();

    if ($_POST['turma_modulo']) {
      $this->turma_modulo = unserialize(urldecode($_POST['turma_modulo']));
    }

    if ($_POST){
      $qtd_modulo = count($this->turma_modulo) == 0 ? 1 : (count($this->turma_modulo) + 1);
      echo "
        <script type=\"text/javascript\">
          window.setTimeout(function() {
            document.getElementById(\"event_incluir_dia_semana\").focus();
          }, 500);
        </script>
      ";
    }
    else
      $qtd_modulo = 0;

    if (is_numeric($this->cod_turma) && !$_POST) {
      if (!$this->padrao_ano_escolar) {

        $obj = new clsPmieducarTurmaModulo();
        $registros = $obj->lista($this->cod_turma);

        if ($registros) {
          foreach ($registros as $campo) {
            $this->turma_modulo[$campo[$qtd_modulo]]['sequencial_']     = $campo['sequencial'];
            $this->turma_modulo[$campo[$qtd_modulo]]['ref_cod_modulo_'] = $campo['ref_cod_modulo'];
            $this->turma_modulo[$campo[$qtd_modulo]]['data_inicio_']    = dataFromPgToBr($campo['data_inicio']);
            $this->turma_modulo[$campo[$qtd_modulo]]['data_fim_']       = dataFromPgToBr($campo['data_fim']);
            $qtd_modulo++;
          }
        } else {
          $anoLetivoModulos = new clsPmieducarAnoLetivoModulo();
          $anoLetivoModulos = $anoLetivoModulos->lista($this->ano, $this->ref_cod_escola);
          foreach ($anoLetivoModulos as $campo) {
            $this->turma_modulo[$campo[$qtd_modulo]]['sequencial_']     = $campo['sequencial'];
            $this->turma_modulo[$campo[$qtd_modulo]]['ref_cod_modulo_'] = $campo['ref_cod_modulo'];
            $this->turma_modulo[$campo[$qtd_modulo]]['data_inicio_']    = dataFromPgToBr($campo['data_inicio']);
            $this->turma_modulo[$campo[$qtd_modulo]]['data_fim_']       = dataFromPgToBr($campo['data_fim']);
            $qtd_modulo++;
          }
        }
      }
    }

    if ($_POST["ref_cod_modulo"] && $_POST["data_inicio"] && $_POST["data_fim"]) {
      $this->turma_modulo[$qtd_modulo]["sequencial_"]     = $qtd_modulo;
      $this->turma_modulo[$qtd_modulo]["ref_cod_modulo_"] = $_POST["ref_cod_modulo"];
      $this->turma_modulo[$qtd_modulo]["data_inicio_"]    = $_POST["data_inicio"];
      $this->turma_modulo[$qtd_modulo]["data_fim_"]       = $_POST["data_fim"];
      $qtd_modulo++;

      unset($this->ref_cod_modulo);
      unset($this->data_inicio);
      unset($this->data_fim);
    }

    $this->campoOculto("excluir_modulo", "");

    $qtd_modulo = 1;

    unset($aux);
    $scriptExcluir = "";

    if ($this->turma_modulo) {
      foreach ($this->turma_modulo as $campo) {
        if ($this->excluir_modulo == $campo['sequencial_']) {
          $this->turma_modulo[$campo['sequencial']] = NULL;
          $this->excluir_modulo                     = NULL;
        }
        else {
          $obj_modulo     = new clsPmieducarModulo($campo['ref_cod_modulo_']);
          $det_modulo     = $obj_modulo->detalhe();
          $nm_tipo_modulo = $det_modulo['nm_tipo'];

          $this->campoTextoInv('ref_cod_modulo_' . $campo['sequencial_'], '',
            $nm_tipo_modulo, 30, 255, FALSE, FALSE, TRUE, '', '', '', '', 'ref_cod_modulo');

          $this->campoTextoInv('data_inicio_' . $campo['sequencial_'], '',
            $campo['data_inicio_'], 10, 10, FALSE, FALSE, TRUE, '', '', '', '', '');

          $this->campoTextoInv('data_fim_' . $campo['sequencial_'], '', $campo['data_fim_'],
            10, 10, FALSE, FALSE, FALSE, '',
            "<a href='#' id=\"event_excluir_modulo_{$qtd_modulo}\" ><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>",
            '', '', '');

          $scriptExcluir.= "<script type=\"text/javascript\">
                    document.getElementById('event_excluir_modulo_{$qtd_modulo}').onclick = excluirModulo{$qtd_modulo};

                    function excluirModulo{$qtd_modulo}(){
                      document.getElementById('excluir_modulo').value = '{$campo["sequencial_"]}';
                      document.getElementById('tipoacao').value = '';
                      {$this->__nome}.submit();
                    }

               </script>";

          $aux[$qtd_modulo]['sequencial_']     = $qtd_modulo;
          $aux[$qtd_modulo]['ref_cod_modulo_'] = $campo['ref_cod_modulo_'];
          $aux[$qtd_modulo]['data_inicio_']    = $campo['data_inicio_'];
          $aux[$qtd_modulo]['data_fim_']       = $campo['data_fim_'];
          $qtd_modulo++;
        }

      }
      unset($this->turma_modulo);
      $this->turma_modulo = $aux;
    }

    $this->campoOculto('turma_modulo', serialize($this->turma_modulo));

    // Módulo
    // foreign keys
    $opcoes = array('' => 'Selecione');

    // Editar
    if ($this->ref_cod_instituicao) {
      $objTemp = new clsPmieducarModulo();
      $objTemp->setOrderby('nm_tipo ASC');
      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, 1, $this->ref_cod_instituicao);

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes[$registro['cod_modulo']] = $registro['nm_tipo'];
        }
      }
    }

    $this->campoLista('ref_cod_modulo', 'M&oacute;dulo', $opcoes, $this->ref_cod_modulo,
      NULL, NULL, NULL, NULL, NULL, FALSE);

    $this->campoData('data_inicio', 'Data In&iacute;cio', $this->data_inicio, FALSE);
    $this->campoData('data_fim', 'Data Fim', $this->data_fim, FALSE);

    $this->campoOculto('incluir_modulo', '');

    $this->campoRotulo('bt_incluir_modulo', 'M&oacute;dulo',
      "<a href='#' id=\"event_incluir_modulo\" ><img src='imagens/nvp_bot_adiciona.gif' alt='adicionar' title='Incluir' border=0></a>"
    );

    $this->campoQuebra2();

    if ($_POST['turma_dia_semana']) {
      $this->turma_dia_semana = unserialize(urldecode($_POST['turma_dia_semana']));
    }

    if (is_numeric($this->cod_turma) && !$_POST) {
      $obj = new clsPmieducarTurmaDiaSemana();
      $registros = $obj->lista(NULL, $this->cod_turma);

      if ($registros) {
        foreach ($registros as $campo) {
          $aux['dia_semana_']   = $campo['dia_semana'];
          $aux['hora_inicial_'] = $campo['hora_inicial'];
          $aux['hora_final_']   = $campo['hora_final'];

          $this->turma_dia_semana[] = $aux;
        }
      }
    }

    unset($aux);

    if ($_POST['dia_semana'] && $_POST['ds_hora_inicial'] && $_POST['ds_hora_final']) {
      $aux['dia_semana_']   = $_POST['dia_semana'];
      $aux['hora_inicial_'] = $_POST['ds_hora_inicial'];
      $aux['hora_final_']   = $_POST['ds_hora_final'];

      $this->turma_dia_semana[] = $aux;

      unset($this->dia_semana);
      unset($this->ds_hora_inicial);
      unset($this->ds_hora_final);
    }

    $this->campoOculto('excluir_dia_semana', '');
    unset($aux);

    if ($this->turma_dia_semana) {
      foreach ($this->turma_dia_semana as $key => $dias_semana) {
        if ($this->excluir_dia_semana == $dias_semana['dia_semana_']) {
          unset($this->turma_dia_semana[$key]);
          unset($this->excluir_dia_semana);
        }
        else {
          $nm_dia_semana = $this->dias_da_semana[$dias_semana['dia_semana_']];

          $this->campoTextoInv('dia_semana_' . $dias_semana['dia_semana_'], '',
            $nm_dia_semana, 8, 8, FALSE, FALSE, TRUE, '', '', '', '', 'dia_semana');

          $this->campoTextoInv('hora_inicial_' . $dias_semana['dia_semana_'], '',
            $dias_semana['hora_inicial_'], 5, 5, FALSE, FALSE, TRUE, '', '', '',
            '', 'ds_hora_inicial_');

          $this->campoTextoInv('hora_final_' . $dias_semana['dia_semana_'], '',
            $dias_semana['hora_final_'], 5, 5, FALSE, FALSE, FALSE, '',
            "<a href='#' id=\"event_excluir_dia_semana_{$dias_semana["dia_semana_"]}\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>",
            '', '', 'ds_hora_final_'
          );
          $scriptExcluir .= "
                <script type=\"text/javascript\">
                    document.getElementById('event_excluir_dia_semana_{$dias_semana["dia_semana_"]}').onclick = excluirModulo{$dias_semana["dia_semana_"]};

                    function excluirModulo{$dias_semana["dia_semana_"]}(){
                      document.getElementById('excluir_dia_semana').value = '{$dias_semana["dia_semana_"]}';
                      document.getElementById('tipoacao').value = '';
                      {$this->__nome}.submit();
                    }
                </script>";

          $aux['dia_semana_']   = $dias_semana['dia_semana_'];
          $aux['hora_inicial_'] = $dias_semana['hora_inicial_'];
          $aux['hora_final_']   = $dias_semana['hora_final_'];
        }
      }
    }

    $this->campoOculto('turma_dia_semana', serialize($this->turma_dia_semana));

    if (class_exists('clsPmieducarTurmaDiaSemana')) {
      $opcoes = $this->dias_da_semana;
    }
    else {
      echo '<!--\nErro\nClasse clsPmieducarTurmaDiaSemana n&atilde;o encontrada\n-->';
      $opcoes = array('' => 'Erro na gera&ccedil;&atilde;o');
    }

    $this->campoLista('dia_semana', 'Dia Semana', $opcoes, $this->dia_semana, NULL,
      false, '', '', false, false);

    $this->campoHora('ds_hora_inicial', 'Hora Inicial', $this->ds_hora_inicial, FALSE);

    $this->campoHora('ds_hora_final', 'Hora Final', $this->ds_hora_final, FALSE);

    $this->campoOculto('incluir_dia_semana', '');

    $this->campoRotulo('bt_incluir_dia_semana', 'Dia Semana',
      "<a href='#' id=\"event_incluir_dia_semana\"><img src='imagens/nvp_bot_adiciona.gif' alt='adicionar' title='Incluir' border=0></a>"
    );

    $this->campoOculto('padrao_ano_escolar', $this->padrao_ano_escolar);

    // Colocado o script com esse campo pois tentando dar um 'print' ou 'echo' o script não funcionava
    $this->campoTextoInv('scripts', $scriptExcluir);

    $this->acao_enviar = 'valida()';

    $resources = array( 0 => Portabilis_String_Utils::toLatin1('Não se aplica'),
                        1    => 'Classe hospitalar',
                        2    => Portabilis_String_Utils::toLatin1('Unidade de internação socioeducativa'),
                        3    => 'Unidade prisional',
                        4    => 'Atividade complementar',
                        5    => 'Atendimento educacional especializado (AEE)');

    $options = array('label' => 'Tipo de atendimento', 'resources' => $resources, 'value' => $this->tipo_atendimento, 'required' => false, 'size' => 70,);
    $this->inputsHelper()->select('tipo_atendimento', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Turma participante do Programa Mais Educação/Ensino médio Inovador'), 'value' => $this->turma_mais_educacao);
    $this->inputsHelper()->checkbox('turma_mais_educacao', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Código do tipo de atividade complementar 1'), 'value' => $this->atividade_complementar_1, 'required' => false, 'size' => 5, 'max_length' => 5, 'placeholder' => '');
    $this->inputsHelper()->integer('atividade_complementar_1', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Código do tipo de atividade complementar 2'), 'value' => $this->atividade_complementar_2, 'required' => false, 'size' => 5, 'max_length' => 5, 'placeholder' => '');
    $this->inputsHelper()->integer('atividade_complementar_2', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Código do tipo de atividade complementar 3'), 'value' => $this->atividade_complementar_3, 'required' => false, 'size' => 5, 'max_length' => 5, 'placeholder' => '');
    $this->inputsHelper()->integer('atividade_complementar_3', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Código do tipo de atividade complementar 4'), 'value' => $this->atividade_complementar_4, 'required' => false, 'size' => 5, 'max_length' => 5, 'placeholder' => '');
    $this->inputsHelper()->integer('atividade_complementar_4', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Código do tipo de atividade complementar 5'), 'value' => $this->atividade_complementar_5, 'required' => false, 'size' => 5, 'max_length' => 5, 'placeholder' => '');
    $this->inputsHelper()->integer('atividade_complementar_5', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Código do tipo de atividade complementar 6'), 'value' => $this->atividade_complementar_6, 'required' => false, 'size' => 5, 'max_length' => 5, 'placeholder' => '');
    $this->inputsHelper()->integer('atividade_complementar_6', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ensino do Sistema Braille'), 'value' => $this->aee_braille);
    $this->inputsHelper()->checkbox('aee_braille', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ensino de uso de recursos ópticos e não ópticos'), 'value' => $this->aee_recurso_optico);
    $this->inputsHelper()->checkbox('aee_recurso_optico', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Estratégias para o desenvolvimento de processos mentais'), 'value' => $this->aee_estrategia_desenvolvimento);
    $this->inputsHelper()->checkbox('aee_estrategia_desenvolvimento', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Técnica de orientações a mobilidade'), 'value' => $this->aee_tecnica_mobilidade);
    $this->inputsHelper()->checkbox('aee_tecnica_mobilidade', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ensino da Língua Brasileira de Sinais - LIBRAS'), 'value' => $this->aee_libras);
    $this->inputsHelper()->checkbox('aee_libras', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ensino de uso da Comunicação Alternativa e Aumentativa - CAA'), 'value' => $this->aee_caa);
    $this->inputsHelper()->checkbox('aee_caa', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Estratégias para enriquecimento curricular'), 'value' => $this->aee_curricular);
    $this->inputsHelper()->checkbox('aee_curricular', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ensino do uso do Soroban'), 'value' => $this->aee_soroban);
    $this->inputsHelper()->checkbox('aee_soroban', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ensino da usabilidade e das funcionalidades de informática acessível'), 'value' => $this->aee_informatica);
    $this->inputsHelper()->checkbox('aee_informatica', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Ensino da Língua Portuguesa na modalidade escrita'), 'value' => $this->aee_lingua_escrita);
    $this->inputsHelper()->checkbox('aee_lingua_escrita', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Estratégias para autonomia no ambiente escolar'), 'value' => $this->aee_autonomia);
    $this->inputsHelper()->checkbox('aee_autonomia', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Código curso educação profissional'), 'value' => $this->cod_curso_profissional, 'required' => false, 'size' => 8, 'max_length' => 8, 'placeholder' => '');
    $this->inputsHelper()->integer('cod_curso_profissional', $options);

    $options = array('label' => Portabilis_String_Utils::toLatin1('Turma não tem profissional escolar em sala de aula'), 'value' => $this->turma_sem_professor);
    $this->inputsHelper()->checkbox('turma_sem_professor', $options);

    $resources = Portabilis_Utils_Database::fetchPreparedQuery('SELECT id,nome FROM modules.etapas_educacenso');
    $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'id', 'nome');
    $resources = Portabilis_Array_Utils::merge($resources, array('null' => 'Selecione'));


    $options = array('label' => 'Etapa de ensino', 'resources' => $resources, 'value' => $this->etapa_id, 'required' => false, 'size' => 70,);
    $this->inputsHelper()->select('etapa_id', $options);

    $resources = array(
      null => 'Selecione',
      1    => 'Creche',
      2    => Portabilis_String_Utils::toLatin1('Pré-escola'),
    );
    $options = array('label' => 'Turma unificada', 'resources' => $resources,'label_hint' => 'Selecione somente se a turma for unificada', 'value' => $this->turma_unificada, 'required' => false, 'size' => 70,);
    $this->inputsHelper()->select('turma_unificada', $options);

    $etapas_educacenso = array(
      null => 'Selecione',
      1  => Portabilis_String_Utils::toLatin1('Educação Infantil - Creche (0 a 3 anos)'),
      2  => Portabilis_String_Utils::toLatin1('Educação Infantil - Pré-escola (4 e 5 anos)'),
      3  => Portabilis_String_Utils::toLatin1('Educação Infantil - Unificada (0 a 5 anos)'),
      4  => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 8 anos - 1ª Série'),
      5  => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 8 anos - 2ª Série'),
      6  => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 8 anos - 3ª Série'),
      7  => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 8 anos - 4ª Série'),
      8  => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 8 anos - 5ª Série'),
      9  => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 8 anos - 6ª Série'),
      10 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 8 anos - 7ª Série'),
      11 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 8 anos - 8ª Série'),
      12 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 8 anos - Multi'),
      13 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 8 anos - Correção de Fluxo'),
      14 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 9 anos - 1º Ano'),
      15 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 9 anos - 2º Ano'),
      16 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 9 anos - 3º Ano'),
      17 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 9 anos - 4º Ano'),
      18 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 9 anos - 5º Ano'),
      19 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 9 anos - 6º Ano'),
      20 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 9 anos - 7º Ano'),
      21 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 9 anos - 8º Ano'),
      22 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 9 anos - Multi'),
      23 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 9 anos - Correção de Fluxo'),
      24 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 8 e 9 anos - Multi 8 e 9 anos'),
      25 => Portabilis_String_Utils::toLatin1('Ensino Médio - 1ª Série'),
      26 => Portabilis_String_Utils::toLatin1('Ensino Médio - 2ª Série'),
      27 => Portabilis_String_Utils::toLatin1('Ensino Médio - 3ª Série'),
      28 => Portabilis_String_Utils::toLatin1('Ensino Médio - 4ª Série'),
      29 => Portabilis_String_Utils::toLatin1('Ensino Médio - Não Seriada'),
      30 => Portabilis_String_Utils::toLatin1('Ensino Médio - Integrado 1ª Série'),
      31 => Portabilis_String_Utils::toLatin1('Ensino Médio - Integrado 2ª Série'),
      32 => Portabilis_String_Utils::toLatin1('Ensino Médio - Integrado 3ª Série'),
      33 => Portabilis_String_Utils::toLatin1('Ensino Médio - Integrado 4ª Série'),
      34 => Portabilis_String_Utils::toLatin1('Ensino Médio - Integrado Não Seriada'),
      35 => Portabilis_String_Utils::toLatin1('Ensino Médio - Normal/Magistério 1ª Série'),
      36 => Portabilis_String_Utils::toLatin1('Ensino Médio - Normal/Magistério 2ª Série'),
      37 => Portabilis_String_Utils::toLatin1('Ensino Médio - Normal/Magistério 3ª Série'),
      38 => Portabilis_String_Utils::toLatin1('Ensino Médio - Normal/Magistério 4ª Série'),
      39 => Portabilis_String_Utils::toLatin1('Educação Profissional (Concomitante)'),
      40 => Portabilis_String_Utils::toLatin1('Educação Profissional (Subseqüente)'),
      41 => Portabilis_String_Utils::toLatin1('Ensino Fundamental de 9 anos - 9º Ano'),
      43 => Portabilis_String_Utils::toLatin1('EJA Presencial - Anos iniciais'),
      44 => Portabilis_String_Utils::toLatin1('EJA Presencial - Anos finais'),
      45 => Portabilis_String_Utils::toLatin1('EJA Presencial - Ensino Médio'),
      46 => Portabilis_String_Utils::toLatin1('EJA Semipresencial - Anos iniciais'),
      47 => Portabilis_String_Utils::toLatin1('EJA Semipresencial - Anos finais'),
      48 => Portabilis_String_Utils::toLatin1('EJA Semipresencial - Ensino Médio'),
      51 => Portabilis_String_Utils::toLatin1('EJA Presencial - Anos iniciais e Anos finais'),
      56 => Portabilis_String_Utils::toLatin1('Educação Infantil e Ensino Fundamental (8 e 9 anos) Multietapa'),
      58 => Portabilis_String_Utils::toLatin1('EJA Semipresencial - Anos iniciais e Anos finais'),
      60 => Portabilis_String_Utils::toLatin1('EJA Presencial - integrado à Educação Profissional de Nível Fundamental - FIC'),
      61 => Portabilis_String_Utils::toLatin1('EJA Semipresencial - integrado à Educação Profissional de Nível Fundamental - FIC'),
      62 => Portabilis_String_Utils::toLatin1('EJA Presencial - integrada à Educação Profissional de Nível Médio'),
      63 => Portabilis_String_Utils::toLatin1('EJA Semipresencial - integrada à Educação Profissional de Nível Médio'),
      64 => Portabilis_String_Utils::toLatin1('Educação Profissional Mista - Concomitante e Subsequente'),
      65 => Portabilis_String_Utils::toLatin1('EJA Presencial - Ensino Fundamental - Projovem Urbano'),
      66 => Portabilis_String_Utils::toLatin1('Segmento Profissional da EJA integrada')
    );

    $options = array('label' => 'Etapa da turma', 'resources' => $etapas_educacenso, 'value' => $this->etapa_educacenso, 'required' => false, 'size' => 70,);
    $this->inputsHelper()->select('etapa_educacenso', $options);

  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->data_fechamento = Portabilis_Date_Utils::brToPgSQL($this->data_fechamento);

    if(! $this->canCreateTurma($this->ref_cod_escola, $this->ref_ref_cod_serie, $this->turma_turno_id))
      return false;

    $this->ref_cod_instituicao_regente = $this->ref_cod_instituicao;

    if (isset($this->multiseriada)) {
      $this->multiseriada = 1;
    }
    else {
      $this->multiseriada = 0;
    }

    if (isset($this->visivel)) {
      $this->visivel = TRUE;
    }
    else {
      $this->visivel = FALSE;
    }

    $this->turma_dia_semana = unserialize(urldecode($this->turma_dia_semana));

    // Não segue o padrao do curso
    if ($this->padrao_ano_escolar == 0) {
      $this->turma_modulo = unserialize(urldecode($this->turma_modulo));

      if ($this->turma_modulo) {
        $obj = new clsPmieducarTurma(NULL, NULL, $this->pessoa_logada,
          $this->ref_ref_cod_serie, $this->ref_cod_escola,
          $this->ref_cod_infra_predio_comodo, $this->nm_turma, $this->sgl_turma,
          $this->max_aluno, $this->multiseriada, NULL, NULL, 1,
          $this->ref_cod_turma_tipo, $this->hora_inicial, $this->hora_final,
          $this->hora_inicio_intervalo, $this->hora_fim_intervalo, $this->ref_cod_regente,
          $this->ref_cod_instituicao_regente, $this->ref_cod_instituicao,
          $this->ref_cod_curso, $this->ref_ref_cod_serie_mult, $this->ref_cod_escola,
          $this->visivel, $this->turma_turno_id, $this->tipo_boletim, $this->ano, $this->data_fechamento);
        $obj->tipo_atendimento = $this->tipo_atendimento;
        $obj->turma_mais_educacao = $this->turma_mais_educacao == 'on' ? 1 : 0;
        $obj->atividade_complementar_1 = $this->atividade_complementar_1;
        $obj->atividade_complementar_2 = $this->atividade_complementar_2;
        $obj->atividade_complementar_3 = $this->atividade_complementar_3;
        $obj->atividade_complementar_4 = $this->atividade_complementar_4;
        $obj->atividade_complementar_5 = $this->atividade_complementar_5;
        $obj->atividade_complementar_6 = $this->atividade_complementar_6;
        $obj->aee_braille = $this->aee_braille == 'on' ? 1 : 0;
        $obj->aee_recurso_optico = $this->aee_recurso_optico == 'on' ? 1 : 0;
        $obj->aee_estrategia_desenvolvimento = $this->aee_estrategia_desenvolvimento == 'on' ? 1 : 0;
        $obj->aee_tecnica_mobilidade = $this->aee_tecnica_mobilidade == 'on' ? 1 : 0;
        $obj->aee_libras = $this->aee_libras == 'on' ? 1 : 0;
        $obj->aee_caa = $this->aee_caa == 'on' ? 1 : 0;
        $obj->aee_curricular = $this->aee_curricular == 'on' ? 1 : 0;
        $obj->aee_soroban = $this->aee_soroban == 'on' ? 1 : 0;
        $obj->aee_informatica = $this->aee_informatica == 'on' ? 1 : 0;
        $obj->aee_lingua_escrita = $this->aee_lingua_escrita == 'on' ? 1 : 0;
        $obj->aee_autonomia = $this->aee_autonomia == 'on' ? 1 : 0;
        $obj->etapa_id = $this->etapa_id;
        $obj->cod_curso_profissional = $this->cod_curso_profissional;
        $obj->turma_sem_professor = $this->turma_sem_professor == 'on' ? 1 : 0;
        $obj->turma_unificada = $this->turma_unificada;
        $obj->etapa_educacenso = $this->etapa_educacenso;
        $obj->ref_cod_disciplina_dispensada = $this->ref_cod_disciplina_dispensada == "" ? NULL : $this->ref_cod_disciplina_dispensada;

        $this->cod_turma = $cadastrou = $obj->cadastra();

        if ($cadastrou) {
          // Cadastra módulo
          foreach ($this->turma_modulo as $campo) {
            $campo['data_inicio_'] = dataToBanco($campo['data_inicio_']);
            $campo['data_fim_']    = dataToBanco($campo['data_fim_']);

            $obj = new clsPmieducarTurmaModulo($cadastrou, $campo['ref_cod_modulo_'],
              $campo['sequencial_'], $campo['data_inicio_'], $campo['data_fim_']);

            $cadastrou1 = $obj->cadastra();

            if (!$cadastrou1) {
              $this->mensagem = 'Cadastro n&atilde;o realizado.';
              echo "<!--\nErro ao cadastrar clsPmieducarTurmaModulo\nvalores obrigatorios\nis_numeric( $cadastrou ) && is_numeric( {$campo["ref_cod_modulo_"]} ) && is_numeric( {$campo["sequencial_"]} ) && is_string( {$campo["data_inicio_"]} ) && is_string( {$campo["data_fim_"]} )\n-->";

              return FALSE;
            }
          }

          // Cadastra dia semana
          foreach ($this->turma_dia_semana as $campo) {
            $obj = new clsPmieducarTurmaDiaSemana($campo["dia_semana_"],
              $cadastrou, $campo["hora_inicial_"], $campo["hora_final_"]);

            $cadastrou2  = $obj->cadastra();

            if (!$cadastrou2) {
              $this->mensagem = 'Cadastro n&atilde;o realizado.';
              echo "<!--\nErro ao cadastrar clsPmieducarTurmaDiaSemana\nvalores obrigat&oacute;rios\nis_numeric( $cadastrou ) && is_numeric( {$campo["dia_semana_"]} ) && is_string( {$campo["hora_inicial_"]} ) && is_string( {$campo["hora_final_"]} )\n-->";

              return FALSE;
            }
          }
          $this->atualizaComponentesCurriculares(
            $this->ref_ref_cod_serie, $this->ref_cod_escola, $this->cod_turma,
            $this->disciplinas, $this->carga_horaria, $this->usar_componente, $this->docente_vinculado
          );

          $this->mensagem .= 'Cadastro efetuado com sucesso.';
          header('Location: educar_turma_lst.php');
          die();
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.';
        echo "<!--\nErro ao cadastrar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";

        return FALSE;
      }

      echo '<script type="text/javascript">alert("E necessario adicionar pelo menos 1 modulo!")</script>';
      $this->mensagem = "Cadastro n&atilde;o realizado.";

      return FALSE;
    }

    // Segue o padrão do ano escolar
    elseif ($this->padrao_ano_escolar == 1) {
      $obj = new clsPmieducarTurma(null, null, $this->pessoa_logada,
        $this->ref_ref_cod_serie, $this->ref_cod_escola,
        $this->ref_cod_infra_predio_comodo, $this->nm_turma, $this->sgl_turma,
        $this->max_aluno, $this->multiseriada, null, null, 1,
        $this->ref_cod_turma_tipo, $this->hora_inicial, $this->hora_final,
        $this->hora_inicio_intervalo, $this->hora_fim_intervalo,
        $this->ref_cod_regente, $this->ref_cod_instituicao_regente,
        $this->ref_cod_instituicao, $this->ref_cod_curso,
        $this->ref_ref_cod_serie_mult, $this->ref_cod_escola, $this->visivel,
        $this->turma_turno_id, $this->tipo_boletim, $this->ano, $this->data_fechamento);
      $obj->tipo_atendimento = $this->tipo_atendimento;
      $obj->turma_mais_educacao = $this->turma_mais_educacao == 'on' ? 1 : 0;
      $obj->atividade_complementar_1 = $this->atividade_complementar_1;
      $obj->atividade_complementar_2 = $this->atividade_complementar_2;
      $obj->atividade_complementar_3 = $this->atividade_complementar_3;
      $obj->atividade_complementar_4 = $this->atividade_complementar_4;
      $obj->atividade_complementar_5 = $this->atividade_complementar_5;
      $obj->atividade_complementar_6 = $this->atividade_complementar_6;
      $obj->aee_braille = $this->aee_braille == 'on' ? 1 : 0;
      $obj->aee_recurso_optico = $this->aee_recurso_optico == 'on' ? 1 : 0;
      $obj->aee_estrategia_desenvolvimento = $this->aee_estrategia_desenvolvimento == 'on' ? 1 : 0;
      $obj->aee_tecnica_mobilidade = $this->aee_tecnica_mobilidade == 'on' ? 1 : 0;
      $obj->aee_libras = $this->aee_libras == 'on' ? 1 : 0;
      $obj->aee_caa = $this->aee_caa == 'on' ? 1 : 0;
      $obj->aee_curricular = $this->aee_curricular == 'on' ? 1 : 0;
      $obj->aee_soroban = $this->aee_soroban == 'on' ? 1 : 0;
      $obj->aee_informatica = $this->aee_informatica == 'on' ? 1 : 0;
      $obj->aee_lingua_escrita = $this->aee_lingua_escrita == 'on' ? 1 : 0;
      $obj->aee_autonomia = $this->aee_autonomia == 'on' ? 1 : 0;
      $obj->etapa_id = $this->etapa_id;
      $obj->cod_curso_profissional = $this->cod_curso_profissional;
      $obj->turma_sem_professor = $this->turma_sem_professor == 'on' ? 1 : 0;
      $obj->turma_unificada = $this->turma_unificada;
      $obj->etapa_educacenso = $this->etapa_educacenso;
      $obj->ref_cod_disciplina_dispensada = $this->ref_cod_disciplina_dispensada == "" ? NULL : $this->ref_cod_disciplina_dispensada;

      $this->cod_turma = $cadastrou = $obj->cadastra();


      if ($cadastrou) {

        // Cadastra dia semana
        foreach ($this->turma_dia_semana as $campo) {
          $obj = new clsPmieducarTurmaDiaSemana($campo["dia_semana_"],
            $cadastrou, $campo["hora_inicial_"], $campo["hora_final_"]);

          $cadastrou2  = $obj->cadastra();

          if (!$cadastrou2) {
            $this->mensagem = 'Cadastro n&atilde;o realizado.';
            echo "<!--\nErro ao cadastrar clsPmieducarTurmaDiaSemana\nvalores obrigat&oacute;rios\nis_numeric( $cadastrou ) && is_numeric( {$campo["dia_semana_"]} ) && is_string( {$campo["hora_inicial_"]} ) && is_string( {$campo["hora_final_"]} )\n-->";

            return FALSE;
          }
        }
        $this->atualizaComponentesCurriculares(
          $this->ref_ref_cod_serie, $this->ref_cod_escola, $this->cod_turma,
          $this->disciplinas, $this->carga_horaria, $this->usar_componente, $this->docente_vinculado
        );
        $this->mensagem .= 'Cadastro efetuado com sucesso.';
        header('Location: educar_turma_lst.php');
        die();
      }

      $this->mensagem = 'Cadastro n&atilde;o realizado.';
      echo "<!--\nErro ao cadastrar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";

      return FALSE;
    }

  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->data_fechamento = Portabilis_Date_Utils::brToPgSQL($this->data_fechamento);

    $this->ref_cod_instituicao_regente = $this->ref_cod_instituicao;

    if (isset($this->multiseriada)) {
      $this->multiseriada = 1;
    }
    else {
      $this->multiseriada = 0;
    }

    if (isset($this->visivel)) {
      $this->visivel = TRUE;
    }
    else {
      $this->visivel = FALSE;
    }

    $this->turma_dia_semana = unserialize(urldecode($this->turma_dia_semana));

    // Não segue o padrão do curso
    if ($this->padrao_ano_escolar == 0) {
      $this->turma_modulo = unserialize(urldecode($this->turma_modulo));

      if ($this->turma_modulo) {
        $obj = new clsPmieducarTurma($this->cod_turma, $this->pessoa_logada, NULL,
          $this->ref_ref_cod_serie, $this->ref_cod_escola,
          $this->ref_cod_infra_predio_comodo, $this->nm_turma, $this->sgl_turma,
          $this->max_aluno, $this->multiseriada, NULL, NULL, 1,
          $this->ref_cod_turma_tipo, $this->hora_inicial, $this->hora_final,
          $this->hora_inicio_intervalo, $this->hora_fim_intervalo, $this->ref_cod_regente,
          $this->ref_cod_instituicao_regente, $this->ref_cod_instituicao,
          $this->ref_cod_curso, $this->ref_ref_cod_serie_mult, $this->ref_cod_escola,
          $this->visivel,
          $this->turma_turno_id,
          $this->tipo_boletim,
          $this->ano, $this->data_fechamento);
        $obj->tipo_atendimento = $this->tipo_atendimento;
        $obj->turma_mais_educacao = $this->turma_mais_educacao == 'on' ? 1 : 0;
        $obj->atividade_complementar_1 = $this->atividade_complementar_1;
        $obj->atividade_complementar_2 = $this->atividade_complementar_2;
        $obj->atividade_complementar_3 = $this->atividade_complementar_3;
        $obj->atividade_complementar_4 = $this->atividade_complementar_4;
        $obj->atividade_complementar_5 = $this->atividade_complementar_5;
        $obj->atividade_complementar_6 = $this->atividade_complementar_6;
        $obj->aee_braille = $this->aee_braille == 'on' ? 1 : 0;
        $obj->aee_recurso_optico = $this->aee_recurso_optico == 'on' ? 1 : 0;
        $obj->aee_estrategia_desenvolvimento = $this->aee_estrategia_desenvolvimento == 'on' ? 1 : 0;
        $obj->aee_tecnica_mobilidade = $this->aee_tecnica_mobilidade == 'on' ? 1 : 0;
        $obj->aee_libras = $this->aee_libras == 'on' ? 1 : 0;
        $obj->aee_caa = $this->aee_caa == 'on' ? 1 : 0;
        $obj->aee_curricular = $this->aee_curricular == 'on' ? 1 : 0;
        $obj->aee_soroban = $this->aee_soroban == 'on' ? 1 : 0;
        $obj->aee_informatica = $this->aee_informatica == 'on' ? 1 : 0;
        $obj->aee_lingua_escrita = $this->aee_lingua_escrita == 'on' ? 1 : 0;
        $obj->aee_autonomia = $this->aee_autonomia == 'on' ? 1 : 0;
        $obj->etapa_id = $this->etapa_id;
        $obj->cod_curso_profissional = $this->cod_curso_profissional;
        $obj->turma_sem_professor = $this->turma_sem_professor == 'on' ? 1 : 0;
        $obj->turma_unificada = $this->turma_unificada;
        $obj->etapa_educacenso = $this->etapa_educacenso;
        $obj->ref_cod_disciplina_dispensada = $this->ref_cod_disciplina_dispensada == "" ? NULL : $this->ref_cod_disciplina_dispensada;

        $editou = $obj->edita();

        if ($editou) {
          $obj  = new clsPmieducarTurmaModulo();
          $excluiu = $obj->excluirTodos($this->cod_turma);

          if ($excluiu) {
            foreach ($this->turma_modulo as $campo) {
              $campo['data_inicio_'] = dataToBanco($campo['data_inicio_']);
              $campo['data_fim_']    = dataToBanco($campo['data_fim_']);

              $obj = new clsPmieducarTurmaModulo($this->cod_turma,
                $campo['ref_cod_modulo_'], $campo['sequencial_'],
                $campo['data_inicio_'], $campo['data_fim_']);

              $cadastrou1 = $obj->cadastra();
              if (!$cadastrou1) {
                $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.';
                echo "<!--\nErro ao editar clsPmieducarTurmaModulo\nvalores obrigatorios\nis_numeric( $this->cod_turma ) && is_numeric( {$campo["ref_cod_modulo_"]} ) \n-->";

                return FALSE;
              }
            }
          }

          // Edita o dia da semana
          $obj  = new clsPmieducarTurmaDiaSemana(NULL, $this->cod_turma);
          $excluiu = $obj->excluirTodos();

          if ($excluiu) {
            foreach ($this->turma_dia_semana as $campo) {
              $obj = new clsPmieducarTurmaDiaSemana($campo["dia_semana_"],
                $this->cod_turma, $campo["hora_inicial_"], $campo["hora_final_"]);

              $cadastrou2  = $obj->cadastra();

              if (!$cadastrou2) {
                $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.';
                echo "<!--\nErro ao editar clsPmieducarTurmaDiaSemana\nvalores obrigat&oacute;rios\nis_numeric( $this->cod_turma ) && is_numeric( {$campo["dia_semana_"]} ) \n-->";

                return FALSE;
              }
            }
          }
        }
        else {
          $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.';
          echo "<!--\nErro ao editar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";

          return FALSE;
        }
      }
      else {
        echo '<script type="text/javascript">alert("E necessario adicionar pelo menos 1 modulo!")</script>';
        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.';

        return FALSE;
      }
    }

    // Segue o padrão do curso
    elseif ($this->padrao_ano_escolar == 1) {
      $obj = new clsPmieducarTurma($this->cod_turma, $this->pessoa_logada, NULL,
        $this->ref_ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_infra_predio_comodo,
        $this->nm_turma, $this->sgl_turma, $this->max_aluno, $this->multiseriada,
        NULL, NULL, 1, $this->ref_cod_turma_tipo, $this->hora_inicial, $this->hora_final,
        $this->hora_inicio_intervalo, $this->hora_fim_intervalo, $this->ref_cod_regente,
        $this->ref_cod_instituicao_regente, $this->ref_cod_instituicao,
        $this->ref_cod_curso, $this->ref_ref_cod_serie_mult, $this->ref_cod_escola,
        $this->visivel, $this->turma_turno_id, $this->tipo_boletim, $this->ano, $this->data_fechamento);
      $obj->tipo_atendimento = $this->tipo_atendimento;
      $obj->turma_mais_educacao = $this->turma_mais_educacao == 'on' ? 1 : 0;
      $obj->atividade_complementar_1 = $this->atividade_complementar_1;
      $obj->atividade_complementar_2 = $this->atividade_complementar_2;
      $obj->atividade_complementar_3 = $this->atividade_complementar_3;
      $obj->atividade_complementar_4 = $this->atividade_complementar_4;
      $obj->atividade_complementar_5 = $this->atividade_complementar_5;
      $obj->atividade_complementar_6 = $this->atividade_complementar_6;
      $obj->aee_braille = $this->aee_braille == 'on' ? 1 : 0;
      $obj->aee_recurso_optico = $this->aee_recurso_optico == 'on' ? 1 : 0;
      $obj->aee_estrategia_desenvolvimento = $this->aee_estrategia_desenvolvimento == 'on' ? 1 : 0;
      $obj->aee_tecnica_mobilidade = $this->aee_tecnica_mobilidade == 'on' ? 1 : 0;
      $obj->aee_libras = $this->aee_libras == 'on' ? 1 : 0;
      $obj->aee_caa = $this->aee_caa == 'on' ? 1 : 0;
      $obj->aee_curricular = $this->aee_curricular == 'on' ? 1 : 0;
      $obj->aee_soroban = $this->aee_soroban == 'on' ? 1 : 0;
      $obj->aee_informatica = $this->aee_informatica == 'on' ? 1 : 0;
      $obj->aee_lingua_escrita = $this->aee_lingua_escrita == 'on' ? 1 : 0;
      $obj->aee_autonomia = $this->aee_autonomia == 'on' ? 1 : 0;
      $obj->etapa_id = $this->etapa_id;
      $obj->cod_curso_profissional = $this->cod_curso_profissional;
      $obj->turma_sem_professor = $this->turma_sem_professor == 'on' ? 1 : 0;
      $obj->turma_unificada = $this->turma_unificada;
      $obj->etapa_educacenso = $this->etapa_educacenso;
      $obj->ref_cod_disciplina_dispensada = $this->ref_cod_disciplina_dispensada == "" ? NULL : $this->ref_cod_disciplina_dispensada;

      $editou = $obj->edita();
    }

    $this->atualizaComponentesCurriculares(
      $this->ref_ref_cod_serie_, $this->ref_cod_escola_, $this->cod_turma,
      $this->disciplinas, $this->carga_horaria, $this->usar_componente, $this->docente_vinculado
    );

    // Caso tenham sido selecionadas discplinas, como se trata de uma edição de turma será rodado uma consulta
    // que limpa os Componentes Curriculares antigos.
    if($this->disciplinas != 1){
      $anoLetivo = $this->ano ? $this->ano : date("Y");
      CleanComponentesCurriculares::destroyOldResources($anoLetivo);
    }


    if ($editou) {

      // Edita o dia da semana
      $obj  = new clsPmieducarTurmaDiaSemana(NULL, $this->cod_turma);
      $excluiu = $obj->excluirTodos();

      if ($excluiu) {
        foreach ($this->turma_dia_semana as $campo) {
          $obj = new clsPmieducarTurmaDiaSemana($campo["dia_semana_"],
            $this->cod_turma, $campo["hora_inicial_"], $campo["hora_final_"]);

          $cadastrou2  = $obj->cadastra();

          if (!$cadastrou2) {
            $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.';
            echo "<!--\nErro ao editar clsPmieducarTurmaDiaSemana\nvalores obrigat&oacute;rios\nis_numeric( $this->cod_turma ) && is_numeric( {$campo["dia_semana_"]} ) \n-->";

            return FALSE;
          }
        }
      }
      $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.';
      header('Location: educar_turma_lst.php');
      die();
    }
    else {
      $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.';
      echo "<!--\nErro ao editar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";

      return FALSE;
    }
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

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj = new clsPmieducarTurma($this->cod_turma, $this->pessoa_logada, null,
      null, null, null, null, null, null, null, null, null, 0);

    $excluiu = $obj->excluir();

    if ($excluiu) {
      $obj      = new clsPmieducarTurmaModulo();
      $excluiu1 = $obj->excluirTodos($this->cod_turma);

      if ($excluiu1) {
        $obj      = new clsPmieducarTurmaDiaSemana(NULL, $this->cod_turma);
        $excluiu2 = $obj->excluirTodos();

        if ($excluiu2) {
          $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.';
          header('Location: educar_turma_lst.php');
          die();
        }
        else {
          $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.';
          echo "<!--\nErro ao excluir clsPmieducarTurma\nvalores obrigatorios\nif( is_numeric( $this->cod_turma ) && is_numeric( $this->pessoa_logada ) )\n-->";

          return FALSE;
        }
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

      $turmas = $turmas->lista(null, null, null, $serieId, $escolaId, null, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, true, $turnoId);

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

  // Inclui dia da semana
  //setVisibility('tr_dia_semana',false);
  //setVisibility('tr_ds_hora_inicial',false);
  //setVisibility('tr_ds_hora_final',false);
  //setVisibility('tr_bt_incluir_dia_semana',false);

  if (!document.getElementById('ref_ref_cod_serie').value) {
    setVisibility('tr_multiseriada',false);
    setVisibility('tr_ref_ref_cod_serie_mult', document.getElementById('multiseriada').checked ? true : false);
    setVisibility('ref_ref_cod_serie_mult', document.getElementById('multiseriada').checked ? true : false);
  }
  else {
    if(document.getElementById('multiseriada').checked){
      changeMultiSerie();
      document.getElementById('ref_ref_cod_serie_mult').value =
        document.getElementById('ref_ref_cod_serie_mult_').value;
    }
    else {
      setVisibility('tr_ref_ref_cod_serie_mult', document.getElementById('multiseriada').checked ? true : false);
      setVisibility('ref_ref_cod_serie_mult', document.getElementById('multiseriada').checked ? true : false);
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

  if (document.getElementById('padrao_ano_escolar').value == 0) {
    setVisibility('tr_ref_cod_modulo', true);
    setVisibility('ref_cod_modulo', true);
    setVisibility('tr_data_inicio', true);
    setVisibility('tr_data_fim', true);
    setVisibility('tr_bt_incluir_modulo', true);

    setVisibility('tr_dia_semana', true);
    setVisibility('tr_ds_hora_inicial', true);
    setVisibility('tr_ds_hora_final', true);
    setVisibility('tr_bt_incluir_dia_semana', true);

    var hr_tag = document.getElementsByTagName('hr');
    for (var ct = 0;ct < hr_tag.length; ct++) {
      setVisibility(hr_tag[ct].parentNode.parentNode, true);
    }
  } else {
    setVisibility('tr_ref_cod_modulo',false);
    setVisibility('ref_cod_modulo',false);
    setVisibility('tr_data_inicio',false);
    setVisibility('tr_data_fim',false);
    setVisibility('tr_bt_incluir_modulo',false);
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

before_getEscola = function()
{
  getModulo();
  getTipoTurma();
  document.getElementById('ref_cod_escola').onchange();
}

document.getElementById('ref_cod_escola').onchange = function()
{
  getEscolaCurso();
  getComodo();
  changeMultiSerie();
  getEscolaCursoSerie();
  PadraoAnoEscolar(null);
  changeMultiSerie();
  hideMultiSerie();

  if (document.getElementById('ref_cod_escola').value == '') {
    getCurso();
  }

  $('img_colecao').style.display = 'none;';

  if ($F('ref_cod_instituicao') == '') {
    $('img_turma').style.display = 'none;';
  }
  else {
    $('img_turma').style.display = '';
  }
}

document.getElementById('ref_cod_curso').onchange = function()
{
  setVisibility('tr_multiseriada', document.getElementById('ref_ref_cod_serie').value ? true : false);
  setVisibility('tr_ref_ref_cod_serie_mult', document.getElementById('multiseriada').checked ? true : false);
  setVisibility('ref_ref_cod_serie_mult', document.getElementById('multiseriada').checked ? true : false);

  hideMultiSerie();
  getEscolaCursoSerie();

  PadraoAnoEscolar_xml();

  if (this.value == '') {
    $('img_colecao').style.display = 'none;';
  }
  else {
    $('img_colecao').style.display = '';
  }
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
  var campoSerie = document.getElementById('ref_ref_cod_serie').value;

  var xml1 = new ajax(atualizaMultiSerie);
  strURL   = 'educar_sequencia_serie_xml.php?cur=' + campoCurso + '&ser_dif=' + campoSerie;

  xml1.envia(strURL);
}

function atualizaMultiSerie(xml)
{
  var campoMultiSeriada = document.getElementById('multiseriada');
  var checked = campoMultiSeriada.checked;

  var multiBool = (document.getElementById('multiseriada').checked == true &&
                   document.getElementById('ref_ref_cod_serie').value != '') ? true : false;

  setVisibility('tr_ref_ref_cod_serie_mult', multiBool);
  setVisibility('ref_ref_cod_serie_mult', multiBool);

  if (!checked){
    document.getElementById('ref_ref_cod_serie_mult').value = '';
    return;
  }

  var campoEscola     = document.getElementById('ref_cod_escola').value;
  var campoCurso      = document.getElementById('ref_cod_curso').value;
  var campoSerieMult  = document.getElementById('ref_ref_cod_serie_mult');
  var campoSerie      = document.getElementById('ref_ref_cod_serie');

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

  document.getElementById('ref_ref_cod_serie_mult').value = document.getElementById('ref_ref_cod_serie_mult_').value;
}

document.getElementById('multiseriada').onclick = function()
{
  changeMultiSerie();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{
  if (this.value) {
    codEscola = document.getElementById('ref_cod_escola').value;

    getHoraEscolaSerie();
    getComponentesCurriculares(this.value);
    getComponentesEscolaSerie(codEscola, this.value);
  }

  if (document.getElementById('multiseriada').checked == true) {
    changeMultiSerie();
  }

  hideMultiSerie();
}

function getComponentesCurriculares(campoSerie)
{
  var xml_disciplina = new ajax(parseComponentesCurriculares);
  xml_disciplina.envia("educar_disciplina_xml.php?ser=" + campoSerie);
}

function getComponentesEscolaSerie(codEscola, codSerie)
{
  var xml_disciplina = new ajax(parseComponentesCurricularesEscolaSerie);
  xml_disciplina.envia("educar_disciplina_xml.php?esc=" + codEscola + "&ser=" + codSerie);
}

function parseComponentesCurriculares(xml_disciplina)
{
  var campoDisciplinas = document.getElementById('disciplinas');
  var DOM_array = xml_disciplina.getElementsByTagName('disciplina');
  var conteudo = '';

  if (DOM_array.length) {
    conteudo += '<div style="margin-bottom: 10px; float: left">';
    conteudo += '  <span style="display: block; float: left; width: 250px;">Nome</span>';
    conteudo += '  <label> <span style="display: block; float: left; width: 100px">Carga hor&aacute;ria </span></label>';
    conteudo += '  <label> <span style="display: block; float: left; width: 200px">Usar padr&atilde;o do componente?</span></label>';
    conteudo += '  <label> <span style="display: block; float: left">Possui docente vinculado?</span></label>';
    conteudo += '</div>';
    conteudo += '<br style="clear: left" />';

    for (var i = 0; i < DOM_array.length; i++) {
      id = DOM_array[i].getAttribute("cod_disciplina");

      conteudo += '<div style="margin-bottom: 10px; float: left">';
      conteudo += '  <label style="display: block; float: left; width: 250px;"><input type="checkbox" name="disciplinas['+ id +']" id="disciplinas[]" value="'+ id +'">'+ DOM_array[i].firstChild.data +'</label>';
      conteudo += '  <label style="display: block; float: left; width: 100px;"><input type="text" name="carga_horaria['+ id +']" value="" size="5" maxlength="7"></label>';
      conteudo += '  <label style="display: block; float: left;width: 200px;"><input type="checkbox" name="usar_componente['+ id +']" value="1">('+ DOM_array[i].getAttribute("carga_horaria") +' h)</label>';
      conteudo += '  <label style="display: block; float: left;"><input type="checkbox" name="docente_vinculado['+ id +']" value="1"></label>';
      conteudo += '</div>';
      conteudo += '<br style="clear: left" />';
    }
  }
  else {
    campoDisciplinas.innerHTML = 'A s\u00e9rie/ano escolar n\u00e3o possui componentes '
                               + 'curriculares cadastrados.';
  }

  if (conteudo) {
    campoDisciplinas.innerHTML = '<table cellspacing="0" cellpadding="0" border="0">';
    campoDisciplinas.innerHTML += '<tr align="left"><td>'+ conteudo +'</td></tr>';
    campoDisciplinas.innerHTML += '</table>';
  }
}

function parseComponentesCurricularesEscolaSerie(xml)
{
  var helpSpan = document.getElementById('_escola_serie_componentes');
  var elements = xml.getElementsByTagName('disciplina');

  ret = '';

  if (elements.length) {
    ret = '<ul>';

    for (var i = 0; i < elements.length; i++) {
      carga = elements[i].getAttribute('carga_horaria');
      name  = elements[i].firstChild.data;

      ret += '<li>' + name + ' (' + carga + ' h)</li>';
    }

    ret += '</ul>';
  }

  helpSpan.innerHTML = ret;
}

function hideMultiSerie()
{
  setVisibility('tr_multiseriada', document.getElementById('ref_ref_cod_serie').value != '' ? true : false);

  var multiBool = (document.getElementById('multiseriada').checked == true &&
                   document.getElementById('ref_ref_cod_serie').value != '')  ? true : false;

  setVisibility('ref_ref_cod_serie_mult', multiBool);
  setVisibility('tr_ref_ref_cod_serie_mult',multiBool);
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

  setVisibility('tr_ref_cod_modulo', false);
  setVisibility('ref_cod_modulo', false);
  setVisibility('tr_data_inicio', false);
  setVisibility('tr_data_fim', false);
  setVisibility('tr_bt_incluir_modulo', false);

  var modulos = document.getElementsByName('tr_ref_cod_modulo');

  for (var i = 0; i < modulos.length; i++) {
    setVisibility(modulos[i].id, false);
  }

  /*setVisibility('tr_dia_semana', false);
  setVisibility('tr_ds_hora_inicial', false);
  setVisibility('tr_ds_hora_final', false);
  setVisibility('tr_bt_incluir_dia_semana', false);

  if (document.getElementById('tr_dia_semana_1')) {
    setVisibility('tr_dia_semana_1', false);
  }

  if (document.getElementById('tr_dia_semana_2')) {
    setVisibility('tr_dia_semana_2', false);
  }

  if (document.getElementById('tr_dia_semana_3')) {
    setVisibility('tr_dia_semana_3', false);
  }

  if (document.getElementById('tr_dia_semana_4')) {
    setVisibility('tr_dia_semana_4', false);
  }

  if (document.getElementById('tr_dia_semana_5')) {
    setVisibility('tr_dia_semana_5', false);
  }

  if (document.getElementById('tr_dia_semana_6')) {
    setVisibility('tr_dia_semana_6', false);
  }

  if (document.getElementById('tr_dia_semana_7')) {
    setVisibility('tr_dia_semana_7', false);
  }*/

  setVisibility('tr_hora_inicial', true);
  setVisibility('tr_hora_final', true);
  setVisibility('tr_hora_inicio_intervalo', true);
  setVisibility('tr_hora_fim_intervalo', true);

  if (campoCurso == '') {
    return;
  }

  var campoCurso = document.getElementById('ref_cod_curso').value;

  if (document.getElementById('padrao_ano_escolar').value == 0) {
    setVisibility('tr_ref_cod_modulo', true);
    setVisibility('ref_cod_modulo', true);
    setVisibility('tr_data_inicio', true);
    setVisibility('tr_data_fim', true);
    setVisibility('tr_bt_incluir_modulo', true);

    var modulos = document.getElementsByName('tr_ref_cod_modulo');

    for (var i = 0; i < modulos.length; i++) {
      setVisibility(modulos[i].id, true);
    }
  }

  setVisibility('tr_dia_semana', true);
  setVisibility('tr_ds_hora_inicial', true);
  setVisibility('tr_ds_hora_final', true);
  setVisibility('tr_bt_incluir_dia_semana', true);

  if (document.getElementById('tr_dia_semana_1')) {
    setVisibility('tr_dia_semana_1', true);
  }

  if (document.getElementById('tr_dia_semana_2')) {
    setVisibility('tr_dia_semana_2', true);
  }

  if (document.getElementById('tr_dia_semana_3')) {
    setVisibility('tr_dia_semana_3', true);
  }

  if (document.getElementById('tr_dia_semana_4')) {
    setVisibility('tr_dia_semana_4', true);
  }

  if (document.getElementById('tr_dia_semana_5')) {
    setVisibility('tr_dia_semana_5', true);
  }

  if (document.getElementById('tr_dia_semana_6')) {
    setVisibility('tr_dia_semana_6', true);
  }

  if (document.getElementById('tr_dia_semana_7')) {
    setVisibility('tr_dia_semana_7', true);
  }
}

function getHoraEscolaSerie()
{
  var campoEscola = document.getElementById('ref_cod_escola').value;
  var campoSerie  = document.getElementById('ref_ref_cod_serie').value;

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
    campoHoraInicial.value         = DOM_escola_serie_hora[0].firstChild.data;
    campoHoraFinal.value           = DOM_escola_serie_hora[1].firstChild.data;
    campoHoraInicioIntervalo.value = DOM_escola_serie_hora[2].firstChild.data;
    campoHoraFimIntervalo.value    = DOM_escola_serie_hora[3].firstChild.data;
  }
}

function valida()
{
  if (document.getElementById('padrao_ano_escolar').value == 1) {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    var campoEscola      = document.getElementById('ref_cod_escola').value;
    var campoTurma       = document.getElementById('cod_turma').value;
    var campoComodo      = document.getElementById('ref_cod_infra_predio_comodo').value;
    var campoCurso       = document.getElementById('ref_cod_curso').value;
    var campoSerie       = document.getElementById('ref_ref_cod_serie').value;

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

function valida_xml(xml)
{
  var DOM_turma_sala = new Array();

  if (xml != null) {
    DOM_turma_sala = xml.getElementsByTagName('item');
  }

  var campoCurso = document.getElementById('ref_cod_curso').value;

  if (document.getElementById('ref_cod_escola').value) {
    if (!document.getElementById('ref_ref_cod_serie').value) {
      alert("Preencha o campo 'Serie' corretamente!");
      document.getElementById('ref_ref_cod_serie').focus();
      return false;
    }
  }

  if (document.getElementById('multiseriada').checked) {
    if (!document.getElementById('ref_ref_cod_serie_mult')){
      alert("Preencha o campo 'Serie Multi-seriada' corretamente!");
      document.getElementById('ref_ref_cod_serie_mult').focus();
      return false;
    }
  }

  if (document.getElementById('padrao_ano_escolar').value == 1) {
    var campoHoraInicial = document.getElementById('hora_inicial').value;
    var campoHoraFinal = document.getElementById('hora_final').value;
    var campoHoraInicioIntervalo = document.getElementById('hora_inicio_intervalo').value;
    var campoHoraFimIntervalo = document.getElementById('hora_fim_intervalo').value;

    if (campoHoraInicial == '') {
      alert("Preencha o campo 'Hora Inicial' corretamente!");
      document.getElementById('hora_inicial').focus();
      return false;
    }
    else if (campoHoraFinal == '') {
      alert("Preencha o campo 'Hora Final' corretamente!");
      document.getElementById('hora_final').focus();
      return false;
    }
    else if (campoHoraInicioIntervalo == '') {
      alert("Preencha o campo 'Hora Inicio Intervalo' corretamente!");
      document.getElementById('hora_inicio_intervalo').focus();
      return false;
    }
    else if (campoHoraFimIntervalo == '') {
      alert("Preencha o campo 'Hora Fim Intervalo' corretamente!");
      document.getElementById('hora_fim_intervalo').focus();
      return false;
    }
  }
  else if (document.getElementById('padrao_ano_escolar').value == 0) {
    var qtdModulo = document.getElementsByName('ref_cod_modulo').length;
    var qtdDiaSemana = document.getElementsByName('dia_semana').length;

    if (qtdModulo == 1) {
      alert("ATEN\u00c7\u00c3O!\n\u00c9 necess\u00e1rio incluir um 'M\u00f3dulo'!");
      document.getElementById('ref_cod_modulo').focus();
      return false;
    }
    /*
    if (qtdDiaSemana == 1) {
      alert("ATENÇÂO! \n É necess&aacute;rio incluir um 'Dia da Semana'!");
      document.getElementById('dia_semana').focus();
      return false;
    }*/
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

  if (confirm('Existe(m) matr\u00edcula(s) vinculada(s) a essa turma, caso exclu\u00edda n\u00e3o ser\u00e1 poss\u00edvel emitir relat\u00f3rios! \nDeseja realmente excluir essa turma?')) {
    document.formcadastro.tipoacao.value = 'Excluir';
    document.formcadastro.submit();
  }
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

  pesquisa_valores_popless('educar_pesquisa_servidor_lst.php?campo1=ref_cod_regente&professor=1&ref_cod_servidor=0&ref_cod_instituicao=' + ref_cod_instituicao + '&ref_cod_escola=' + ref_cod_escola, 'ref_cod_servidor');
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

  var campoSerie    = document.getElementById('ref_ref_cod_serie');
  campoSerie.length = 1;

  limpaCampos(4);

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
  var campoSerie             = document.getElementById('ref_ref_cod_serie');
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

document.getElementById('event_incluir_modulo').onclick = incluirModulo;

function incluirModulo(){
  document.getElementById('incluir_modulo').value = 'S';
  document.getElementById('tipoacao').value = '';
  acao();
}

document.getElementById('event_incluir_dia_semana').onclick = incluirDiaSemana;

function incluirDiaSemana(){
  document.getElementById('incluir_dia_semana').value = 'S';
  document.getElementById('tipoacao').value = '';
  acao();
}

$j(document).ready( function(){
  $j('#scripts').closest('tr').hide();
});

$j('.etapas_utilizadas').mask("9,9,9,9", {placeholder: "1,2,3..."});

</script>
