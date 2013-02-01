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

require_once 'Portabilis/Controller/Page/ListController.php';

class ProcessamentoController extends Portabilis_Controller_Page_ListController
{
  protected $_dataMapper = 'Avaliacao_Model_NotaAlunoDataMapper';
  protected $_titulo     = 'Processamento histórico';
  protected $_processoAp = 999613;
  protected $_formMap    = array();

  // #TODO migrar funcionalidade para novo padrão
  protected $backwardCompatibility = true;

  public function Gerar()
  {
    Portabilis_View_Helper_Application::loadStylesheet($this, '/modules/HistoricoEscolar/Static/styles/processamento.css');

    $this->inputsHelper()->dynamic(array('ano', 'instituicao', 'escola'));
    $this->inputsHelper()->dynamic(array('curso', 'serie', 'turma', 'matricula'), array('required' => false));

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

    Portabilis_View_Helper_Application::loadJQueryUiLib($this);

    Portabilis_View_Helper_Application::loadJavascript(
      $this,
      array('/modules/Portabilis/Assets/Javascripts/Utils.js',
            '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/SimpleSearch.js',
            '/modules/HistoricoEscolar/Static/scripts/processamento.js')
    );
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
}
?>