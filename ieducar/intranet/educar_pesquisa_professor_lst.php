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
  var $ref_cod_funcao;
  var $ref_cod_instituicao;
  var $professor;
  var $ref_cod_escola;
  var $nome_servidor;
  var $ref_cod_servidor;
  var $identificador;
  function Gerar()
  {
      Session::put([
          'campo1' => $_GET['campo1'] ?? Session::get('campo1'),
          'campo2' => $_GET['campo2'] ?? Session::get('campo2'),
          'ref_cod_instituicao' => $_GET['ref_cod_instituicao'] ?? Session::get('ref_cod_instituicao'),
          'ref_cod_escola' => $_GET['ref_cod_escola'] ?? Session::get('ref_cod_escola'),
          'ref_cod_servidor' => $_GET['ref_cod_servidor'] ?? Session::get('ref_cod_servidor'),
          'professor' => $_GET['professor'] ?? Session::get('professor'),
          'identificador' => $_GET['identificador'] ?? Session::get('identificador'),
      ]);

    if (!isset($_GET['tipo'])) {
        Session::forget([
            'setAllField1', 'setAllField2', 'tipo',
        ]);
    }

    $this->ref_cod_instituicao = Session::get('ref_cod_instituicao');
    $this->ref_cod_escola      = Session::get('ref_cod_escola');
    $this->ref_cod_servidor    = Session::get('ref_cod_servidor');
    $this->professor           = Session::get('professor');
    $this->identificador       = Session::get('identificador');

    if (isset($_GET['lst_matriculas']) && Session::has('lst_matriculas')) {
      $this->lst_matriculas = $_GET['lst_matriculas'] ?? Session::get('lst_matriculas');
    }

    Session::put('tipo', $_GET['tipo'] ?? Session::get('tipo'));

    $this->titulo = 'Servidores P&uacute;blicos - Listagem';
    // Passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val)  {
      $this->$var = $val === '' ? NULL : $val;
    }
    if (isset($this->lst_matriculas)) {
      $this->lst_matriculas = urldecode($this->lst_matriculas);
    }
    $this->addCabecalhos(array(
      'Nome do Servidor',
      'Matr&iacute;cula',
      'Institui&ccedil;&atilde;o'
    ));
    $this->campoTexto('nome_servidor', 'Nome Servidor', $this->nome_servidor, 30, 255, FALSE);
    $this->campoOculto('tipo', $_GET['tipo']);
    $obj_servidor = new clsPmieducarServidor();
    $obj_servidor->setOrderby('nome ASC');

    $lista_professor = false;

    if ($this->ref_cod_instituicao && $this->ref_cod_escola) {
        $lista_professor = $obj_servidor->lista_professor($this->ref_cod_instituicao, $this->ref_cod_escola, $this->nome_servidor);
    }

    // pega detalhes de foreign_keys
      $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $lista_professor[0]["ref_cod_instituicao"] );
      $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
      $nm_instituicao = $det_ref_cod_instituicao["nm_instituicao"];

    // monta a lista
    if (is_array($lista_professor) && count($lista_professor)) {
      foreach ($lista_professor as $registro) {
        $campo1 = Session::get('campo1');
        $campo2 = Session::get('campo2');
        $setAll = '';
        if (Session::get('tipo')) {
          if (is_string($campo1) && is_string($campo2)) {
            $script = " onclick=\"addVal1('{$campo1}','{$registro['cod_servidor']}', '{$registro['nome']}'); addVal1('{$campo2}','{$registro['nome']}', '{$registro['cod_servidor']}'); $setAll fecha();\"";
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
    $this->largura = '100%';
    $obj_permissoes = new clsPermissoes();

    Session::save();
    Session::start();
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
