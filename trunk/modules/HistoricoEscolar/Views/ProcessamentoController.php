<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
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
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

#TODO remover includes desnecessarios
require_once 'CoreExt/View/Helper/UrlHelper.php';
require_once 'CoreExt/View/Helper/TableHelper.php';
require_once 'Core/Controller/Page/ListController.php';
require_once 'App/Model/IedFinder.php';

require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

require_once 'lib/Portabilis/View/Helper/Application.php';

class ProcessamentoController extends Core_Controller_Page_ListController
{
  protected $_dataMapper = 'Avaliacao_Model_NotaAlunoDataMapper';
  protected $_titulo   = 'Processamento histórico';
  protected $_processoAp = 999613;
  protected $_formMap  = array();

  protected function setVars()
  {

    $this->ref_cod_aluno = $_GET['aluno_id'];
    $this->ref_cod_instituicao = $_GET['instituicao_id'];
    $this->ref_cod_escola = $_GET['escola_id'];
    $this->ref_cod_curso = $_GET['curso_id'];
    $this->ref_cod_turma = $_GET['turma_id'];
    $this->ref_ref_cod_serie = $this->ref_cod_serie = $_GET['serie_id'];
    $this->ano = $_GET['ano'];

    if ($this->ref_cod_aluno)
    {
      $nome_aluno_filtro = new clsPmieducarAluno();
      $nome_aluno_filtro = $nome_aluno_filtro->lista($int_cod_aluno = $this->ref_cod_aluno);
      $this->nm_aluno = $nome_aluno_filtro[0]['nome_aluno'];
    }
  }


  protected function setSelectionFields()
  {

    #variaveis usadas pelo modulo /intranet/include/pmieducar/educar_campo_lista.php
    $this->verificar_campos_obrigatorios = true;
    $this->add_onchange_events = true;

    $this->campoNumero( "ano", "Ano", date("Y"), 4, 4, true);
    $instituicao_obrigatorio = true;
    $get_escola = $escola_obrigatorio = true;
    $get_curso = true;
    $get_escola_curso_serie = true;
    $get_turma = true;
    $get_alunos_matriculados = true;
    include 'include/pmieducar/educar_campo_lista.php';
  }


function getSelectGradeCurso(){

    $db = new clsBanco();
    $sql = "select * from pmieducar.historico_grade_curso where ativo = 1";
    $db->Consulta($sql);

    $select = "<select id='grade-curso' class='obrigatorio disable-on-search clear-on-change-curso'>";
    $select .= "<option value=''>Selecione</option>";

    while ($db->ProximoRegistro()){
      $record = $db->Tupla();
      $select .= "<option value='{$record['id']}'>{$record['descricao_etapa']}</option>";
    }

    $select .= '</select>';
    return $select;
  }

