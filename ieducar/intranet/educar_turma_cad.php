<?php

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
  var $ref_cod_escola;

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

  var $dias_da_semana = array(
    '' => 'Selecione',
    1  => 'Domingo',
    2  => 'Segunda',
    3  => 'Terça',
    4  => 'Quarta',
    5  => 'Quinta',
    6  => 'Sexta',
    7  => 'Sábado'
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

        $this->fexcluir = $obj_permissoes->permissao_excluir(
          586, $this->pessoa_logada, 7, 'educar_turma_lst.php'
        );

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar      = $retorno == 'Editar' ?
      'educar_turma_det.php?cod_turma=' . $registro['cod_turma'] : 'educar_turma_lst.php';
    $this->nome_url_cancelar = 'Cancelar';

    return $retorno;
  }

  function Gerar()
  {
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

    include 'include/pmieducar/educar_campo_lista.php';

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
      $script = sprintf("<img id='img_colecao' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick='%s'>",
                  $script);
    }
    else {
      $script = sprintf("<img id='img_colecao' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick='%s'>",
                  $script);
    }

    $this->campoLista('ref_ref_cod_serie', 'Série', $opcoes_serie, $this->ref_ref_cod_serie,
      '', FALSE, '', $script);

    // o campo ano somente é exibido para turmas novas  ou cadastradas após inclusão deste campo.
    if (! isset($this->cod_turma) || isset($this->ano))
      $this->inputsHelper()->dynamic('anoLetivo');

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
      $script = sprintf("<img id='img_turma' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick='%s'>",
                  $script);
    }
    else {
      $script = sprintf("<img id='img_turma' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick='%s'>",
                  $script);
    }

    $this->campoLista('ref_cod_turma_tipo', 'Tipo de Turma', $opcoes,
      $this->ref_cod_turma_tipo, '', FALSE, '', $script);

    $this->campoTexto('nm_turma', 'Turma', $this->nm_turma, 30, 255, TRUE);

    $this->campoTexto('sgl_turma', 'Sigla', $this->sgl_turma, 15, 15, FALSE);

    $this->campoNumero('max_aluno', 'Máximo de Alunos', $this->max_aluno, 3, 3, TRUE);

    $ativo = isset($this->cod_turma) ? dbBool($this->visivel) : true;
    $this->campoCheck('visivel', 'Ativo', $ativo);

    $this->campoCheck('multiseriada', 'Multi-Seriada', $this->multiseriada, '',
      FALSE, FALSE);

    $this->campoLista('ref_ref_cod_serie_mult','Série', array('' => 'Selecione'),
      '', '', FALSE, '', '', '', FALSE);

    $this->campoOculto('ref_ref_cod_serie_mult_',$this->ref_ref_cod_serie_mult);

    $this->campoQuebra2();

    // hora
    $this->campoHora('hora_inicial', 'Hora Inicial', $this->hora_inicial, FALSE);

    $this->campoHora('hora_final', 'Hora Final', $this->hora_final, FALSE);

    $this->campoHora('hora_inicio_intervalo', 'Hora Início Intervalo',
      $this->hora_inicio_intervalo, FALSE);

    $this->campoHora( 'hora_fim_intervalo', 'Hora Fim Intervalo', $this->hora_fim_intervalo, FALSE);

    $this->inputsHelper()->turmaTurno();

    // modelos boletim
    require_once 'Portabilis/Model/Report/TipoBoletim.php';
    require_once 'Portabilis/Array/Utils.php';

    $tiposBoletim = Portabilis_Model_Report_TipoBoletim::getInstance()->getEnums();
    $tiposBoletim = Portabilis_Array_Utils::insertIn(null, "Selecione um modelo", $tiposBoletim);

    $this->campoLista('tipo_boletim', 'Modelo relatório boletim', $tiposBoletim, $this->tipo_boletim);

    $this->campoQuebra2();

    if ($this->ref_ref_cod_serie) {
      require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
      require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
      require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';

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
        $conteudo .= '  <span style="display: block; float: left; width: 100px;">Carga horária</span>';
        $conteudo .= '  <span style="display: block; float: left">Usar padrão do componente?</span>';
        $conteudo .= '</div>';
        $conteudo .= '<br style="clear: left" />';

        foreach ($lista as $registro) {
          $checked = '';
          $usarComponente = FALSE;

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

          $conteudo .= '<div style="margin-bottom: 10px; float: left">';
          $conteudo .= "  <label style='display: block; float: left; width: 250px'><input type=\"checkbox\" $checked name=\"disciplinas[$registro->id]\" id=\"disciplinas[]\" value=\"{$registro->id}\">{$registro}</label>";
          $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='text' name='carga_horaria[$registro->id]' value='{$cargaHoraria}' size='5' maxlength='7'></label>";
          $conteudo .= "  <label style='display: block; float: left'><input type='checkbox' name='usar_componente[$registro->id]' value='1' ". ($usarComponente == TRUE ? $checked : '') .">($cargaComponente h)</label>";
          $conteudo .= '</div>';
          $conteudo .= '<br style="clear: left" />';

          $cargaHoraria = '';
        }

        $disciplinas  = '<table cellspacing="0" cellpadding="0" border="0">';
        $disciplinas .= sprintf('<tr align="left"><td>%s</td></tr>', $conteudo);
        $disciplinas .= '</table>';
      }
      else {
        $disciplinas = 'A série/ano escolar não possui componentes curriculares cadastrados.';
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
           . '<strong>Observação:</strong> caso não defina os componentes<br />'
           . 'curriculares para a turma, esta usará a definição<br />'
           . 'da série/ano escolar da escola:'
           . '<span id="_escola_serie_componentes">%s</span>';

    $label = sprintf($label, $help);

    $this->campoRotulo('disciplinas_', $label,
      "<div id='disciplinas'>$disciplinas</div>");

    $this->campoQuebra2();

    if ($_POST['turma_modulo']) {
      $this->turma_modulo = unserialize(urldecode($_POST['turma_modulo']));
    }

    $qtd_modulo = count($this->turma_modulo) == 0 ? 1 : (count($this->turma_modulo) + 1);

    if (is_numeric($this->cod_turma) && !$_POST) {
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
            "<a href='#' onclick=\"document.getElementById('excluir_modulo').value = '{$campo["sequencial_"]}'; document.getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>",
            '', '', '');

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

    $this->campoLista('ref_cod_modulo', 'Módulo', $opcoes, $this->ref_cod_modulo,
      NULL, NULL, NULL, NULL, NULL, FALSE);

    $this->campoData('data_inicio', 'Data Início', $this->data_inicio, FALSE);
    $this->campoData('data_fim', 'Data Fim', $this->data_fim, FALSE);

    $this->campoOculto('incluir_modulo', '');

    $this->campoRotulo('bt_incluir_modulo', 'Módulo',
      "<a href='#' onclick=\"document.getElementById('incluir_modulo').value = 'S'; document.getElementById('tipoacao').value = ''; acao();\"><img src='imagens/nvp_bot_adiciona.gif' alt='adicionar' title='Incluir' border=0></a>"
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
            "<a href='#' onclick=\"document.getElementById('excluir_dia_semana').value = '{$dias_semana["dia_semana_"]}'; document.getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>",
            '', '', 'ds_hora_final_'
          );

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
      echo '<!--\nErro\nClasse clsPmieducarTurmaDiaSemana não encontrada\n-->';
      $opcoes = array('' => 'Erro na geração');
    }

    $this->campoLista('dia_semana', 'Dia Semana', $opcoes, $this->dia_semana, NULL,
      false, '', '', false, false);

    $this->campoHora('ds_hora_inicial', 'Hora Inicial', $this->ds_hora_inicial, FALSE);

    $this->campoHora('ds_hora_final', 'Hora Final', $this->ds_hora_final, FALSE);

    $this->campoOculto('incluir_dia_semana', '');

    $this->campoRotulo('bt_incluir_dia_semana', 'Dia Semana',
      "<a href='#' onclick=\"document.getElementById('incluir_dia_semana').value = 'S'; document.getElementById('tipoacao').value = ''; acao();\"><img src='imagens/nvp_bot_adiciona.gif' alt='adicionar' title='Incluir' border=0></a>"
    );

    $this->campoOculto('padrao_ano_escolar', $this->padrao_ano_escolar);

    $this->acao_enviar = 'valida()';
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

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

    // Não segue o padrao do curso
    if ($this->padrao_ano_escolar == 0) {
      $this->turma_modulo = unserialize(urldecode($this->turma_modulo));
      $this->turma_dia_semana = unserialize(urldecode($this->turma_dia_semana));

      if ($this->turma_modulo && $this->turma_dia_semana) {
        $obj = new clsPmieducarTurma(NULL, NULL, $this->pessoa_logada,
          $this->ref_ref_cod_serie, $this->ref_cod_escola,
          $this->ref_cod_infra_predio_comodo, $this->nm_turma, $this->sgl_turma,
          $this->max_aluno, $this->multiseriada, NULL, NULL, 1,
          $this->ref_cod_turma_tipo, $this->hora_inicial, $this->hora_final,
          $this->hora_inicio_intervalo, $this->hora_fim_intervalo, $this->ref_cod_regente,
          $this->ref_cod_instituicao_regente, $this->ref_cod_instituicao,
          $this->ref_cod_curso, $this->ref_ref_cod_serie_mult, $this->ref_cod_escola,
          $this->visivel, $this->turma_turno_id, $this->tipo_boletim, $this->ano);

        $cadastrou = $obj->cadastra();

        if ($cadastrou) {
          // Cadastra módulo
          foreach ($this->turma_modulo as $campo) {
            $campo['data_inicio_'] = dataToBanco($campo['data_inicio_']);
            $campo['data_fim_']    = dataToBanco($campo['data_fim_']);

            $obj = new clsPmieducarTurmaModulo($cadastrou, $campo['ref_cod_modulo_'],
              $campo['sequencial_'], $campo['data_inicio_'], $campo['data_fim_']);

            $cadastrou1 = $obj->cadastra();

            if (!$cadastrou1) {
              $this->mensagem = 'Cadastro não realizado.';
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
              $this->mensagem = 'Cadastro não realizado.';
              echo "<!--\nErro ao cadastrar clsPmieducarTurmaDiaSemana\nvalores obrigat&oacute;rios\nis_numeric( $cadastrou ) && is_numeric( {$campo["dia_semana_"]} ) && is_string( {$campo["hora_inicial_"]} ) && is_string( {$campo["hora_final_"]} )\n-->";

              return FALSE;
            }
          }

          $this->mensagem .= 'Cadastro efetuado com sucesso.';
          header('Location: educar_turma_lst.php');
          die();
        }

        $this->mensagem = 'Cadastro não realizado.';
        echo "<!--\nErro ao cadastrar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";

        return FALSE;
      }

      echo '<script type="text/javascript">alert("É necessário adicionar pelo menos 1 módulo e 1 dia da semana!")</script>';
      $this->mensagem = "Cadastro não realizado.";

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
        $this->turma_turno_id, $this->tipo_boletim, $this->ano);

      $cadastrou = $obj->cadastra();


      if ($cadastrou) {
        $this->mensagem .= 'Cadastro efetuado com sucesso.';
        header('Location: educar_turma_lst.php');
        die();
      }

      $this->mensagem = 'Cadastro não realizado.';
      echo "<!--\nErro ao cadastrar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";

      return FALSE;
    }

    $this->atualizaComponentesCurriculares(
      $this->ref_ref_cod_serie, $this->ref_cod_escola, $this->cod_turma,
      $this->disciplinas, $this->carga_horaria, $this->usar_componente
    );
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

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

    // Não segue o padrão do curso
    if ($this->padrao_ano_escolar == 0) {
      $this->turma_modulo = unserialize(urldecode($this->turma_modulo));
      $this->turma_dia_semana = unserialize(urldecode($this->turma_dia_semana));

      if ($this->turma_modulo && $this->turma_dia_semana) {
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
          $this->ano);

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
                $this->mensagem = 'Edição não realizada.';
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
                $this->mensagem = 'Edição não realizada.';
                echo "<!--\nErro ao editar clsPmieducarTurmaDiaSemana\nvalores obrigat&oacute;rios\nis_numeric( $this->cod_turma ) && is_numeric( {$campo["dia_semana_"]} ) \n-->";

                return FALSE;
              }
            }
          }
        }
        else {
          $this->mensagem = 'Edição não realizada.';
          echo "<!--\nErro ao editar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";

          return FALSE;
        }
      }
      else {
        echo '<script type="text/javascript">alert("É necessário adicionar pelo menos 1 módulo e 1 dia da semana!")</script>';
        $this->mensagem = 'Edição não realizada.';

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
        $this->visivel, $this->turma_turno_id, $this->tipo_boletim, $this->ano);

      $editou = $obj->edita();
    }

    $this->atualizaComponentesCurriculares(
      $this->ref_ref_cod_serie, $this->ref_cod_escola, $this->cod_turma,
      $this->disciplinas, $this->carga_horaria, $this->usar_componente
    );

    if ($editou) {
      $this->mensagem .= 'Edição efetuada com sucesso.';
      header('Location: educar_turma_lst.php');
      die();
    }
    else {
      $this->mensagem = 'Edição não realizada.';
      echo "<!--\nErro ao editar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";

      return FALSE;
    }
  }

  function atualizaComponentesCurriculares($codSerie, $codEscola, $codTurma, $componentes, $cargaHoraria, $usarComponente)
  {
    require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';
    $mapper = new ComponenteCurricular_Model_TurmaDataMapper();

    $componentesTurma = array();

    foreach ($componentes as $key => $value) {
      $carga = isset($usarComponente[$key]) ?
        NULL : $cargaHoraria[$key];

      $componentesTurma[] = array(
        'id'           => $value,
        'cargaHoraria' => $carga
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
          $this->mensagem .= 'Exclusão efetuada com sucesso.';
          header('Location: educar_turma_lst.php');
          die();
        }
        else {
          $this->mensagem = 'Exclusão não realizada.';
          echo "<!--\nErro ao excluir clsPmieducarTurma\nvalores obrigatorios\nif( is_numeric( $this->cod_turma ) && is_numeric( $this->pessoa_logada ) )\n-->";

          return FALSE;
        }
      }
      else
      {
        $this->mensagem = 'Exclusão não realizada.';
        echo "<!--\nErro ao excluir clsPmieducarTurma\nvalores obrigatorios\nif( is_numeric( $this->cod_turma ) && is_numeric( $this->pessoa_logada ) )\n-->";

        return FALSE;
      }
    }

    $this->mensagem = 'Exclusão não realizada.';
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
      $this->mensagem = "Não foi possivel obter um ano em andamento, por favor, inicie um ano para a escola ou desative a configuração (para série e escola) 'Bloquear cadastro de novas turmas antes de atingir limite de vagas (no mesmo turno)'.";

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
          $this->mensagem = "Não é possivel cadastrar turmas, pois ainda existem $vagas vagas em aberto na turma '{$turma['nm_turma']}' desta serie e turno.\n\nTal limitação ocorre devido definição feita para esta escola e série.";
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
    campoComodo.options[0] = new Option('A escola não possui nenhuma Sala', '', false, false);
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
      'A instituição não possui nenhum Tipo de Turma', '', false, false
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
  campoModulo.options[0] = new Option('Selecione um módulo', '', false, false);

  var DOM_modulos = xml.getElementsByTagName('item');

  for (var j = 0; j < DOM_modulos.length; j += 2) {
    campoModulo.options[campoModulo.options.length] = new Option(
      DOM_modulos[j + 1].firstChild.nodeValue, DOM_modulos[j].firstChild.nodeValue,
      false, false
    );
  }

  if (campoModulo.length == 1 && campoInstituicao != '') {
    campoModulo.options[0] = new Option(
      'A Instituição não possui nenhum módulo', '', false, false
    );
  }
}

