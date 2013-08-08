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
 * @package   iEd_Include
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id: clsBase.inc.php 773 2010-12-19 20:46:49Z eriksencosta@gmail.com $
 */

// Inclui arquivo de bootstrapping
//require_once '/home/rafael/ieducar/ieducar/includes/bootstrap.php';
require_once '../includes/bootstrap.php';

// redireciona requisições, caso configurado
if ($GLOBALS['coreExt']['Config']->app->routes &&
    $GLOBALS['coreExt']['Config']->app->routes->redirect_to) {

  header('HTTP/1.1 503 Service Temporarily Unavailable');
  header("Location: {$GLOBALS['coreExt']['Config']->app->routes->redirect_to}");
}

require_once 'include/clsCronometro.inc.php';
require_once 'clsConfigItajai.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsMenu.inc.php';
require_once 'include/clsControlador.inc.php';
require_once 'include/clsLogAcesso.inc.php';
require_once 'include/Geral.inc.php';
require_once 'include/pmicontrolesis/geral.inc.php';
require_once 'include/funcoes.inc.php';

require_once 'Portabilis/Utils/Database.php';
require_once 'Portabilis/Utils/User.php';
require_once 'Portabilis/String/Utils.php';

require_once 'modules/Error/Mailers/NotificationMailer.php';