  public function Gerar()
  {

    $this->setVars();
    $this->setSelectionFields();

    $this->rodape = "";

    $this->largura = '100%';

    $resourceOptionsTable = "<table id='resource-options' class='styled horizontal-expand hide-on-search disable-on-apply-changes'>

      <tr>
        <td><label for='dias-letivos'>Quantidade dias letivos *</label></td>
        <td colspan='2'><input type='text' id='dias-letivos' name='quantidade-dias-letivos' class='obrigatorio disable-on-search clear-on-change-curso validates-value-is-numeric'></input></td>
      </tr>

      <tr>
        <td><label for='grade-curso'>Grade curso *</label></td>
        <td>{$this->getSelectGradeCurso()}</td>
      </tr>

      <tr>
        <td><label for='percentual-frequencia'>% Frequ&ecirc;ncia *</label></td>
        <td>
          <select id='percentual-frequencia' class='obrigatorio disable-on-search'>
            <option value=''>Selecione</option>
            <option value='buscar-boletim'>Usar do boletim</option>
            <option value='informar-manualmente'>Informar manualmente</option>
          </select>
        </td>
        <td><input id='percentual-frequencia-manual' name='percentual-frequencia-manual' style='display:none;'></input></td>
      </tr>

      <tr>
        <td><label for='situacao'>Situa&ccedil;&atilde;o *</label></td>
        <td colspan='2'>
          <select id='situacao' class='obrigatorio disable-on-search'>
            <option value=''>Selecione</option>
            <option value='buscar-matricula'>Usar do boletim</option>
            <option value='em-andamento'>Em andamento</option>
            <option value='aprovado'>Aprovado</option>
            <option value='reprovado'>Reprovado</option>
            <option value='transferido'>Transferido</option>
          </select>
        </td>
      </tr>

      <tr>
        <td><label for='disciplinas'>Disciplinas *</label></td>
        <td>
          <select id='disciplinas' name='disciplinas' class='obrigatorio disable-on-search'>
            <option value=''>Selecione</option>
            <option value='buscar-boletim'>Usar do boletim</option>
            <option value='informar-manualmente'>Informar manualmente</option>
          </select>
        </td>
        <td>
          <table id='disciplinas-manual' style='display:none;'>
            <tr>
              <th>Nome</th>
              <th>Nota</th>
              <th>Falta</th>
              <th>A&ccedil;&atilde;o</th>
            </tr>
            <tr class='disciplina'>
              <td><input class='nome obrigatorio disable-on-search change-state-with-parent' style='display:none;'></input></td>
              <td><input class='nota' ></input></td>
              <td>
                <input class='falta validates-value-is-numeric'></input>
              </td>
              <td>
                <a class='remove-disciplina-line' href='#'>Remover</a>
              </td>
            </tr>
            <tr class='actions'>
              <td colspan='4'>
                <input type='button' class='action' id='new-disciplina-line' name='new-line' value='Adicionar nova'></input>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td><label for='notas'>Notas *</label></td>
        <td>
          <select id='notas' class='obrigatorio disable-on-search disable-and-hide-wen-disciplinas-manual'>
            <option value=''>Selecione</option>
            <option value='buscar-boletim'>Lan&ccedil;adas no boletim</option>
            <option value='AP'>AP</option>
            <option value='informar-manualmente'>Informar manualmente</option>
          </select>
        </td>
        <td><input id='notas-manual' name='notas-manual' style='display:none;'></input></td>
      </tr>

      <tr>
        <td><label for='faltas'>Faltas *</label></td>
        <td>
          <select id='faltas' class='obrigatorio disable-on-search disable-and-hide-wen-disciplinas-manual'>
            <option value=''>Selecione</option>
            <option value='buscar-boletim'>Lan&ccedil;adas no boletim</option>
            <option value='informar-manualmente'>Informar manualmente</option>
          </select>
        </td>
        <td><input id='faltas-manual' name='faltas-manual' style='display:none;'></input></td>
      </tr>

      <tr>
        <td><label for='registro'>Registro (arquivo)</label></td>
        <td colspan='2'><input type='text' id='registro' name='registro'></input></td>
      </tr>

      <tr>
        <td><label for='livro'>Livro</label></td>
        <td colspan='2'><input type='text' id='livro' name='livro'></input></td>
      </tr>

      <tr>
        <td><label for='dias-letivos'>Folha</label></td>
        <td colspan='2'><input type='text' id='folha' name='folha'></input></td>
      </tr>

      <tr>
        <td><label for='observacao'>Observa&ccedil;&atilde;o</label></td>
        <td colspan='2'><textarea id='observacao' name='observacao' cols='60' rows='5'></textarea></td>
      </tr>

      <tr>
        <td><label for='extra-curricular'>Extra curricular</label></td>
        <td colspan='2'><input type='checkbox' id='extra-curricular' name='extra-curricular'></input></td>
      </tr>

    </table>";


    $this->appendOutput($resourceOptionsTable);

    Portabilis_View_Helper_Application::loadJQueryLib($this);
    Portabilis_View_Helper_Application::loadJQueryFormLib($this);
    Portabilis_View_Helper_Application::loadJQueryUiLib($this);

    Portabilis_View_Helper_Application::loadJavascript($this, '/modules/HistoricoEscolar/Static/scripts/processamentoController.js');
    Portabilis_View_Helper_Application::loadStylesheet($this, '/modules/HistoricoEscolar/Static/styles/processamentoController.css');
  }
}
?>

