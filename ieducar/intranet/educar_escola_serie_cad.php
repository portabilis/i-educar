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
require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Escola S&eacute;rie');
    $this->processoAp = 585;
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
 * @todo      Ver a questão de formulários que tem campos dinamicamente
 *   desabilitados de acordo com a requisição (GET, POST ou erro de validação).
 *   A forma atual de usar valores em campos hidden leva a diversos problemas
 *   como aumento da lógica de pré-validação nos métodos Novo() e Editar().
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_escola;
  var $ref_cod_escola_;
  var $ref_cod_serie;
  var $ref_cod_serie_;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $hora_inicial;
  var $hora_final;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $hora_inicio_intervalo;
  var $hora_fim_intervalo;
  var $hora_fim_intervalo_;

  var $ref_cod_instituicao;
  var $ref_cod_curso;
  var $intervalo;

  var $escola_serie_disciplina;
  var $ref_cod_disciplina;
  var $incluir_disciplina;
  var $excluir_disciplina;

  var $disciplinas;

  var $carga_horaria;

  function Inicializar()
  {
    $retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->ref_cod_serie = $_GET['ref_cod_serie'];
    $this->ref_cod_escola = $_GET['ref_cod_escola'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(585, $this->pessoa_logada, 7,
      'educar_escola_serie_lst.php');

    if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie))
    {
      $tmp_obj = new clsPmieducarEscolaSerie();
      $lst_obj = $tmp_obj->lista($this->ref_cod_escola, $this->ref_cod_serie);
      $registro = array_shift($lst_obj);

      if ($registro) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $this->fexcluir = $obj_permissoes->permissao_excluir(585,
          $this->pessoa_logada, 7);

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = ($retorno == 'Editar') ?
      sprintf('educar_escola_serie_det.php?ref_cod_escola=%d&ref_cod_serie=%d',
        $registro['ref_cod_escola'], $registro['ref_cod_serie']) :
      'educar_escola_serie_lst.php';

    $this->nome_url_cancelar = 'Cancelar';
    return $retorno;
  }

  function Gerar()
  {
    if ($_POST) {
      foreach($_POST as $campo => $val) {
        $this->$campo = ($this->$campo) ? $this->$campo : $val;
      }
    }

    if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
      $instituicao_desabilitado  = TRUE;
      $escola_desabilitado       = TRUE;
      $curso_desabilitado        = TRUE;
      $serie_desabilitado        = TRUE;
      $escola_serie_desabilitado = TRUE;

      $this->campoOculto('ref_cod_instituicao_', $this->ref_cod_instituicao);
      $this->campoOculto('ref_cod_escola_', $this->ref_cod_escola);
      $this->campoOculto('ref_cod_curso_', $this->ref_cod_curso);
      $this->campoOculto('ref_cod_serie_', $this->ref_cod_serie);
    }

    $obrigatorio      = TRUE;
    $get_escola       = TRUE;
    $get_curso        = TRUE;
    $get_serie        = FALSE;
    $get_escola_serie = TRUE;

    include 'include/pmieducar/educar_campo_lista.php';

    if ($this->ref_cod_escola_) {
      $this->ref_cod_escola = $this->ref_cod_escola_;
    }
    if ($this->ref_cod_serie_) {
      $this->ref_cod_serie = $this->ref_cod_serie_;
    }

    $opcoes_serie = array('' => 'Selecione');

    // Editar
    if ($this->ref_cod_curso) {
      $obj_serie = new clsPmieducarSerie();
      $obj_serie->setOrderby('nm_serie ASC');
      $lst_serie = $obj_serie->lista(NULL, NULL, NULL,$this->ref_cod_curso,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,1);

      if (is_array($lst_serie) && count($lst_serie)) {
        foreach ($lst_serie as $serie) {
          $opcoes_serie[$serie['cod_serie']] = $serie['nm_serie'];
        }
      }
    }

    $this->campoLista('ref_cod_serie', 'Série', $opcoes_serie, $this->ref_cod_serie,
      '', FALSE, '', '', $this->ref_cod_serie ? TRUE : FALSE);

    $this->hora_inicial          = substr($this->hora_inicial, 0, 5);
    $this->hora_final            = substr($this->hora_final, 0, 5);
    $this->hora_inicio_intervalo = substr($this->hora_inicio_intervalo, 0, 5);
    $this->hora_fim_intervalo    = substr($this->hora_fim_intervalo, 0, 5);

    // hora
    $this->campoHora('hora_inicial', 'Hora Inicial', $this->hora_inicial, TRUE);
    $this->campoHora('hora_final', 'Hora Final', $this->hora_final, TRUE);

    $this->campoHora('hora_inicio_intervalo', 'Hora In&iacute;cio Intervalo',
      $this->hora_inicio_intervalo, TRUE);

    $this->campoHora('hora_fim_intervalo', 'Hora Fim Intervalo',
      $this->hora_fim_intervalo, TRUE);

		$this->campoCheck("bloquear_enturmacao_sem_vagas", "Bloquear enturmação após atingir limite de vagas", $this->bloquear_enturmacao_sem_vagas);

		$this->campoCheck("bloquear_cadastro_turma_para_serie_com_vagas", "Bloquear cadastro de novas turmas antes de atingir limite de vagas (no mesmo turno)", $this->bloquear_cadastro_turma_para_serie_com_vagas);

    $this->campoQuebra();

    // Inclui disciplinas
    if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie)) {
      $obj = new clsPmieducarEscolaSerieDisciplina();
      $registros = $obj->lista($this->ref_cod_serie, $this->ref_cod_escola, NULL, 1);

      if ($registros) {
        foreach ($registros as $campo) {
          $this->escola_serie_disciplina[$campo['ref_cod_disciplina']] = $campo['ref_cod_disciplina'];
          $this->escola_serie_disciplina_carga[$campo['ref_cod_disciplina']] = floatval($campo['carga_horaria']);
        }
      }
    }

    $opcoes = array('' => 'Selecione');

    // Editar
    $disciplinas = 'Nenhuma série selecionada';

    if ($this->ref_cod_serie) {
      $disciplinas = '';
      $conteudo = '';

      // Instancia o mapper de ano escolar
      $anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
      $lista = $anoEscolar->findComponentePorSerie($this->ref_cod_serie);

      // Instancia o mapper de componente curricular
      $mapper = new ComponenteCurricular_Model_ComponenteDataMapper();

      if (is_array($lista) && count($lista)) {
        $conteudo .= '<div style="margin-bottom: 10px; float: left">';
        $conteudo .= '  <span style="display: block; float: left; width: 250px;">Nome</span>';
        $conteudo .= '  <span style="display: block; float: left; width: 100px;">Carga horária</span>';
        $conteudo .= '  <span style="display: block; float: left">Usar padrão do componente?</span>';
        $conteudo .= '</div>';
        $conteudo .= '<br style="clear: left" />';
        $conteudo .= '<div style="margin-bottom: 10px; float: left">';
        $conteudo .= "  <label style='display: block; float: left; width: 350px;'><input type='checkbox' name='CheckTodos' onClick='marcarCheck(".'"disciplinas[]"'.");'/>Marcar Todos</label>";
        $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='checkbox' name='CheckTodos2' onClick='marcarCheck(".'"usar_componente[]"'.");';/>Marcar Todos</label>";
        $conteudo .= '</div>';
        $conteudo .= '<br style="clear: left" />';         

        foreach ($lista as $registro) {
          $checked = '';
          $usarComponente = FALSE;

          if ($this->escola_serie_disciplina[$registro->id] == $registro->id) {
            $checked = 'checked="checked"';
          }

          if (is_null($this->escola_serie_disciplina_carga[$registro->id]) ||
            0 == $this->escola_serie_disciplina_carga[$registro->id]) {
            $usarComponente = TRUE;
          }
          else {
            $cargaHoraria = $this->escola_serie_disciplina_carga[$registro->id];
          }

          $cargaComponente = $registro->cargaHoraria;

          $conteudo .= '<div style="margin-bottom: 10px; float: left">';
          $conteudo .= "  <label style='display: block; float: left; width: 250px'><input type=\"checkbox\" $checked name=\"disciplinas[$registro->id]\" id=\"disciplinas[]\" value=\"{$registro->id}\">{$registro}</label>";
          $conteudo .= "  <label style='display: block; float: left; width: 100px;'><input type='text' name='carga_horaria[$registro->id]' value='{$cargaHoraria}' size='5' maxlength='7'></label>";
          $conteudo .= "  <label style='display: block; float: left'><input type='checkbox' id='usar_componente[]' name='usar_componente[$registro->id]' value='1' ". ($usarComponente == TRUE ? $checked : '') .">($cargaComponente h)</label>";

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

    $this->campoRotulo("disciplinas_", "Componentes curriculares",
      "<div id='disciplinas'>$disciplinas</div>");

    $this->campoQuebra();
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    /*
     * Se houve erro na primeira tentativa de cadastro, irá considerar apenas
     * os valores enviados de forma oculta.
     */
    if (isset($this->ref_cod_instituicao_)) {
      $this->ref_cod_instituicao = $this->ref_cod_instituicao_;
      $this->ref_cod_escola      = $this->ref_cod_escola_;
      $this->ref_cod_curso       = $this->ref_cod_curso_;
      $this->ref_cod_serie       = $this->ref_cod_serie_;
    }

    $anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
    $componenteAno = $anoEscolar->findComponentePorSerie($this->ref_cod_serie);

    /*
     * Se $disciplinas não for informado e o ano escolar tem componentes
     * curriculares cadastrados, retorna erro.
     */
    if (!is_array($this->disciplinas) &&
        (is_array($componenteAno) && 0 < count($componenteAno))
    ) {
      echo "<script> alert('É necessário adicionar pelo menos um componente curricular.') </script>";
      $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
      return FALSE;
    }

    $this->bloquear_enturmacao_sem_vagas = is_null($this->bloquear_enturmacao_sem_vagas) ? 0 : 1;
    $this->bloquear_cadastro_turma_para_serie_com_vagas = is_null($this->bloquear_cadastro_turma_para_serie_com_vagas) ? 0 : 1;

    $obj = new clsPmieducarEscolaSerie($this->ref_cod_escola, $this->ref_cod_serie,
      $this->pessoa_logada, $this->pessoa_logada, $this->hora_inicial,
      $this->hora_final, NULL, NULL, 1, $this->hora_inicio_intervalo,
      $this->hora_fim_intervalo, $this->bloquear_enturmacao_sem_vagas, $this->bloquear_cadastro_turma_para_serie_com_vagas);

    if ($obj->existe()) {
      $cadastrou = $obj->edita();
    }
    else {
      $cadastrou = $obj->cadastra();
    }

    if ($cadastrou) {
      if ($this->disciplinas) {
        foreach ($this->disciplinas as $key => $campo) {
          $obj = new clsPmieducarEscolaSerieDisciplina($this->ref_cod_serie,
            $this->ref_cod_escola, $campo, 1, $this->carga_horaria[$key]);

          if ($obj->existe()) {
            $cadastrou1 = $obj->edita();
          }
          else {
            $cadastrou1 = $obj->cadastra();
          }

          if (!$cadastrou1) {
            $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
            echo "<!--\nErro ao cadastrar clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo[$i]} ) \n-->";
            return FALSE;
          }
        }
      }

      $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
      header('Location: educar_escola_serie_lst.php');
      die();
    }

    $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
    echo "<!--\nErro ao cadastrar clsPmieducarEscolaSerie\nvalores obrigatorios\nis_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->pessoa_logada ) && ( $this->hora_inicial ) && ( $this->hora_final ) && ( $this->hora_inicio_intervalo ) && ( $this->hora_fim_intervalo )\n-->";
    return FALSE;
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    /*
     * Atribui valor para atributos usados em Gerar(), senão o formulário volta
     * a liberar os campos Instituição, Escola e Curso que devem ser read-only
     * quando em modo de edição
     */
    $this->ref_cod_instituicao = $this->ref_cod_instituicao_;
    $this->ref_cod_escola      = $this->ref_cod_escola_;
    $this->ref_cod_curso       = $this->ref_cod_curso_;
    $this->ref_cod_serie       = $this->ref_cod_serie_;

    $anoEscolar = new ComponenteCurricular_Model_AnoEscolarDataMapper();
    $componenteAno = $anoEscolar->findComponentePorSerie($this->ref_cod_serie);

    /**
     * @see indice#Novo();
     */
    if (!is_array($this->disciplinas) &&
        (is_array($componenteAno) && 0 < count($componenteAno))
    ) {
      echo "<script>alert('É necessário adicionar pelo menos um componente curricular.');</script>";
      $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
      return FALSE;
    }

    $this->bloquear_enturmacao_sem_vagas = is_null($this->bloquear_enturmacao_sem_vagas) ? 0 : 1;
    $this->bloquear_cadastro_turma_para_serie_com_vagas = is_null($this->bloquear_cadastro_turma_para_serie_com_vagas) ? 0 : 1;

    $obj = new clsPmieducarEscolaSerie($this->ref_cod_escola, $this->ref_cod_serie,
      $this->pessoa_logada, NULL, $this->hora_inicial, $this->hora_final,
      NULL, NULL, 1, $this->hora_inicio_intervalo, $this->hora_fim_intervalo, $this->bloquear_enturmacao_sem_vagas, $this->bloquear_cadastro_turma_para_serie_com_vagas);

    $editou = $obj->edita();
    $obj = new clsPmieducarEscolaSerieDisciplina($this->ref_cod_serie,
      $this->ref_cod_escola, $campo, 1);

    $obj->excluirTodos();

    if ($editou) {
      if ($this->disciplinas) {
        foreach ($this->disciplinas as $key => $campo) {
          if (isset($this->usar_componente[$key])) {
            $carga_horaria = NULL;
          }
          else {
            $carga_horaria = $this->carga_horaria[$key];
          }

          $obj = new clsPmieducarEscolaSerieDisciplina($this->ref_cod_serie,
            $this->ref_cod_escola, $campo, 1, $carga_horaria);

          $existe = $obj->existe();

          if ($existe) {
            $editou1 = $obj->edita();
            if (!$editou1) {
              $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
              echo "<!--\nErro ao editar clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo[$i]} ) \n-->";
              return FALSE;
            }
          }
          else {
            $cadastrou = $obj->cadastra();
            if (!$cadastrou) {
              $this->mensagem = 'Cadastro n&atilde;o realizada.<br>';
              echo "<!--\nErro ao editar clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo[$i]} ) \n-->";
              return FALSE;
            }
          }
        }
      }

      $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
      header('Location: educar_escola_serie_lst.php');
      die();
    }

    $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
    return FALSE;
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj = new clsPmieducarEscolaSerie($this->ref_cod_escola_, $this->ref_cod_serie_,
      $this->pessoa_logada,  NULL, NULL, NULL, NULL, NULL, 0);

    $excluiu = $obj->excluir();
    if ($excluiu) {
      $obj = new clsPmieducarEscolaSerieDisciplina($this->ref_cod_serie_,
        $this->ref_cod_escola_, NULL, 0);

      $excluiu1 = $obj->excluirTodos();

      if ($excluiu1) {
        $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
        header( "Location: educar_escola_serie_lst.php" );
        die();
      }
    }

    $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
    echo "<!--\nErro ao excluir clsPmieducarEscolaSerie\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_escola_ ) && is_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->pessoa_logada ) )\n-->";
    return FALSE;
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
document.getElementById('ref_cod_instituicao').onchange = function()
{
  getDuploEscolaCurso();
}

document.getElementById('ref_cod_escola').onchange = function()
{
  getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
  getSerie();
  var campoDisciplinas = document.getElementById('disciplinas');
  campoDisciplinas.innerHTML = "Nenhuma série selecionada";
}

function getDisciplina(xml_disciplina)
{
  var campoDisciplinas = document.getElementById('disciplinas');
  var DOM_array = xml_disciplina.getElementsByTagName( "disciplina" );
  var conteudo = '';

  if (DOM_array.length) {
    conteudo += '<div style="margin-bottom: 10px; float: left">';
    conteudo += '  <span style="display: block; float: left; width: 250px;">Nome</span>';
    conteudo += '  <label span="display: block; float: left; width: 100px">Carga horária</span>';
    conteudo += '  <label span="display: block; float: left">Usar padrão do componente?</span>';
    conteudo += '</div>';
    conteudo += '<br style="clear: left" />';
    conteudo += '<div style="margin-bottom: 10px; float: left">';
    conteudo += "  <label style='display: block; float: left; width: 350px;'><input type='checkbox' name='CheckTodos' onClick='marcarCheck("+'"disciplinas[]"'+");'/>Marcar Todos</label>";
    conteudo += "  <label style='display: block; float: left; width: 100px;'><input type='checkbox' name='CheckTodos2' onClick='marcarCheck("+'"usar_componente[]"'+");';/>Marcar Todos</label>";
    conteudo += '</div>';
    conteudo += '<br style="clear: left" />';    

    for (var i = 0; i < DOM_array.length; i++) {
      id = DOM_array[i].getAttribute("cod_disciplina");

      conteudo += '<div style="margin-bottom: 10px; float: left">';
      conteudo += '  <label style="display: block; float: left; width: 250px;"><input type="checkbox" name="disciplinas['+ id +']" id="disciplinas[]" value="'+ id +'">'+ DOM_array[i].firstChild.data +'</label>';
      conteudo += '  <label style="display: block; float: left; width: 100px;"><input type="text" name="carga_horaria['+ id +']" value="" size="5" maxlength="7"></label>';
      conteudo += '  <label style="display: block; float: left"><input type="checkbox" id="usar_componente[]" name="usar_componente['+ id +']" value="1">('+ DOM_array[i].getAttribute("carga_horaria") +' h)</label>';    
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

document.getElementById('ref_cod_serie').onchange = function()
{
  var campoSerie = document.getElementById('ref_cod_serie').value;

  var campoDisciplinas = document.getElementById('disciplinas');
  campoDisciplinas.innerHTML = "Carregando disciplina";

  var xml_disciplina = new ajax( getDisciplina );
  xml_disciplina.envia("educar_disciplina_xml.php?ser=" + campoSerie);
}

after_getEscola = function()
{
  getEscolaCurso();
  getSerie();

  var campoDisciplinas = document.getElementById('disciplinas');
  campoDisciplinas.innerHTML = "Nenhuma série selecionada";
};

function getSerie()
{
  var campoCurso = document.getElementById('ref_cod_curso').value;
  if (document.getElementById('ref_cod_escola')) {
    var campoEscola = document.getElementById('ref_cod_escola').value;
  }
  else if (document.getElementById('ref_ref_cod_escola')) {
    var campoEscola = document.getElementById('ref_ref_cod_escola').value;
  }

  var campoSerie  = document.getElementById('ref_cod_serie');

  campoSerie.length = 1;

  limpaCampos(4);
  if (campoEscola && campoCurso) {
    campoSerie.disabled = true;
    campoSerie.options[0].text = 'Carregando séries';

    var xml = new ajax(atualizaLstSerie);
    xml.envia("educar_serie_not_escola_xml.php?esc="+campoEscola+"&cur="+campoCurso);
  }
  else {
    campoSerie.options[0].text = 'Selecione';
  }
}

function atualizaLstSerie(xml)
{
  var campoSerie = document.getElementById('ref_cod_serie');
  campoSerie.length = 1;
  campoSerie.options[0].text = 'Selecione uma série';
  campoSerie.disabled = false;

  series = xml.getElementsByTagName('serie');
  if (series.length) {
    for(var i = 0; i < series.length; i++) {
      campoSerie.options[campoSerie.options.length] = new Option(series[i].firstChild.data,
        series[i].getAttribute('cod_serie'), false, false);
    }
  }
  else {
    campoSerie.options[0].text = 'O curso não possui nenhuma série ou todas as séries já estã associadas a essa escola';
    campoSerie.disabled = true;
  }
}

function marcarCheck(idValue) {
    // testar com formcadastro
    var contaForm = document.formcadastro.elements.length;
    var campo = document.formcadastro;
    var i;
    if (idValue == 'disciplinas[]'){
      for (i=0; i<contaForm; i++) {
          if (campo.elements[i].id == idValue) {

              campo.elements[i].checked = campo.CheckTodos.checked;
          }
      }
    }else {
      for (i=0; i<contaForm; i++) {
          if (campo.elements[i].id == idValue) {

              campo.elements[i].checked = campo.CheckTodos2.checked;
           }
       }

    }
     
} 
</script>