/**
 * clsBase class.
 *
 * Provê uma API para criação de páginas HTML programaticamente.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Include
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsBase extends clsConfig
{
  var $titulo     = 'Prefeitura Cobra Tecnologia';
  var $clsForm    = array();
  var $bodyscript = NULL;
  var $processoAp;
  var $refresh    = FALSE;

  var $convidado  = FALSE;
  var $renderMenu = TRUE;
  var $renderMenuSuspenso = TRUE;
  var $renderBanner = TRUE;
  var $estilos;
  var $scripts;

  var $script_header;
  var $script_footer;
  var $prog_alert;

  function OpenTpl($template)
  {
    $prefix = 'nvp_';
    $file   = $this->arrayConfig['strDirTemplates'] . $prefix . $template . '.tpl';

    ob_start();
    include $file;
    $contents = ob_get_contents();
    ob_end_clean();

    return $contents;
  }

  function SetTitulo($titulo)
  {
    $this->titulo = $titulo;
  }

  function AddForm($form)
  {
    $this->clsForm[] = $form;
  }

  function MakeHeadHtml()
  {
    $saida = $this->OpenTpl('htmlhead');
    $saida = str_replace("<!-- #&TITULO&# -->", $this->titulo, $saida);

    if ($this->refresh) {
      $saida = str_replace("<!-- #&REFRESH&# -->", "<meta http-equiv='refresh' content='60'>", $saida);
    }

    if (is_array($this->estilos) && count($this->estilos)) {
      $estilos = '';
      foreach ($this->estilos as $estilo) {
        $estilos .= "<link rel=stylesheet type='text/css' href='styles/{$estilo}.css' />";
      }
      $saida = str_replace("<!-- #&ESTILO&# -->", $estilos, $saida);
    }

    if (is_array($this->scripts) && count($this->scripts)) {
      $estilos = '';
      foreach ($this->scripts as $script) {
        $scripts .= "<script type='text/javascript' src='scripts/{$script}.js'></script>";
      }
      $saida = str_replace( "<!-- #&SCRIPT&# -->", $scripts, $saida );
    }

    if ($this->bodyscript) {
      $saida = str_replace("<!-- #&BODYSCRIPTS&# -->", $this->bodyscript, $saida);
    }
    else {
      $saida = str_replace("<!-- #&BODYSCRIPTS&# -->", "", $saida);
    }

    if ($this->script_header) {
      $saida = str_replace("<!-- #&SCRIPT_HEADER&# -->", $this->script_header, $saida);
    }
    else {
      $saida = str_replace("<!-- #&SCRIPT_HEADER&# -->", "", $saida);
    }

    return $saida;
  }

  function addEstilo($estilo_nome)
  {
    $this->estilos[$estilo_nome] = $estilo_nome;
  }

  function addScript($script_nome)
  {
    $this->scripts[$script_nome] = $script_nome;
  }

  function MakeFootHtml()
  {
    $saida =  $this->OpenTpl('htmlfoot');

    if ($this->script_footer) {
      $saida = str_replace("<!-- #&SCRIPT_FOOTER&# -->", $this->script_footer, $saida);
    }
    else {
      $saida = str_replace("<!-- #&SCRIPT_FOOTER&# -->", "", $saida);
    }

    return $saida;
  }

  function verificaPermissao()
  {
    return $this->VerificaPermicao();
  }

  function VerificaPermicao()
  {
    if(is_array($this->processoAp))
    {
      $permite = true;
      foreach($this->processoAp as $processo) {
        if(!$this->VerificaPermicaoNumerico($processo)) {
          $permite = false;
        }
        else {
          $this->processoAp = $processo;
          $permite = true;
          break;
        }
      }
      if (!$permite) {
        header("location: index.php?negado=1&err=1");
        die("Acesso negado para este usu&acute;rio");
      }
    }
    else {
      if (!$this->VerificaPermicaoNumerico($this->processoAp)) {
        header("location: index.php?negado=1&err=2");
        die("Acesso negado para este usu&acute;rio");
      }
    }

    return TRUE;
  }

  function VerificaPermicaoNumerico($processo_ap)
  {
    if (is_numeric($processo_ap)) {
      $sempermissao = TRUE;

      if ($processo_ap == 0) {
        $this->prog_alert .= "Processo AP == 0!";
      }

      if ($processo_ap != 0) {
        $this->db()->Consulta("SELECT 1 FROM menu_funcionario WHERE ref_cod_menu_submenu = 0 AND ref_ref_cod_pessoa_fj = {$this->currentUserId()}");
        if ($this->db()->ProximoRegistro()) {
          list($aui) = $this->db()->Tupla();
          $sempermissao = FALSE;
        }

        // @todo A primeira consulta OK, verifica de forma simples de tem
        //       permissão de acesso ao processo. Já a segunda, não existe
        //       sentido para nivel = 2 já que processoAp pode ser de níveis
        //       maiores que 2.
        $this->db()->Consulta("SELECT 1 FROM menu_funcionario WHERE (ref_cod_menu_submenu = {$processo_ap} AND ref_ref_cod_pessoa_fj = {$this->currentUserId()}) OR (SELECT true FROM menu_submenu WHERE cod_menu_submenu = {$processo_ap} AND nivel = 2)");
        if ($this->db()->ProximoRegistro()) {
          list($aui) = $this->db()->Tupla();
          $sempermissao = FALSE;
        }

        if ($sempermissao) {
          $ip = empty($_SERVER['REMOTE_ADDR']) ? "NULL" : $_SERVER['REMOTE_ADDR'];
          $ip_de_rede = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? "NULL" : $_SERVER['HTTP_X_FORWARDED_FOR'];
          $pagina = $_SERVER["PHP_SELF"];
          $posts = "";
          $gets = "";
          $sessions = "";

          foreach ($_POST as $key => $val) {
            $posts .= " - $key: $val\n";
          }

          foreach ($_GET as $key => $val) {
            $gets .= " - $key: $val\n";
          }

          foreach ($_SESSION as $key => $val) {
            $sessions .= " - $key: $val\n";
          }

          $variaveis = "POST\n{$posts}GET\n{$gets}SESSION\n{$sessions}";
          $variaveis = Portabilis_String_Utils::toLatin1($variaveis, array('escape' => true));

          if ($this->currentUserId()) {
            $this->db()->Consulta("INSERT INTO intranet_segur_permissao_negada (ref_ref_cod_pessoa_fj, ip_externo, ip_interno, data_hora, pagina, variaveis) VALUES('{$this->currentUserId()}', '$ip', '$ip_de_rede', NOW(), '$pagina', '$variaveis')");
          }
          else {
            $this->db()->Consulta("INSERT INTO intranet_segur_permissao_negada (ref_ref_cod_pessoa_fj, ip_externo, ip_interno, data_hora, pagina, variaveis) VALUES(NULL, '$ip', '$ip_de_rede', NOW(), '$pagina', '$variaveis')");
          }

          return FALSE;
        }
      }
      return TRUE;
    }
  }

  function MakeMenu()
  {
    $menu = $this->openTpl("htmlmenu");
    $menuObj = new clsMenu();
    $saida = $menuObj->MakeMenu($this->openTpl("htmllinhamenu"), $this->openTpl("htmllinhamenusubtitulo"));
    $saida = str_replace("<!-- #&LINHAS&# -->", $saida, $menu);
    return $saida;
  }

  /**
   * Cria o menu suspenso dos subsistemas Escola e Biblioteca.
   *
   * @todo Refatorar lógica do primeiro par if/else, duplicação
   * @return bool|string Retorna FALSE em caso de erro
   */
  function makeMenuSuspenso()
  {
    // Usa helper de Url para pegar o path da requisição
    require_once 'CoreExt/View/Helper/UrlHelper.php';

    $uri = explode('/', CoreExt_View_Helper_UrlHelper::url($_SERVER['REQUEST_URI'],
      array(
        'components' => CoreExt_View_Helper_UrlHelper::URL_PATH
      )
    ));

    @session_start();
    $idpes = $_SESSION['id_pessoa'];
    @session_write_close();

    $submenu = array();
    $menu_tutor = '';

    if ($this->processoAp) {
      $menu_atual = $this->db()->UnicoCampo("SELECT ref_cod_menu_menu FROM menu_submenu WHERE cod_menu_submenu = '{$this->processoAp}'");

      if ($menu_atual) {
        $this->db()->Consulta("SELECT cod_menu_submenu FROM menu_submenu WHERE ref_cod_menu_menu = '{$menu_atual}'");
        while ($this->db()->ProximoRegistro()) {
          $tupla = $this->db()->Tupla();
          $submenu[] = $tupla['cod_menu_submenu'];
        }
        $where = implode(" OR ref_cod_menu_submenu = ", $submenu);
        $where = "ref_cod_menu_submenu = $where";
        $menu_tutor = $this->db()->UnicoCampo("SELECT ref_cod_tutormenu FROM pmicontrolesis.menu WHERE $where LIMIT 1 OFFSET 0");
      }
      else {
        $this->prog_alert .= "O menu pai do processo AP {$this->processoAp} está voltando vazio (cod_menu inexistente?).<br>";
      }
    }
    elseif ($_SESSION['menu_atual']) {
      $this->db()->Consulta("SELECT cod_menu_submenu FROM menu_submenu WHERE ref_cod_menu_menu = '{$_SESSION['menu_atual']}'");

      while ($this->db()->ProximoRegistro()) {
        $tupla = $this->db()->Tupla();
        $submenu[] = $tupla['cod_menu_submenu'];
      }

      $where = implode(" OR ref_cod_menu_submenu = ", $submenu);
      $where = "ref_cod_menu_submenu = $where";
      $menu_tutor = $this->db()->UnicoCampo("SELECT ref_cod_tutormenu FROM pmicontrolesis.menu WHERE $where LIMIT 1 OFFSET 0");
    }

    if ($menu_tutor) {
      $obj_menu_suspenso = new clsMenuSuspenso();
      $lista_menu = $obj_menu_suspenso->listaNivel($menu_tutor, $idpes);
      $lista_menu_suspenso = $lista_menu;

      if ($lista_menu_suspenso) {
        for ($i = 0, $loop = count($lista_menu_suspenso); $i < $loop; $i++) {
          $achou = FALSE;

          if (!$lista_menu_suspenso[$i]['ref_cod_menu_submenu']) {
            foreach ($lista_menu as $id => $menu) {
              if ($menu['ref_cod_menu_pai'] == $lista_menu_suspenso[$i]['cod_menu']) {
                $achou = TRUE;
              }
            }
            if (!$achou) {
              unset($lista_menu[$i]);
            }
          }
        }

        $saida  = '<script type="text/javascript">';
        $saida .= 'array_menu = new Array(); array_id = new Array();';

        foreach ($lista_menu as $menu_suspenso) {
           $ico_menu = '';

          if (is_numeric($menu_suspenso['ref_cod_ico'])) {
            $this->db()->Consulta("SELECT caminho FROM portal.imagem WHERE cod_imagem = {$menu_suspenso['ref_cod_ico']} ");
            if ($this->db()->ProximoRegistro()) {
              list($ico_menu) = $this->db()->Tupla();
              $ico_menu = "imagens/banco_imagens/$ico_menu";
            }
          }

          $alvo = $menu_suspenso['alvo'] ? $menu_suspenso['alvo'] : '_self';

          // Corrige o path usando caminhos relativos para permitir a inclusão
          // de itens no menu que apontem para um módulo
          if ($uri[1] == 'module') {
            if (0 === strpos($menu_suspenso['caminho'], 'module')) {
              $menu_suspenso['caminho'] = '../../' . $menu_suspenso['caminho'];
            }
            else {
              $menu_suspenso['caminho'] = '../../intranet/' . $menu_suspenso['caminho'];
            }
          }
          elseif (0 === strpos($menu_suspenso['caminho'], 'module')) {
              $menu_suspenso['caminho'] = '../../' . $menu_suspenso['caminho'];
          }

          $saida .= "array_menu[array_menu.length] = new Array(\"{$menu_suspenso['tt_menu']}\",{$menu_suspenso['cod_menu']},'{$menu_suspenso['ref_cod_menu_pai']}','', '$ico_menu', '{$menu_suspenso['caminho']}', '{$alvo}');";
          if (!$menu_suspenso['ref_cod_menu_pai']) {
            $saida .= "array_id[array_id.length] = {$menu_suspenso['cod_menu']};";
          }
        }

        $saida .="</script>";
      }

      $saida .="<script type=\"text/javascript\">
          setTimeout(\"setXY();\",150);
          MontaMenu();
        </script>";
      return $saida;
    }

    return FALSE;
  }

  function DataAtual()
  {
    $retorno = "";
    switch (date('w')) {
      case "0":
        $retorno .= "Domingo";
        break;
      case "1":
        $retorno .= "Segunda-feira";
        break;
      case "2":
        $retorno .= "Ter&ccedil;a-feira";
        break;
      case "3":
        $retorno .= "Quarta-feira";
        break;
      case "4":
        $retorno .= "Quinta-feira";
        break;
      case "5":
        $retorno .= "Sexta-feira";
        break;
      case "6":
        $retorno .= "S&aacute;bado";
        break;
    }

    $retorno .= ", " . date('d') . " de ";

    switch (date('n')) {
      case "1":
        $retorno .= "janeiro de ";
        break;
      case "2":
        $retorno .= "fevereiro de ";
        break;
      case "3":
        $retorno .= "mar&ccedil;o de ";
        break;
      case "4":
        $retorno .= "abril de ";
        break;
      case "5":
        $retorno .= "maio de ";
        break;
      case "6":
        $retorno .= "junho de ";
        break;
      case "7":
        $retorno .= "julho de ";
        break;
      case "8":
        $retorno .= "agosto de ";
        break;
      case "9":
        $retorno .= "setembro de ";
        break;
      case "10":
        $retorno .= "outubro de ";
        break;
      case "11":
        $retorno .= "novembro de ";
        break;
      case "12":
        $retorno .= "dezembro de ";
        break;
    }

    $retorno .= date('Y') . ".";

    return $retorno;
  }

  /**
   * @see Core_Page_Controller_Abstract#getAppendedOutput()
   * @see Core_Page_Controller_Abstract#getPrependedOutput()
   */
  function MakeBody()
  {
    $corpo = '';
    foreach ($this->clsForm as $form) {
      $corpo .= $form->RenderHTML();

      // Prepend output.
      if (method_exists($form, 'getPrependedOutput')) {
        $corpo = $form->getPrependedOutput() . $corpo;
      }

      // Append output.
      if (method_exists($form, 'getAppendedOutput')) {
        $corpo = $corpo . $form->getAppendedOutput();
      }

      if (is_string($form->prog_alert) && $form->prog_alert) {
        $this->prog_alert .= $form->prog_alert;
      }
    }

    $menu = '';

    if ($this->renderMenu) {
      $menu = $this->MakeMenu();
    }
    $data = $this->DataAtual();

    if ($this->renderBanner) {
      if ($this->renderMenu) {
        $saida = $this->OpenTpl("htmlbody");
      }
      /**
       * @todo Essa segunda condição não se torna verdadeira nunca, já que não
       *   existe uma condição binária entre $renderBanner e $renderMenu que
       *   a execute. Ver:
       *   <code>
       *     $ egrep -rn "renderBanner\s?=\s?true" intranet/
       *     $ egrep -rn "renderBanner\s?=\s?false" intranet/
       *     $  egrep -rn "renderMenu\s?=\s?false" intranet/
       *   </code>
       *
       *   Para acontecer, seria necessário renderBanner = true (default,
       *     herança) com renderMenu = false.
       *
       *   Caso não ocorra, remover a condicional e apagar o arquivo _sem_menu.
       */
      else {
        $saida = $this->OpenTpl("htmlbody_sem_menu");
      }
    }
    else {
      $saida = $this->OpenTpl("htmlbodys");
    }
    $saida = str_replace("<!-- #&DATA&# -->", $data, $saida);

    if ($this->renderMenu) {
      $saida = str_replace("<!-- #&MENU&# -->", $menu, $saida);
    }

    $menu_dinamico = $this->makeBanner();

    $notificacao = "";
    $this->db()->Consulta("SELECT cod_notificacao, titulo, conteudo, url FROM portal.notificacao WHERE ref_cod_funcionario = '{$this->currentUserId()}' AND data_hora_ativa < NOW()");

    if ($this->db()->numLinhas()) {
      while ($this->db()->ProximoRegistro()) {
        list($cod_notificacao, $titulo, $conteudo, $url) = $this->db()->Tupla();

        $titulo = ($url) ? "<a href=\"{$url}\">{$titulo}</a>": $titulo;

        $notificacao .= "<div id=\"notificacao_{$cod_notificacao}\" class=\"prog_alert\" align=\"left\">
        <div class=\"controle_fechar\" title=\"Fechar\" onclick=\"fecha_notificacao( {$cod_notificacao} );\">x</div>
        <center><strong>Notifica&ccedil;&atilde;o</strong></center>
        <b>T&iacute;tulo</b>: {$titulo}<br />
        <b>Conte&uacute;do</b>: " . str_replace("\n", "<br>", $conteudo) . "<br />
        </div>";
      }
      $saida = str_replace( "<!-- #&NOTIFICACOES&# -->", $notificacao, $saida );
      $this->db()->Consulta("UPDATE portal.notificacao SET visualizacoes = visualizacoes + 1 WHERE ref_cod_funcionario = '{$this->currentUserId()}' AND data_hora_ativa < NOW()");
      $this->db()->Consulta("DELETE FROM portal.notificacao WHERE visualizacoes > 10");
    }

    // nome completo usuario
    $nomePessoa       = new clsPessoaFisica();
    list($nomePessoa) = $nomePessoa->queryRapida($this->currentUserId(), "nome");
    $nomePessoa       = ($nomePessoa) ? $nomePessoa : "<span style='color: #DD0000; '>Convidado</span>";


    // data ultimo acesso
    $ultimoAcesso     = $this->db()->UnicoCampo("SELECT data_hora FROM acesso WHERE cod_pessoa = {$this->currentUserId()} ORDER BY data_hora DESC LIMIT 1,1");

    if($ultimoAcesso)
      $ultimoAcesso = date("d/m/Y H:i", strtotime(substr($ultimoAcesso,0,19)));

    $this->checkUserExpirations();

    // substitui valores no template
    $saida = str_replace("<!-- #&ULTIMOACESSO&# -->", $ultimoAcesso,  $saida);
    $saida = str_replace("<!-- #&USERLOGADO&# -->",   $nomePessoa,    $saida);
    $saida = str_replace("<!-- #&CORPO&# -->",        $corpo,         $saida);
    $saida = str_replace("<!-- #&ANUNCIO&# -->",      $menu_dinamico, $saida);

    // Pega o endereço IP do host, primeiro com HTTP_X_FORWARDED_FOR (para pegar o IP real
    // caso o host esteja atrás de um proxy)
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
      // No caso de múltiplos IPs, pega o último da lista
      $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
      $ip_maquina = trim(array_pop($ip));
    }
    else {
      $ip_maquina = $_SERVER['REMOTE_ADDR'];
    }

    $sql = "UPDATE funcionario SET ip_logado = '$ip_maquina' , data_login = NOW() WHERE ref_cod_pessoa_fj = {$this->currentUserId()}";
    $this->db()->Consulta($sql);

    return $saida;
  }

  function organiza($listaBanners)
  {
    $aux_inicio = 0;
    $aux_fim = 0;
    foreach ($listaBanners as $ind => $banner) {
      $aux_fim = $aux_inicio + $banner["prioridade"];
      $banner["controle_inicio"] = $aux_inicio;
      $banner["controle_fim"] = $aux_fim;
      $aux_inicio = $aux_fim+1;
      $listaBanners[$ind] = $banner;
    }

    return array($listaBanners, $aux_fim);
  }

  function makeBanner()
  {
    $retorno = '';
    $listaBanners = array();
    $this->db()->Consulta("SELECT caminho, title, prioridade, link FROM portal_banner WHERE lateral=1 ORDER BY prioridade, title");

    while ($this->db()->ProximoRegistro()) {
      list($caminho, $title, $prioridade, $link) = $this->db()->Tupla();
      $listaBanners[] = array("titulo"=>$title, "caminho"=>$caminho, "prioridade"=>$prioridade, "link"=>$link, "controle_inicio"=>0, "controle_fim"=>0);
    }

    list($listaBanners, $aux_fim) = $this->organiza($listaBanners);

    $pregadas = 0;
    $total_pregar = count($listaBanners) > 7 ? 7 :count($listaBanners);
    while ($pregadas < $total_pregar) {
      $sorteio = rand(0, $aux_fim);
      foreach($listaBanners as $ind => $banner) {
        if ($banner["controle_inicio"]<=$sorteio && $banner["controle_fim"]>=$sorteio) {
          if ($pregadas == 0) {
            $img = "<IMG style='margin-top: 170px;' src='fotos/imgs/{$banner['caminho']}' border=0 title='{$banner['titulo']}' alt='{$banner['titulo']}' width='149' height='74'>";

            if (!empty($banner['link'])) {
              $retorno .= "<a href='{$banner['link']}' target='_blank' alt='{$banner['titulo']}'>{$img}</a><BR><BR>";
            }
            else {
              $retorno .= "{$img}<BR><BR>";
            }
          }
          else {
            $img = "<IMG src='fotos/imgs/{$banner['caminho']}' border=0 title='{$banner['titulo']}' alt='{$banner['titulo']}' width='149' height='74'>";

            if (!empty($banner['link'])) {
              $retorno .= "<a href='{$banner['link']}' target='_blank' alt='{$banner['titulo']}'>{$img}</a><BR><BR>";
            }
            else {
              $retorno .= "{$img}<BR><BR>";
            }
          }

          unset($listaBanners[$ind]);
          $pregadas++;
          list ($listaBanners, $aux_fim) = $this->organiza($listaBanners);
          continue;
        }
      }
    }
    return $retorno;
  }

  function Formular()
  {
    return FALSE;
  }

  /**
   * @todo Verificar se funciona.
   */
  function CadastraAcesso()
  {
    @session_start();
    if (@$_SESSION['marcado'] != "private") {
      if (!$this->convidado) {
        $ip = empty($_SERVER['REMOTE_ADDR']) ? "NULL" : $_SERVER['REMOTE_ADDR'];
        $ip_de_rede = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? "NULL" : $_SERVER['HTTP_X_FORWARDED_FOR'];
        $id_pessoa = $_SESSION['id_pessoa'];

        $logAcesso = new clsLogAcesso(FALSE, $ip, $ip_de_rede, $id_pessoa);
        $logAcesso->cadastra();

        $_SESSION['marcado'] = "private";
      }
    }
    session_write_close();
  }

  function MakeAll ()
  {
    try {
      $cronometro = new clsCronometro();
      $cronometro->marca('inicio');
      $liberado = TRUE;

      $saida_geral = '';

      if ($this->convidado) {
        @session_start();
        $_SESSION['convidado'] = TRUE;
        $_SESSION['id_pessoa'] = '0';
        session_write_close();
      }

      $controlador = new clsControlador();
      if ($controlador->Logado() && $liberado || $this->convidado) {
        $this->Formular();
        $this->VerificaPermicao();
        $this->CadastraAcesso();
        $saida_geral = $this->MakeHeadHtml();

        if ($this->renderMenu) {
          $saida_geral .= $this->MakeBody();
        }
        else {
          foreach ($this->clsForm as $form) {
            $saida_geral .= $form->RenderHTML();
          }
        }

        $saida_geral .= $this->MakeFootHtml();

        if ($_GET['suspenso'] == 1 || $_SESSION['suspenso'] == 1 || $_SESSION["tipo_menu"] == 1) {
          if ($this->renderMenuSuspenso) {
            $saida_geral = str_replace("<!-- #&MENUSUSPENSO&# -->", $this->makeMenuSuspenso(), $saida_geral);
          }

          if ($_GET['suspenso'] == 1) {
            @session_start();
            $_SESSION['suspenso'] = 1;
            @session_write_close();
          }
        }
      }
      elseif ((empty($_POST['login'])) || (empty($_POST['senha'])) && $liberado) {
        $saida_geral .= $this->MakeHeadHtml();
        $controlador->Logar(FALSE);
        $saida_geral .= $this->MakeFootHtml();
      }
      else {
        $controlador->Logar(TRUE);
        if ($controlador->Logado() && $liberado) {
          $this->Formular();
          $this->VerificaPermicao();
          $this->CadastraAcesso();
          $saida_geral = $this->MakeHeadHtml();
          $saida_geral .= $this->MakeBody();
          $saida_geral .= $this->MakeFootHtml();
        }
        else {
          $saida_geral = $this->MakeHeadHtml();
          $controlador->Logar  (false);
          $saida_geral .= $this->MakeFootHtml();
        }
      }

      echo $saida_geral;

      $cronometro->marca('fim');
      $tempoTotal = $cronometro->getTempoTotal();
      $tempoTotal += 0;
      $objConfig  = new clsConfig();

      if ($tempoTotal > $objConfig->arrayConfig["intSegundosProcessaPagina"]) {
        $conteudo = "<table border=\"1\" width=\"100%\">";
        $conteudo .= "<tr><td><b>Data</b>:</td><td>" . date( "d/m/Y H:i:s", time() ) . "</td></tr>";
        $conteudo .= "<tr><td><b>Script</b>:</td><td>{$_SERVER["PHP_SELF"]}</td></tr>";
        $conteudo .= "<tr><td><b>Tempo de processamento</b>:</td><td>{$tempoTotal} segundos</td></tr>";
        $conteudo .= "<tr><td><b>Tempo max permitido</b>:</td><td>{$objConfig->arrayConfig["intSegundosProcessaPagina"]} segundos</td></tr>";
        $conteudo .= "<tr><td><b>URL get</b>:</td><td>{$_SERVER['QUERY_STRING']}</td></tr>";
        $conteudo .= "<tr><td><b>Metodo</b>:</td><td>{$_SERVER["REQUEST_METHOD"]}</td></tr>";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          $conteudo .= "<tr><td><b>POST vars</b>:</td><td>";
          foreach ($_POST as $var => $val) {
            $conteudo .= "{$var} => {$val}<br>";
          }
          $conteudo .= "</td></tr>";
        }
        elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
          $conteudo .= "<tr><td><b>GET vars</b>:</td><td>";
          foreach ($_GET as $var => $val) {
            $conteudo .= "{$var} => {$val}<br>";
          }
          $conteudo .= "</td></tr>";
        }

        if ($_SERVER['HTTP_REFERER']) {
          $conteudo .= "<tr><td><b>Referrer</b>:</td><td>{$_SERVER["HTTP_REFERER"]}</td></tr>";
        }

        $conteudo .= "</table>";

        $objMail = new clsEmail($objConfig->arrayConfig['ArrStrEmailsAdministradores'], "[INTRANET - PMI] Desempenho de pagina", $conteudo);
        $objMail->envia();
      }
    }
    catch (Exception $e) {
      $lastError = error_get_last();

      @session_start();
      $_SESSION['last_error_message']     = $e->getMessage();
      $_SESSION['last_php_error_message'] = $lastError['message'];
      $_SESSION['last_php_error_line']    = $lastError['line'];
      $_SESSION['last_php_error_file']    = $lastError['file'];
      @session_write_close();

      error_log("Erro inesperado (pego em clsBase): " . $e->getMessage());
      NotificationMailer::unexpectedError($e->getMessage());

      die("<script>document.location.href = '/module/Error/unexpected';</script>");
    }
  }

  function setAlertaProgramacao($string)
  {
    if (is_string($string) && $string) {
      $this->prog_alert = $string;
    }
  }

  protected function checkUserExpirations() {
    $user                = Portabilis_Utils_User::load('current_user');
    $uri                 = $_SERVER['REQUEST_URI'];
    $forcePasswordUpdate = $GLOBALS['coreExt']['Config']->app->user_accounts->force_password_update == true;

    if($user['expired_account'] || $user['proibido'] != '0' || $user['ativo'] != '1')
      header("Location: /intranet/logof.php");

    elseif($user['expired_password'] && $forcePasswordUpdate && $uri != '/module/Usuario/AlterarSenha')
      header("Location: /module/Usuario/AlterarSenha");
  }

  // wrappers for Portabilis_*Utils*

  protected function db() {
    return Portabilis_Utils_Database::db();
  }

  protected function currentUserId() {
    return Portabilis_Utils_User::currentUserId();
  }
}
