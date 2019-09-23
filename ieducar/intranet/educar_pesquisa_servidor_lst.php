<?php
/**
 * i-Educar - Sistema de gestÃ£o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de ItajaÃ­
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa Ã© software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo
 * sob os termos da LicenÃ§a PÃºblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versÃ£o 2 da LicenÃ§a, como (a seu critÃ©rio)
 * qualquer versÃ£o posterior.
 *
 * Este programa Ã© distribuÃ­Â­do na expectativa de que seja Ãºtil, porÃ©m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implÃ­Â­cita de COMERCIABILIDADE OU
 * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral
 * do GNU para mais detalhes.
 *
 * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU junto
 * com este programa; se nÃ£o, escreva para a Free Software Foundation, Inc., no
 * endereÃ§o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de ItajaÃ­ <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponÃ­vel desde a versÃ£o 1.0.0
 * @version   $Id$
 */

use Illuminate\Support\Facades\Session;

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de ItajaÃ­ <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponÃ­vel desde a versÃ£o 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  public function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor');
    $this->processoAp = '0';
    $this->renderMenu = FALSE;
    $this->renderMenuSuspenso = FALSE;
  }
}
/**
 * indice class.
 *
 * @author    Prefeitura Municipal de ItajaÃ­ <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponÃ­vel desde a versÃ£o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsListagem
{
  var $pessoa_logada;
  var $titulo;
  var $limite;
  var $offset;
  var $cod_servidor;
  var $ref_idesco;
  var $ref_cod_funcao;
  var $carga_horaria;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $horario;
  var $lst_matriculas;
  var $ref_cod_instituicao;
  var $professor;
  var $ref_cod_escola;
  var $nome_servidor;
  var $ref_cod_servidor;
  var $periodo;
  var $carga_horaria_usada;
  var $min_mat;
  var $min_ves;
  var $min_not;
  var $dia_semana;
  var $ref_cod_disciplina;
  var $ref_cod_curso;
  var $matutino   = FALSE;
  var $vespertino = FALSE;
  var $noturno    = FALSE;
  var $identificador;
  var $ano_alocacao;
  function Gerar()
  {
      Session::put([
          'campo1' => $_GET['campo1'] ?? Session::get('campo1'),
          'campo2' => $_GET['campo2'] ?? Session::get('campo2'),
          'dia_semana' => $_GET['dia_semana'] ?? Session::get('dia_semana'),
          'hora_inicial' => $_GET['hora_inicial'] ?? Session::get('hora_inicial'),
          'hora_final' => $_GET['hora_final'] ?? Session::get('hora_final'),
          'professor' => $_GET['professor'] ?? Session::get('professor'),
          'horario' => $_GET['horario'] ?? Session::get('horario'),
          'ref_cod_escola' => $_GET['ref_cod_escola'] ?? Session::get('ref_cod_escola'),
          'min_mat' => $_GET['min_mat'] ?? Session::get('min_mat'),
          'min_ves' => $_GET['min_ves'] ?? Session::get('min_ves'),
          'min_not' => $_GET['min_not'] ?? Session::get('min_not'),
          'ref_cod_disciplina' => $_GET['ref_cod_disciplina'] ?? Session::get('ref_cod_disciplina'),
          'ref_cod_curso' => $_GET['ref_cod_curso'] ?? Session::get('ref_cod_curso'),
          'ano_alocacao' => $_GET['ano_alocacao'] ?? Session::get('ano_alocacao'),
          'identificador' => $_GET['identificador'] ?? Session::get('identificador'),
          'lst_matriculas' => $_GET['lst_matriculas'] ?? Session::get('lst_matriculas'),
          'ref_cod_instituicao' => $_GET['ref_cod_instituicao'] ? $_GET['ref_cod_instituicao'] : Session::get('ref_cod_instituicao'),
          'ref_cod_servidor' => $_GET['ref_cod_servidor'] ? $_GET['ref_cod_servidor'] : Session::get('ref_cod_servidor'),
      ]);

      if (!isset($_GET['tipo'])) {
          Session::forget([
              'setAllField1',
              'setAllField2',
              'tipo',
          ]);
      }

      $this->ref_cod_escola      = Session::get('ref_cod_escola');
      $this->ref_cod_instituicao = Session::get('ref_cod_instituicao');
      $this->ref_cod_servidor    = Session::get('ref_cod_servidor');
      $this->professor           = Session::get('professor');
      $this->horario             = Session::get('horario');
      $this->min_mat             = Session::get('min_mat');
      $this->min_ves             = Session::get('min_ves');
      $this->min_not             = Session::get('min_not');
      $this->ref_cod_disciplina  = Session::get('ref_cod_disciplina');
      $this->ref_cod_curso       = Session::get('ref_cod_curso');
      $this->identificador       = Session::get('identificador');
      $this->ano_alocacao        = Session::get('ano_alocacao');
      $this->lst_matriculas        = Session::get('lst_matriculas');

      Session::put('tipo', $_GET['tipo'] ?? Session::get('tipo'));

    $this->titulo = 'Servidores P&uacute;blicos - Listagem';
    // Passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val)  {
      $this->$var = $val === '' ? NULL : $val;
    }
    if (isset($this->lst_matriculas)) {
      $this->lst_matriculas = urldecode($this->lst_matriculas);
    }
    $string1 = ($this->min_mat - floor($this->min_mat / 60) * 60);
    $string1 = str_repeat(0, 2 - strlen($string1)).$string1;
    $string2 = floor($this->min_mat / 60);
    $string2 = str_repeat(0, 2 - strlen($string2)).$string2;
    $hr_mat  = $string2.':'.$string1;
    $string1 = ($this->min_ves - floor($this->min_ves / 60) * 60);
    $string1 = str_repeat(0, 2 - strlen($string1)).$string1;
    $string2 = floor($this->min_ves / 60);
    $string2 = str_repeat(0, 2 - strlen($string2)).$string2;
    $hr_ves  = $string2.':'.$string1;
    $string1 = ($this->min_not - floor($this->min_not / 60) * 60);
    $string1 = str_repeat(0, 2 - strlen($string1)).$string1;
    $string2 = floor($this->min_not / 60);
    $string2 = str_repeat(0, 2 - strlen($string2)).$string2;
    $hr_not  = $string2.':'.$string1;
    $hora_inicial_ = explode(':', Session::get('hora_inicial'));
    $hora_final_   = explode(':', Session::get('hora_final'));
    $horas_ini     = sprintf('%02d', (int) abs($hora_final_[0]) - abs($hora_inicial_[0]));
    $minutos_ini   = sprintf('%02d', (int) abs($hora_final_[1]) - abs($hora_inicial_[1]));
    $h_m_ini = ($hora_inicial_[0] * 60) + $hora_inicial_[1];
     $h_m_fim = ($hora_final_[0]   * 60) + $hora_final_[1];
    if ($h_m_ini >= 480 && $h_m_ini <= 720) {
      $this->matutino = TRUE;
      if ($h_m_fim >= 721 && $h_m_fim <= 1080) {
        $this->vespertino = TRUE;
      }
      elseif (($h_m_fim >= 1801 && $h_m_fim <= 1439) || ($h_m_fim == 0)) {
        $this->noturno = TRUE;
      }
    }
    elseif ($h_m_ini >= 721 && $h_m_ini <= 1080) {
      $this->vespertino = TRUE;
      if (($h_m_fim >= 1081 && $h_m_fim <= 1439)) {
        $this->noturno = TRUE;
      }
    }
    elseif (($h_m_ini >= 1081 && $h_m_ini <= 1439) || ($h_m_ini == 0)) {
      $this->noturno = TRUE;
    }
    $this->addCabecalhos(array(
      'Nome do Servidor',
      'Matr&iacute;cula',
      'Institui&ccedil;&atilde;o'
    ));
    $this->campoTexto('nome_servidor', 'Nome Servidor', $this->nome_servidor, 30, 255, FALSE);
    $this->campoOculto('tipo', $_GET['tipo']);
    // Paginador
    $this->limite = 20;
    $this->offset = ($_GET['pagina_{$this->nome}']) ? $_GET['pagina_{$this->nome}'] * $this->limite-$this->limite: 0;
    $obj_servidor = new clsPmieducarServidor();
    $obj_servidor->setOrderby('carga_horaria ASC');
    $obj_servidor->setLimite($this->limite, $this->offset);
    if (Session::has(['dia_semana', 'hora_inicial', 'hora_final'])) {
      $array_hora = [
          Session::get('dia_semana'),
          Session::get('hora_inicial'),
          Session::get('hora_final')
      ];
    }
    // Marca a disciplina como NULL se nÃ£o for informada, restringindo a busca
    // aos professores e nÃ£o selecionar aqueles em que o curso nÃ£o seja
    // globalizado e sem disciplinas cadastradas
    $this->ref_cod_disciplina = $this->ref_cod_disciplina ?
      $this->ref_cod_disciplina : NULL;
    // Passa NULL para $alocacao_escola_instituicao senÃ£o o seu filtro anula
    // um anterior (referente a selecionar somente servidores nÃ£o alocados),
    // selecionando apenas servidores alocados na instituiÃ§Ã£o
    $lista = $obj_servidor->lista(
      NULL,
      NULL,
      $this->ref_idesco,
      $this->carga_horaria,
      NULL,
      NULL,
      NULL,
      NULL,
      1,
      $this->ref_cod_instituicao,
      Session::get('tipo'),
      $array_hora,
      $this->ref_cod_servidor,
      $this->nome_servidor,
      TRUE,
      $this->horario,
      FALSE,
      $this->lst_matriculas,
      $this->matutino,
      $this->vespertino,
      $this->noturno,
      $this->ref_cod_escola,
      $hr_mat,
      $hr_ves,
      $hr_not,
      Session::get('dia_semana'),
      $this->ref_cod_escola,
      $this->identificador,
      $this->ref_cod_curso,
      $this->ref_cod_disciplina,
      NULL,
      NULL,
      $this->ano_alocacao
    );

    // Se for uma listagem de professores, recupera as disciplinas dadas para
    // comparaÃ§Ã£o com a de outros professores (somente quando a busca Ã© para
    // substituiÃ§Ã£o de servidores)
    $disciplinas = array();
    if ('true' == $this->professor) {
      $disciplinas = $obj_servidor->getServidorDisciplinasQuadroHorarioHorarios(
        $this->ref_cod_servidor, $this->ref_cod_instituicao);
    }
    $total = $obj_servidor->_total;
    // pega detalhes de foreign_keys
      $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $lista[0]["ref_cod_instituicao"] );
      $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
      $nm_instituicao = $det_ref_cod_instituicao["nm_instituicao"];

    // monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $obj_cod_servidor      = new clsFuncionario( $registro['cod_servidor'] );
        $det_cod_servidor      = $obj_cod_servidor->detalhe();
        $registro['matricula'] = $det_cod_servidor['matricula'];
        // Se servidor for professor, verifica se possui as mesmas
        // disciplinas do servidor a ser substituido (este passo somente Ã©
        // executado ao buscar um servidor substituto)
        if ($this->professor == 'true') {
          $disciplinasSubstituto = clsPmieducarServidor::getServidorDisciplinas(
            $registro['cod_servidor'], $this->ref_cod_instituicao);
          // Se os arrays diferirem, passa para o prÃ³ximo resultado
          if ($disciplinasSubstituto != $disciplinas) {
            continue;
          }
        }
        $campo1 = Session::get('campo1');
        $campo2 = Session::get('campo2');
        if (Session::get('tipo')) {
          if (is_string($campo1) && is_string($campo2)) {
            if (is_string( Session::get('horario'))) {
              $script = " onclick=\"addVal1('{$campo1}','{$registro['nome']}','{$registro['cod_servidor']}'); addVal1('{$campo2}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
            }
            else {
              $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}', '{$registro['nome']}'); addVal1('{$campo2}','{$registro['nome']}', '{$registro['cod_servidor']}'); $setAll fecha();\"";
            }
          }
          elseif (is_string($campo1)) {
            $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
          }
        }
        else {
          if (is_string($campo1) && is_string($campo2)) {
            $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); addVal1('{$campo2}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
          }
          elseif (is_string($campo2)) {
            $script = " onclick=\"addVal1('{$campo2}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
          }
          elseif (is_string($campo1)) {
            $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}','{$registro['nome']}'); $setAll fecha();\"";
          }
        }
        $this->addLinhas(array(
          "<a href=\"javascript:void(0);\" $script>{$registro["nome"]}</a>",
          "<a href=\"javascript:void(0);\" $script>{$registro["matricula"]}</a>",
          "<a href=\"javascript:void(0);\" $script>{$nm_instituicao}</a>"
        ) );
      }
    }
    $this->addPaginador2('educar_pesquisa_servidor_lst.php', $total, $_GET,
      $this->nome, $this->limite);
    $obj_permissoes = new clsPermissoes();
    $this->largura = '100%';
  }
}
// Instancia objeto de pÃ¡gina
$pagina = new clsIndexBase();
// Instancia objeto de conteÃºdo
$miolo = new indice();
// Atribui o conteÃºdo Ã   pÃ¡gina
$pagina->addForm($miolo);
// Gera o cÃ³digo HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
function addVal1(campo,opcao, valor)
{
  if (window.parent.document.getElementById(campo)) {
    if (window.parent.document.getElementById(campo).type == 'select-one') {
      obj                     = window.parent.document.getElementById(campo);
      novoIndice              = obj.options.length;
      obj.options[novoIndice] = new Option(valor);
      valor                   = obj.options[novoIndice];
      valor.value             = opcao.toString();
      valor.selected          = true;
      obj.onchange();
    }
    else if (window.parent.document.getElementById(campo)) {
      obj       =  window.parent.document.getElementById(campo);
      obj.value = valor;
    }
  }
}
function fecha()
{
  window.parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));
}
function setAll(field,value)
{
  var elements = window.parent.document.getElementsByName(field);
  for (var ct = 0;ct < elements.length; ct++) {
    elements[ct].value = value;
  }
}
function clearAll()
{
  var elements = window.parent.document.getElementsByName('ref_cod_servidor_substituto');
  for (var ct = 0;ct < elements.length;ct++) {
    elements[ct].value = '';
  }
  for (var ct =0;ct < num_alocacao;ct++) {
    var elements = window.parent.document.getElementById('ref_cod_servidor_substituto_' + ct).value='';
  }
}
function getArrayHora(hora)
{
  var array_h;
  if(hora) {
    array_h = hora.split(':');
  }
  else {
    array_h = new Array(0,0);
  }
  return array_h;
}
</script>