var evtOnLoad = function()
{
  setVisibility('tr_hora_inicial',false);
  setVisibility('tr_hora_final',false);
  setVisibility('tr_hora_inicio_intervalo',false);
  setVisibility('tr_hora_fim_intervalo',false);

  // Inclui módulo
  setVisibility('tr_ref_cod_modulo',false);
  setVisibility('ref_cod_modulo',false);
  setVisibility('tr_data_inicio',false);
  setVisibility('tr_data_fim',false);
  setVisibility('tr_bt_incluir_modulo',false);

  // Inclui dia da semana
  setVisibility('tr_dia_semana',false);
  setVisibility('tr_ds_hora_inicial',false);
  setVisibility('tr_ds_hora_final',false);
  setVisibility('tr_bt_incluir_dia_semana',false);

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

  if (document.getElementById('ref_cod_curso').value) {
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
    }
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
  campoSerieMult.options[0] = new Option('Selecione uma série', '', false, false);

  var multi_serie = xml.getElementsByTagName('serie');

  if (multi_serie.length) {
    for (var i = 0; i < multi_serie.length; i++) {
      campoSerieMult.options[campoSerieMult.options.length] = new Option(
        multi_serie[i].firstChild.data, multi_serie[i].getAttribute('cod_serie'), false, false
      );
    }
  }

  if (campoSerieMult.length == 1 && campoCurso != '') {
    campoSerieMult.options[0] = new Option('O curso não possui nenhuma série', '', false, false);
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
    conteudo += '  <label span="display: block; float: left; width: 100px">Carga horária</span>';
    conteudo += '  <label span="display: block; float: left">Usar padrão do componente?</span>';
    conteudo += '</div>';
    conteudo += '<br style="clear: left" />';

    for (var i = 0; i < DOM_array.length; i++) {
      id = DOM_array[i].getAttribute("cod_disciplina");

      conteudo += '<div style="margin-bottom: 10px; float: left">';
      conteudo += '  <label style="display: block; float: left; width: 250px;"><input type="checkbox" name="disciplinas['+ id +']" id="disciplinas[]" value="'+ id +'">'+ DOM_array[i].firstChild.data +'</label>';
      conteudo += '  <label style="display: block; float: left; width: 100px;"><input type="text" name="carga_horaria['+ id +']" value="" size="5" maxlength="7"></label>';
      conteudo += '  <label style="display: block; float: left"><input type="checkbox" name="usar_componente['+ id +']" value="1">('+ DOM_array[i].getAttribute("carga_horaria") +' h)</label>';
      conteudo += '</div>';
      conteudo += '<br style="clear: left" />';
    }
  }
  else {
    campoDisciplinas.innerHTML = 'A série/ano escolar não possui componentes '
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

  setVisibility('tr_dia_semana', false);
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
  }

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
      alert("Preencha o campo 'Série' corretamente!");
      document.getElementById('ref_ref_cod_serie').focus();
      return false;
    }
  }

  if (document.getElementById('multiseriada').checked) {
    if (!document.getElementById('ref_ref_cod_serie_mult')){
      alert("Preencha o campo 'Série Multi-seriada' corretamente!");
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
      alert("Preencha o campo 'Hora Início Intervalo' corretamente!");
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
      alert("ATENÇÃO!\nÉ necessário incluir um 'Módulo'!");
      document.getElementById('ref_cod_modulo').focus();
      return false;
    }

    if (qtdDiaSemana == 1) {
      alert("ATENÇÂO! \n É necessário incluir um 'Dia da Semana'!");
      document.getElementById('dia_semana').focus();
      return false;
    }
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
        alert("ATENÇÃO!\nA 'sala' já está alocada nesse horário!\nPor favor, escolha outro horário ou sala.");
        return false;
      }
    }
  }

  if (!acao()) {
    return false;
  }

  document.forms[0].submit();
}

function validaCampoServidor()
{
  if (document.getElementById('ref_cod_instituicao').value)
    ref_cod_instituicao = document.getElementById('ref_cod_instituicao').value;
  else {
    alert('Selecione uma instituição');
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
    campoSerie.options[0].text = 'Carregando séries';

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
  campoSerie.options[0].text = 'Selecione uma série';
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
    campoSerie.options[0].text = 'A escola/curso não possui nenhuma série';
  }
}
</script>
