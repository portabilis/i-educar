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
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor Disciplina');
    $this->processoAp         = 0;
    $this->renderBanner       = FALSE;
    $this->renderMenu         = FALSE;
    $this->renderMenuSuspenso = FALSE;
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

  var $cod_servidor;
  var $ref_cod_instituicao;
  var $ref_cod_deficiencia;
  var $ref_idesco;
  var $ref_cod_funcao;
  var $carga_horaria;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_cod_curso;
  var $ref_cod_disciplina;
  var $cursos_disciplina;

  function Inicializar()
  {
    $retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->cod_servidor = $_GET['ref_cod_servidor'];
    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $obj_permissoes = new clsPermissoes();

    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7,
      'educar_servidor_lst.php');

    if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
      $obj = new clsPmieducarServidor($this->cod_servidor, NULL, NULL, NULL,
        NULL, NULL, NULL, $this->ref_cod_instituicao);

      $registro  = $obj->detalhe();
      if ($registro) {
        $retorno = 'Editar';
      }
    }

    @session_start();
    $this->cursos_disciplina = $_SESSION['cursos_disciplina'];
    @session_write_close();

    if (!$this->cursos_disciplina) {
      $obj_servidor_disciplina = new clsPmieducarServidorDisciplina();
      $lst_servidor_disciplina = $obj_servidor_disciplina->lista(NULL,
        $this->ref_cod_instituicao, $this->cod_servidor);

      if ($lst_servidor_disciplina) {
        foreach ($lst_servidor_disciplina as $disciplina) {
          $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
          $componente = $componenteMapper->find($disciplina['ref_cod_disciplina']);

          $this->cursos_disciplina[$disciplina['ref_cod_curso']][$disciplina['ref_cod_disciplina']] = $disciplina['ref_cod_disciplina'];
        }
      }
    }

    if ($this->cursos_disciplina) {
      foreach ($this->cursos_disciplina as $curso => $disciplinas) {
        if ($disciplinas) {
          foreach ($disciplinas as $disciplina) {
            $this->ref_cod_curso[] = $curso;
            $this->ref_cod_disciplina[] = $disciplina;
          }
        }
      }
    }

    return $retorno;
  }

  function Gerar()
  {
    $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);
    $opcoes = $opcoes_curso = array('' => 'Selecione');

    $obj_cursos = new clsPmieducarCurso();
    $obj_cursos->setOrderby('nm_curso');
    $lst_cursos = $obj_cursos->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, NULL,1, NULL, $this->ref_cod_instituicao);

    if ($lst_cursos) {
      foreach ($lst_cursos as $curso) {
        $opcoes_curso[$curso['cod_curso']] = $curso['nm_curso'];
      }
    }

    $obj_disciplina = new clsPmieducarDisciplina();
    $obj_disciplina->setOrderby('nm_disciplina');
    $lst_opcoes = array();
    $arr_valores = array();


    if ($this->cursos_disciplina) {
      foreach ($this->cursos_disciplina as $curso => $disciplinas) {
        if ($disciplinas) {
          foreach ($disciplinas as $disciplina) {
            $arr_valores[] = array($curso, $disciplina);
          }
        }
      }
    }

    if ($this->ref_cod_curso) {
      foreach ($this->ref_cod_curso as $curso) {
        $lst_disciplinas = $obj_disciplina->lista(NULL, NULL, NULL, NULL, NULL,
          NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, $curso,
          $this->ref_cod_instituicao);

        $componenteAnoDataMapper = new ComponenteCurricular_Model_AnoEscolarDataMapper();
        $componentes = $componenteAnoDataMapper->findComponentePorCurso($curso);

        $opcoes_disc = array();
        foreach ($componentes as $componente) {
          $opcoes_disc[$componente->id]  = $componente->nome;
        }

        $lst_opcoes[] = array($opcoes_curso, $opcoes_disc);
      }
    }

    $this->campoTabelaInicio('funcao', 'Componentes Curriculares', array('Curso', 'Componente Curricular'),
      $arr_valores, '', $lst_opcoes);

    // Cursos
    $this->campoLista('ref_cod_curso', 'Curso', $opcoes_curso,
      $this->ref_cod_curso, 'trocaCurso(this)', '', '', '');

    // Disciplinas
    $this->campoLista('ref_cod_disciplina', 'Componente Curricular', $opcoes,
      $this->ref_cod_disciplina, '', '', '', '');

    $this->campoTabelaFim();
  }

  public function Novo()
  {
    $cursos_disciplina = array();

    @session_start();
    $curso_servidor = $_SESSION['cursos_servidor'];

    if ($this->ref_cod_curso) {
      for ($i = 0, $loop = count($this->ref_cod_curso); $i < $loop; $i++) {
        $curso = $this->ref_cod_curso[$i];
        $curso_servidor[$curso] = $curso;
        $disciplina = $this->ref_cod_disciplina[$i];
        $cursos_disciplina[$curso][$disciplina] = $disciplina;
      }
    }

    $_SESSION['cursos_disciplina'] = $cursos_disciplina;
    $_SESSION['cod_servidor']      = $this->cod_servidor;
    $_SESSION['cursos_servidor']   = $curso_servidor;
    @session_write_close();

    echo "<script>parent.fechaExpansivel('{$_GET['div']}');</script>";
    die;
  }

  public function Editar() {
    return $this->Novo();
  }

  function Excluir()
  {
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
  function trocaCurso(id_campo)
  {
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    var campoCurso = document.getElementById(id_campo.id).value;
    var id = /[0-9]+/.exec(id_campo.id);
    var campoDisciplina = document.getElementById('ref_cod_disciplina['+id+']');
    campoDisciplina.length = 1;

    if (campoDisciplina) {
      campoDisciplina.disabled = true;
      campoDisciplina.options[0].text = 'Carregando Disciplinas';

      var xml = new ajax(atualizaLstDisciplina,'ref_cod_disciplina['+id+']');
      xml.envia("educar_disciplina_xml.php?cur="+campoCurso);
    }
    else {
      campoFuncao.options[0].text = 'Selecione';
    }
  }

  function atualizaLstDisciplina(xml)
  {
    var campoDisciplina = document.getElementById(arguments[1]);

    campoDisciplina.length = 1;
    campoDisciplina.options[0].text = 'Selecione uma Disciplina';
    campoDisciplina.disabled = false;

    var disciplinas = xml.getElementsByTagName('disciplina');

    if (disciplinas.length) {
      for (var i = 0; i < disciplinas.length; i++) {
        campoDisciplina.options[campoDisciplina.options.length] =
          new Option(disciplinas[i].firstChild.data, disciplinas[i].getAttribute('cod_disciplina'), false, false);
      }
    }
    else {
      campoDisciplina.options[0].text = 'A instituição não possui nenhuma disciplina';
    }
  }

  tab_add_1.afterAddRow = function () { }

  window.onload = function()
  {
  }

  function trocaTodasfuncoes()
  {
    for (var ct = 0; ct < tab_add_1.id; ct++) {
      getFuncao('ref_cod_funcao['+ct+']');
    }
  }

  function acao2()
  {
    var total_horas_alocadas = getArrayHora(document.getElementById('total_horas_alocadas').value);
    var carga_horaria = (document.getElementById('carga_horaria').value).replace(',', '.');

    if (parseFloat(total_horas_alocadas) > parseFloat(carga_horaria)) {
      alert('Atenção, carga horária deve ser maior que horas alocadas!');
      return false;
    }
    else {
      acao();
    }
  }

  if (document.getElementById('total_horas_alocadas')) {
    document.getElementById('total_horas_alocadas').style.textAlign = 'right';
  }
</script>