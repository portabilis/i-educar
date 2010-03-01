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
 * @package   iEd_Menu
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/imagem/clsPortalImagem.inc.php';

/**
 * clsIndex class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Menu
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Menu Suspenso');
    $this->processoAp = "445";
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Menu
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $id_pessoa;
  var $cod_menu;
  var $ref_cod_menu_submenu;
  var $ref_cod_menu_pai;
  var $tt_menu;
  var $ico_menu;
  var $ord_menu;
  var $caminho;
  var $alvo;
  var $suprime_menu;
  var $ref_cod_tutormenu;
  var $id_deletar;
  var $ref_cod_menu;
  var $saida;

  function Inicializar()
  {
     $retorno = "Editar";

     @session_start();
     $this->id_pessoa = $_SESSION['id_pessoa'];
     @session_write_close();

     $this->cod_menu = $_GET['cod_menu'];

     if ($this->cod_menu && !$_POST) {
       @session_start();
       unset($_SESSION['menu_suspenso']);
       $obj = new clsMenuSuspenso();
       $lista  = $obj->listaNivel($this->cod_menu,$this->id_pessoa);

       if ($lista) {
           foreach ($lista as $menu) {
             $_SESSION['menu_suspenso'][] = array(
               'ref_cod_menu_pai'     => $menu['ref_cod_menu_pai'],
               'cod_menu'             => $menu['cod_menu'],
               'ref_cod_menu_submenu' => $menu['ref_cod_menu_submenu'],
               'tt_menu'              => $menu['tt_menu'],
               'ico_menu'             => $menu['ref_cod_ico'],
               'ord_menu'             => $menu['ord_menu'],
               'caminho'              => $menu['caminho'],
               'alvo'                 => $menu['alvo'],
               'suprime_menu'         => $menu['suprime_menu'],
               'ref_cod_tutor_menu'   => $_GET['cod_menu'],
               'menu_menu_pai'        => $menu['menu_menu_pai']
             );
           }
      }

      if ($_SESSION['menu_suspenso']) {
        foreach ($_SESSION['menu_suspenso'] as $id => $valor) {
          foreach ($_SESSION['menu_suspenso'] as $id2 => $valor2) {
            if($valor2['ref_cod_menu_pai'] == $valor['cod_menu']) {
              $_SESSION['menu_suspenso'][$id2]['ref_cod_menu_pai'] = $id;
            }
          }
        }
      }

      @session_write_close();
     }

     if ($_FILES['ico_menu']['name']) {
       $caminho  = "imagens/banco_imagens/";
       $nome_do_arquivo = $_FILES['ico_menu']['name'];
       $extensao = substr($_FILES['ico_menu']['name'], -3);

       $objImagem = new clsPortalImagem(FALSE, 1, 'ico_menu', FALSE,
         $extensao, FALSE, FALSE, FALSE, $this->id_pessoa);

       if ($cod_imagem = $objImagem->cadastra()) {
         $this->ico_menu = $cod_imagem;
         echo '<script>alert("Ícone inserido!");</script>';
       }
     }

     if (isset($_POST['id_deletar']) && $_POST['id_deletar'] != "" && $_POST['editando'] == 2) {
       @session_start();
       foreach ($_SESSION['menu_suspenso'] as $id => $ref_pai) {
         if ($ref_pai['ref_cod_menu_pai'] == $_POST['id_deletar']) {
           $arr_del[] = $id;
         }
       }

       if ($arr_del) {
         foreach ($arr_del as $indice) {
           unset($_SESSION['menu_suspenso'][$indice]);
         }
       }

       if ($_POST['id_deletar'] == 0) {
         unset($_SESSION['menu_suspenso'][0]);
       }
       else {
         unset($_SESSION['menu_suspenso'][$_POST['id_deletar']]);
       }
     }
     elseif (!$_POST['lista'] && $_POST) {
       @session_start();
       if ($_POST['ref_cod_menu_submenu']) {
         $db1 = new clsBanco();
         $cod_submenu = @$_POST['ref_cod_menu_submenu'];
         $db1->Consulta("SELECT arquivo FROM menu_submenu WHERE cod_sistema = 2 AND cod_menu_submenu = {$cod_submenu} ");
         while ($db1->ProximoRegistro()) {
           list ($caminho) = $db1->Tupla();
         }
       }

       if ($_POST['editando'] == 1) {
         if (($_POST['tt_menu'] != '' && $_POST['ord_menu'] != '')
              || ($_POST['ord_menu'] == '0' && $_POST['tt_menu'] != '')
              || ($_POST['tt_menu'] != '' && $_POST['ord_menu']  == '0')
         ) {
           $db1 = new clsBanco();

           if($_POST['ref_cod_menu_submenu']) {
             $menu_menu_pai = $db1->CampoUnico("SELECT ref_cod_menu_pai FROM menu_menu mm, menu_submenu ms WHERE mm.cod_menu_menu = ms.ref_cod_menu_menu and ms.cod_menu_submenu = {$_POST['ref_cod_menu_submenu']}");
           }

           $icone = ($_POST['img_banco']) ? @$_POST['img_banco'] : $cod_imagem;
           $_SESSION['menu_suspenso'][$_POST['editar']]  = array(
             'ref_cod_menu_pai'     => $_POST['ref_cod_menu_pai'],
             'cod_menu'             => $this->cod_menu,
             'ref_cod_menu_submenu' => $_POST['ref_cod_menu_submenu'],
             'tt_menu'              => $_POST['tt_menu'],
             'ico_menu'             => $icone,
             'ord_menu'             => $_POST['ord_menu'],
             'caminho'              => $caminho,
             'alvo'                 => $_POST['alvo'],
             'suprime_menu'         => $_POST['suprime_menu'],
             'ref_cod_tutor_menu'   => $_GET['cod_menu'],
             'menu_menu_pai'        => $menu_menu_pai
           );
         }
         else {
           echo '<script>alert("Os campos Ordem e Título são obrigatórios!");</script>';
         }
       }
       else {
         if (($_POST['tt_menu'] != '' && $_POST['ord_menu'] != '')
              || ($_POST['ord_menu'] == '0' && $_POST['tt_menu'] != '')
              || ($_POST['tt_menu'] != '' && $_POST['ord_menu']  == '0')
         ) {
           $db1 = new clsBanco();
           if ($_POST['ref_cod_menu_submenu']) {
             $menu_menu_pai = $db1->CampoUnico("SELECT ref_cod_menu_pai FROM menu_menu mm, menu_submenu ms WHERE mm.cod_menu_menu = ms.ref_cod_menu_menu and ms.cod_menu_submenu = {$_POST['ref_cod_menu_submenu']}");
           }

           $icone = ($_POST['img_banco']) ? @$_POST['img_banco'] : $cod_imagem;

           $_SESSION['menu_suspenso'][]  = array(
             'ref_cod_menu_pai'     => $_POST['ref_cod_menu_pai'],
             'cod_menu'             => $this->cod_menu,
             'ref_cod_menu_submenu' => $_POST['ref_cod_menu_submenu'],
             'tt_menu'              => $_POST['tt_menu'],
             'ico_menu'             => $icone,
             'ord_menu'             => $_POST['ord_menu'],
             'caminho'              => $caminho,
             'alvo'                 => $_POST['alvo'],
             'suprime_menu'         => $_POST['suprime_menu'],
             'ref_cod_tutor_menu'   => $_GET['cod_menu'],
             'menu_menu_pai'        => $menu_menu_pai
           );
         }
         else {
           echo '<script>alert("Os campos Ordem e Título são obrigatórios!");</script>';
         }
       }

       @session_write_close();
     }

     if ($_SESSION['menu_suspenso']) {
       $this->saida ="<script>";

       foreach ($_SESSION['menu_suspenso'] as $key=>$detalhe) {
         $ico_menu = '';

         if (is_numeric($detalhe['ico_menu'])) {
           $db=  new clsBanco();
           $db->Consulta("SELECT caminho FROM portal.imagem WHERE cod_imagem = {$detalhe['ico_menu']} ");

           if($db->ProximoRegistro()) {
             list($ico_menu) = $db->Tupla();
             $ico_menu = 'imagens/banco_imagens/' . $ico_menu;
           }
         }

         $this->saida .= "array_menu[array_menu.length] = new Array(\"{$detalhe['tt_menu']}\",{$key},'{$detalhe['ref_cod_menu_pai']}','', '{$ico_menu}', '', '','MenuCarregaDados({$key},\'{$detalhe['ord_menu']}\',\'{$detalhe['ref_cod_menu_pai']}\',\'{$detalhe['ref_cod_menu']}\',\'{$detalhe['ref_cod_menu_submenu']}\',\'{$detalhe['tt_menu']}\',\'{$detalhe['ico_menu']}\',\'{$detalhe['alvo']}\',\'{$detalhe['suprime_menu']}\');');";
         if (empty($detalhe['ref_cod_menu_pai']) && $detalhe['ref_cod_menu_pai'] != '0') {
           $this->saida .= "array_id[array_id.length] = {$key};";
         }
       }

      $this->saida .="</script>";
    }

    return $retorno;
  }

  function Gerar()
  {
    $this->url_cancelar = $this->cod_menu ?
      'menu_suspenso_det.php?cod_menu=' . $this->cod_menu : 'menu_suspenso_lst.php';

    $this->nome_url_cancelar = 'Cancelar';

    if ($_POST['lista']) {
      $this->ref_cod_menu = $_POST['ref_cod_menu'];
      $this->ref_cod_menu_pai = $_POST['ref_cod_menu_pai'];
      $this->ord_menu = $_POST['ord_menu'];
    }

    $this->campoOculto('id_deletar', $this->id_deletar);
    $this->campoRotulo('menu', 'Menu', '<div id="teste_menu"></div>' . $this->saida);

    $this->campoOculto('todos_tipos', serialize($this->todos_tipos));

    if ($this->ref_cod_menu) {
      $where = "AND ref_cod_menu_menu = '{$this->ref_cod_menu}'";
    }

    $cod_menu = NULL;

    if ($_POST['ref_cod_menu']) {
      $cod_menu = $_POST['ref_cod_menu'];
      $where = "AND ref_cod_menu_menu = '{$_POST['ref_cod_menu']}'";
    }

    if ($_GET &&  $_SESSION['menu_suspenso']) {
      foreach ($_SESSION['menu_suspenso'] as $id => $value) {
        $menu = $value['ref_cod_menu_submenu'];
        $menu_pai = $value['menu_menu_pai'];

        if ($menu) {
          $db = new clsBanco();
          $db->Consulta("
                  SELECT
                    ref_cod_menu_menu, nm_menu
                  FROM
                    menu_submenu, menu_menu
                  WHERE
                    cod_menu_menu = ref_cod_menu_menu
                     AND cod_menu_submenu = {$menu}"
          );

          if ($db->ProximoRegistro()) {
            list($cod_sub, $nm_sub) = $db->Tupla();
            $cod_menu = $cod_sub;
            $where = " AND ref_cod_menu_menu = '{$cod_sub}' ";
          }
        }
      }
    }

    if ($cod_menu) {
      $db = new clsBanco();
      $num_rows = $db->Consulta("SELECT cod_menu_menu, nm_menu FROM menu_menu WHERE ref_cod_menu_pai = '{$cod_menu}'");

      if (pg_num_rows($num_rows)) {
        $db->ProximoRegistro();
        list($cod_sub, $nm_sub) = $db->Tupla();
        $where_filho = " AND ref_cod_menu_menu = '{$cod_sub}' ";

        reset($_SESSION['menu_suspenso']);
        $menu_suspenso_filho = "";

        if($_SESSION['menu_suspenso']) {
          foreach ($_SESSION['menu_suspenso'] as $campo)  {
            if (!empty($campo['ref_cod_menu_submenu']) && $campo['menu_menu_pai']) {
              $AND = 'AND';
              $menu_suspenso_filho .= " {$AND} cod_menu_submenu <> '{$campo['ref_cod_menu_submenu']}'";
            }
          }
        }

        $union = " UNION
                     SELECT
                       cod_menu_submenu, nm_submenu, 1
                     FROM
                       menu_submenu
                     WHERE
                       cod_sistema = 2 $where_filho $menu_suspenso_filho $menu_suspenso";
      }
      else {
        $union = " ORDER BY 3,nm_submenu ";
      }
    }

    $menu_suspenso = "";
    if (!$where) {
      $AND = '';
    }
    else {
      $AND = "AND";
    }

    if ($_SESSION['menu_suspenso']) {
      reset($_SESSION['menu_suspenso']);
      foreach ($_SESSION['menu_suspenso'] as $campo) {
        if (!empty($campo['ref_cod_menu_submenu'])) {
          $AND = "AND";
          $menu_suspenso .= " {$AND} cod_menu_submenu <> '{$campo['ref_cod_menu_submenu']}'";
        }
      }
    }

    $opcoes_submenu  = array();
    $opcoes_submenu['0'] = 'Selecione';
    $db1 = new clsBanco();
    $db1->Consulta("SELECT cod_menu_submenu, nm_submenu, 0 FROM menu_submenu WHERE cod_sistema = 2 $where $menu_suspenso "
        . $union);

    while ($db1->ProximoRegistro()) {
      list($cod_menu_submenu, $nm_menu_submenu) = $db1->Tupla();
      $opcoes_submenu[$cod_menu_submenu] = $nm_menu_submenu;
    }

    $obj_tutormenu = new clsTutormenu();
    $lista_tutormenu = $obj_tutormenu->lista();

    $opcoes_tutormenu = array('0' => 'Selecione');

    if ($lista_tutormenu) {
      foreach ($lista_tutormenu as $tutormenu) {
        $opcoes_tutormenu[$tutormenu['cod_tutormenu']] = $tutormenu['nm_tutormenu'];
      }
    }

    $lista_menu_pai = $_SESSION['menu_suspenso'];
    $opcoes_pai = array('' => 'Selecione');

    if ($lista_menu_pai) {
      foreach ($lista_menu_pai as $key=>$menu_pai) {
        if ($menu_pai['tt_menu']) {
          $opcoes_pai[$key] = $menu_pai['tt_menu'];
        }
      }
    }

    $lista_sim_nao = array(
      '1' => 'Sim',
      '0' => 'Não'
    );

    $lista_alvo = array(
      '_self' => 'Self',
      '_blank' => 'Blank',
      '_parent' => 'Parent',
      '_top'=>'Top'
    );

    if (!$this->suprime_menu) {
      $this->suprime_menu = 1;
    }

    $db = new clsBanco();
    $db->Consulta('SELECT cod_menu_menu, nm_menu FROM menu_menu ORDER BY nm_menu');

    while ($db->ProximoRegistro()) {
      list ($cod_menu_menu, $nm_menu_menu) = $db->Tupla();
      $opcoes_menu['0'] = 'Selecione';
      $opcoes_menu[$cod_menu_menu] = $nm_menu_menu;
    }

    $this->campoOculto('cod_menu', $this->cod_menu);
    $this->campoOculto('lista', '0');
    $this->campoNumero('ord_menu', 'Ordem', $this->ord_menu, 5, 5);
    $this->campoLista('ref_cod_menu_pai', 'Menu Pai', $opcoes_pai,
      $_POST['ref_cod_menu_pai'], '', FALSE, '', '', FALSE, FALSE);

    $vf = FALSE;

    if ($_SESSION['menu_suspenso']) {
      foreach ($_SESSION['menu_suspenso'] as $campo) {
        if (!empty($campo['ref_cod_menu_pai']) || $campo['ref_cod_menu_pai'] == '0') {
          $vf = TRUE;
        }
      }
    }

    if (!$vf) {
      $this->campoLista("ref_cod_menu", "Menu", $opcoes_menu, $this->ref_cod_menu,"insereSubmitLista();");
    }
    elseif (!$_SESSION['menu_suspenso']) {
      $this->campoLista("ref_cod_menu", "Menu", $opcoes_menu, $this->ref_cod_menu,"insereSubmitLista();");
    }
    elseif ($_POST['ref_cod_menu']) {
      $this->campoRotulo("ref_cod_menu_1", "Menu", $opcoes_menu[$_POST['ref_cod_menu']]);
      $this->campoOculto("ref_cod_menu", $_POST['ref_cod_menu']);
    }
    elseif ($_SESSION['menu_suspenso']) {
      foreach ($_SESSION['menu_suspenso'] as $id => $value) {
        $menu = $value['ref_cod_menu_submenu'];
        $menu_pai =  $value['menu_menu_pai'];

        if ($menu && empty($menu_pai)) {
          $db = new clsBanco();
          $db->Consulta("SELECT ref_cod_menu_menu, nm_menu FROM menu_submenu, menu_menu WHERE cod_menu_menu=ref_cod_menu_menu AND cod_menu_submenu={$menu}");

          if($db->ProximoRegistro()) {
            list($cod_sub, $nm_sub) = $db->Tupla();
          }

          break;
        }
      }

      $this->campoRotulo('ref_cod_menu_2', 'Menu', $nm_sub);
      $this->campoOculto('ref_cod_menu', $cod_sub);
    }

    $this->campoLista('ref_cod_menu_submenu', 'Sub Menu', $opcoes_submenu,
      $this->ref_cod_menu_submenu);

    $this->campoTexto('tt_menu','T&iacute;tulo', $this->tt_menu, 30, 30);

    $this->campoRotulo('banco_imagem', '&Iacute;cone Menu',
      "<input class='geral' type='text' name=\"img_banco_\" id=\"img_banco_\" value=\"\" size=\"30\" maxlength=\"255\" disabled><a href='#' onclick=\"pesquisa_valores_f('pesquisa_imagens.php', 'img')\"><img src='imagens/banco_imagens/identify.gif' alt='Carregar Imagem' title='Carregar Imagem' border='0' hspace='5'>Carregar Imagem</a><a href='#' onclick='formcadastro.img_banco_.value = \"\";formcadastro.img_banco.value = \"\";' ><img src='imagens/banco_imagens/lixeira.gif' alt='Carregar Imagem' title='Carregar Imagem' border='0' hspace='5'>Limpar</a>");

    $this->campoOculto('img_banco', '');
    $this->campoArquivo('ico_menu', '&Iacute;cone Menu', $this->ico_menu, '50');
    $this->campoLista('alvo', 'Alvo', $lista_alvo, $this->alvo);
    $this->campoLista('suprime_menu', 'Suprime Menu', $lista_sim_nao, $this->suprime_menu);
    $this->campoRotulo('tutor','Tutor Menu', $opcoes_tutormenu[$_GET['cod_menu']]);
    $this->campoOculto('editar', '');
    $this->campoOculto('editando', '');

    $this->campoRotulo('opcao', 'Op&ccedil;&otilde;es',
      "<a href='#' onclick='insereSubmit();'><img src='imagens/banco_imagens/incluir.gif' hspace='5' alt='Adicionar' title='Adicionar' border='0'></a><a href='#' onclick='MenuExcluiDado()'><img src='imagens/banco_imagens/excluir.gif' hspace='5' alt='Excluir' title='Excluir' border='0'></a>");
  }

  function Editar()
  {
    @session_start();
    $ordenado = $_SESSION['menu_suspenso'];
    @session_write_close();

    $ObjDel = new clsMenuSuspenso(FALSE, FALSE, FALSE, FALSE, FALSE, FALSE,
      FALSE, FALSE, FALSE, $this->cod_menu);

    $excluiu = $ObjDel->exclui();
    $arr_chaves = array();

    foreach ($_SESSION['menu_suspenso'] as $id=>$menu) {
      $arr_chaves[$id] = $menu['ref_cod_menu_pai'];
    }

    $filhos = array();

    if (is_array($_SESSION['menu_suspenso']) && $excluiu) {
      foreach ($_SESSION['menu_suspenso'] as $id => $menu) {
        $obj = new clsMenuSuspenso(FALSE, $menu['ref_cod_menu_submenu'],
          $filhos[$id], $menu['tt_menu'], $menu['ico_menu'], $menu['ord_menu'],
          $menu['caminho'], $menu['alvo'], $menu['suprime_menu'],
          $menu['ref_cod_tutor_menu']);

        $cod = $obj->cadastra();

        if($arr_chaves) {
          foreach ($arr_chaves as $id2 => $valor) {
            if ($id == $valor) {
              $filhos[$id2] = $cod;
            }

            if (empty($valor) && $valor != '0') {
              $filhos[$id2] = "";
            }
          }
        }
      }

      header("Location: menu_suspenso_det.php?cod_menu={$_GET['cod_menu']}");
    }

    return FALSE;
  }
}

// Instancia objeto de página
$pagina = new clsIndex();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
div_mostrar = "teste_menu";
setTimeout("setXY();", 10);

function setXY()
{
  document.getElementById(div_mostrar).style.height = '20px';

  x = DOM_ObjectPosition_getPageOffsetLeft(document.getElementById('teste_menu'));
  y = DOM_ObjectPosition_getPageOffsetTop(document.getElementById('teste_menu'));

  for (i = 1; i <= array_id.length; i++) {
    obj = document.getElementById('oCMenu_' + i + '__0');
    obj.style.left = (obj.style.left.split('px')[0] * 1) + x + 'px';
    obj.style.top  = (obj.style.top.split('px')[0] * 1) + y + 'px';
  }
}

MontaMenu();
</script>