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
 * @version   $Id$
 */

require_once 'include/clsCampos.inc.php';
require_once 'include/localizacaoSistema.php';

if (class_exists('clsPmiajudaPagina')) {
  require_once 'include/pmiajuda/clsPmiajudaPagina.inc.php';
}

require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/View/Helper/Inputs.php';

define('alTopLeft', 'valign=top align=left');
define('alTopCenter', 'valign=top align=center');
define('alTopRight', 'valign=top align=right');

define('alMiddleLeft', 'valign=middle align=left');
define('alMiddleCenter', 'valign=middle align=center');
define('alMiddleRight', 'valign=middle align=right');

define('alBottomLeft', 'valign=bottom align=left');
define('alBottomCenter', 'valign=bottom align=center');
define('alBottomRight', 'valign=bottom align=right');

/**
 * clsListagem class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Include
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsListagem extends clsCampos
{
  var $nome = 'formulario';
  var $__titulo;
  var $titulo;
  var $banner = FALSE;
  var $bannerLateral = FALSE;
  var $titulo_barra;
  var $bannerClose = FALSE;
  var $largura;
  var $linhas;
  var $colunas;
  var $cabecalho;
  var $paginacao;
  var $tabulacao;
  var $method = 'GET';
  var $camposResultado;
  var $tituloFormResultado;
  var $funcAcao = '';
  var $funcAcaoNome = '';
  var $rotulo_anterior;
  var $locale = "<b>=> PAGINA SEM LOCALIZACAO, COLOQUE POR GENTILEZA. <=</b>";

  var $array_botao;
  var $array_botao_url;
  var $array_botao_script;
  var $show_botao_novo = TRUE;
  var $acao_imprimir = FALSE;
  var $valor_imprimir = 'Imprimir Arquivo';

  var $paginador = array();
  var $numeropaginador = 0;
  var $paginador2;
  var $busca_janela = 0;

  var $rodape = '';

  var $ordenacao;
  var $campos_ordenacao;
  var $fonte;

  var $exibirBotaoSubmit = true;

  function Gerar()
  {
    return FALSE;
  }

  function addBanner($strBannerUrl = '', $strBannerLateralUrl = '',
    $strBannerTitulo = '', $boolFechaBanner = TRUE)
  {
    if ($strBannerUrl != '') {
      $this->banner = $strBannerUrl;
    }
    if ($strBannerLateralUrl != '') {
      $this->bannerLateral = $strBannerLateralUrl;
    }
    if ($strBannerTitulo != '') {
      $this->titulo_barra = $strBannerTitulo;
    }

    $this->bannerClose = $boolFechaBanner;
  }
  
  function enviaLocalizacao($localizao){
      if($localizao)
        $this->locale = $localizao;
  }

  function addCabecalhos($coluna)
  {
    $this->cabecalho = $coluna;
  }

  function addCabecalho($coluna)
  {
    $this->cabecalho[] = $coluna;
  }

  function addLinhas($linha)
  {
    $this->linhas[] = $linha;
  }

  function addPaginador2($strUrl, $intTotalRegistros, $mixVariaveisMantidas = '',
    $nome = 'formulario', $intResultadosPorPagina = 20, $intPaginasExibidas = 3,
    $var_alteranativa = FALSE, $pag_modifier = 0, $add_iniciolimit = FALSE)
  {
    if ($intTotalRegistros > 0) {
      $getVar = "pagina_{$nome}";
      if ($var_alteranativa) {
        $getVar = $var_alteranativa;
      }

      if (isset($_GET[$getVar])) {
        $intPaginaAtual = $_GET[$getVar];
      }
      else {
        $intPaginaAtual = 1;
      }

      $pagStart = $intPaginaAtual - $intPaginasExibidas;
      $totalPaginas = ceil( $intTotalRegistros / $intResultadosPorPagina );

      if ($pagStart > $totalPaginas - $intPaginasExibidas * 2) {
        $pagStart = $totalPaginas - $intPaginasExibidas * 2;
      }
      if ($pagStart < 1) {
        $pagStart = 1;
      }

      $linkFixo = $strUrl . '?';

      if (is_array($mixVariaveisMantidas)) {
        foreach ($mixVariaveisMantidas as $key => $value) {
          if ($key != $getVar) {
            if (! ($add_iniciolimi && $key == 'iniciolimit')) {
              $linkFixo .= "$key=$value&";
            }
          }
        }
      }
      else {
        if (is_string($mixVariaveisMantidas)) {
          $linkFixo .= "$mixVariaveisMantidas&";
        }
      }

      /**
       * HTML do paginador.
       */
      $strReturn = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\"><tr>";

      // Setas de início e anterior
      $imagem = ($intPaginaAtual > 1) ? "seta" :"seta_transp";
      $compl_url = ($add_iniciolimit) ? "&iniciolimit=" . (1 + $pag_modifier): "";
      $strReturn .= "<td width=\"23\" align=\"center\"><a href=\"{$linkFixo}$getVar=" . (1 + $pag_modifier) . "{$compl_url}\" class=\"nvp_paginador\" title=\"Ir para a primeira pagina\"><img src=\"imagens/paginador/{$imagem}1.gif\" border=\"0\" alt=\"primeira pagina\"></a></td> ";
      $compl_url = ($add_iniciolimit) ? "&iniciolimit=" . max(1 + $pag_modifier, $intPaginaAtual - 1) : '';
      $strReturn .= "<td width=\"23\" align=\"center\"><a href=\"{$linkFixo}$getVar=" . max(1 + $pag_modifier, $intPaginaAtual - 1) . "{$compl_url}\" class=\"nvp_paginador\" title=\"Ir para a pagina anterior\"><img src=\"imagens/paginador/{$imagem}2.gif\" border=\"0\" alt=\"pagina anterior\"></a></td> ";

      // Meio
      $strReturn .= "<td align=\"center\"><img src=\"imagens/paginador/esq.gif\" border=\"0\" alt=\"\"></td>";
      $meios = array();

      for ($i = 0; $i <= $intPaginasExibidas * 2 && $i + $pagStart <= $totalPaginas; $i++) {
        $ordenacao = empty($_POST['ordenacao']) ? $_GET['ordenacao'] : $_POST['ordenacao'];

        $imagem     = ($pagStart + $i + $pag_modifier == $intPaginaAtual) ? '2' : '1';
        $compl_url  = ($add_iniciolimit) ? "&iniciolimit=" . ($pagStart + $i + $pag_modifier) : '';
        $strReturn .= "<td align=\"center\" style=\"padding-left:5px;padding-right:5px;background-image: url('imagens/paginador/bg{$imagem}.gif');\"><a href=\"{$linkFixo}$getVar=" . ( $pagStart + $i + $pag_modifier ) . "{$compl_url}&ordenacao={$ordenacao}\" class=\"nvp_paginador\" title=\"Ir para a p&aacute;gina " . ($pagStart + $i) . "\">" . addLeadingZero($pagStart + $i) ."</a></td>";
        $strReturn .= "<td><img src=\"imagens/paginador/separador.gif\" border=\"0\" alt=\"\"></td>";
      }

      $strReturn .= "<td align=\"center\"><img src=\"imagens/paginador/dir.gif\" border=\"0\" alt=\"\"></td>";

      // Setas de fim e próxima
      $imagem     = ($intPaginaAtual < $totalPaginas) ? 'seta' : 'seta_transp';
      $compl_url  = ($add_iniciolimit) ? "&iniciolimit=" . min($totalPaginas + $pag_modifier, $intPaginaAtual + 1) : '';
      $strReturn .= "<td width=\"23\" align=\"center\"><a href=\"{$linkFixo}$getVar=" . min($totalPaginas + $pag_modifier, $intPaginaAtual + 1) . "{$compl_url}\" class=\"nvp_paginador\" title=\"Ir para a proxima pagina\"><img src=\"imagens/paginador/{$imagem}3.gif\" border=\"0\" alt=\"proxima pagina\"></a></td> ";
      $compl_url  = ( $add_iniciolimit ) ? "&iniciolimit=" . ($totalPaginas + $pag_modifier): "";
      $strReturn .= "<td width=\"23\" align=\"center\"><a href=\"{$linkFixo}$getVar=" . ($totalPaginas + $pag_modifier) . "{$compl_url}\" class=\"nvp_paginador\" title=\"Ir para a ultima pagina\"><img src=\"imagens/paginador/{$imagem}4.gif\" border=\"0\" alt=\"ultima pagina\"></a></td> ";

      $strReturn .= "</tr></table>";

      $this->paginador2 = $strReturn;
    }
  }

  function addPaginador($argumento, $inicio, $buffer = FALSE)
  {
    $visual = 1 + $this->numeropaginador;

    if (!$buffer || (($this->numeropaginador > $inicio - 6) && ($this->numeropaginador < $inicio + 6))) {
      if ($inicio == $this->numeropaginador) {
        $this->paginador[] = array($visual, $argumento."&iniciolimit={$this->numeropaginador}", FALSE);
      }
      else {
        $this->paginador[] = array($visual, $argumento."&iniciolimit={$this->numeropaginador}", TRUE);
      }
    }

    $this->numeropaginador++;
  }

  /**
   * Cria o código HTML.
   *
   * @param string $caminho
   * @param int $qdt_registros
   * @param int $limite
   * @param string $link_atual
   * @return NULL
   */
  function paginador($caminho, $qdt_registros, $limite, $link_atual)
  {
    $this->addPaginador2('', $qdt_registros, $_GET, 'formulario', $limite, 3,
      'pos_atual', -1, TRUE);

    return NULL;
  } 

  function RenderHTML()
  {
    $this->_preRender();

    $this->Gerar();

    $retorno = '';

    if ($this->banner) {
      $retorno .= "<table width='100%' style=\"height:100%\" border='0' cellpadding='0' cellspacing='0'><tr>";
      $retorno .= "<td class=\"barraLateral\" width=\"21\" valign=\"top\"><a href='#'><img src=\"{$this->bannerLateral}\" align=\"right\" border=\"0\" alt=\"$this->titulo_barra\" title=\"$this->titulo_barra\"></a></td><td valign='top'>";
    }

    $retorno .= "
    <script type=\"text/javascript\">function go(url) { document.location = url; }
    var goodIE = (document.all) ? 1:0;
    var netscape6 = (document.getElementById && !document.all) ? 1:0;
    var aux = '';
    var aberto = false;";

    $retorno .= $this->MakeFormat();
    $retorno .= '</script>';

    if ($this->campos) {
      $width = empty($this->largura) ? '' : "width='$this->largura'";

      /**
       * Adiciona o help da página.
       */
      $url = parse_url($_SERVER['REQUEST_URI']);
      $url = ereg_replace( '^/', '', $url['path']);

      if (strpos($url, '_det.php') !== FALSE) {
        $tipo = 'det';
      }
      elseif (strpos($url, '_lst.php') !== FALSE) {
        $tipo = 'lst';
      }
      elseif (strpos($url, '_pdf.php') !== FALSE) {
        $tipo = 'pdf';
      }
      else {
        $tipo = 'cad';
      }
      
      $server = $_SERVER['SERVER_NAME'];
      $endereco = $_SERVER ['REQUEST_URI'];
      $enderecoPagina = $_SERVER['PHP_SELF'];
      
      //$barra = '<b>Localizacao: http://'.$server.$endereco.'</b><br>';
      //$barra = '<tr><td><b>Localizacao:'. $enderecoPagina .'</b><br></tr></td>';
      $barra = '<b>Filtros de busca</b>';  

      if (class_exists('clsPmiajudaPagina')) {
        $ajudaPagina = new clsPmiajudaPagina();
        $lista = $ajudaPagina->lista(null, null, $url);
        if ($lista) {
          $barra = "
          <table border=\"0\" cellpading=\"0\" cellspacing=\"0\" width=\"100%\">
            <tr>
            <script type=\"text/javascript\">document.help_page_index = 0;</script>
            <td width=\"20\"><a href=\"javascript:showExpansivelIframe(700,500,'ajuda_mostra.php?cod_topico={$lista[0]["ref_cod_topico"]}&tipo={$tipo}');\"><img src=\"imagens/banco_imagens/interrogacao.gif\" border=\"0\" alt=\"Botï¿½o de Ajuda\" title=\"Clique aqui para obter ajuda sobre esta pï¿½gina\"></a></td>
            <td><b>Filtros de busca</b></td>
            <td align=\"right\"><a href=\"javascript:showExpansivelIframe(700,500,'ajuda_mostra.php?cod_topico={$lista[0]["ref_cod_topico"]}&tipo={$tipo}');\"><img src=\"imagens/banco_imagens/interrogacao.gif\" border=\"0\" alt=\"Botï¿½o de Ajuda\" title=\"Clique aqui para obter ajuda sobre esta pï¿½gina\"></a></td>
            </tr>
          </table>";
        }
      }

      if ($this->busca_janela) {
        $janela .= "<form name='{$this->__nome}' id='{$this->__nome}' method='{$this->method}'>";
        $janela .= "<input name='busca' type='hidden' value='S'>";
        $janela .= "<table class='tablelistagem' border='0' cellpadding='2' cellspacing='1'>";

        if ($this->campos) {
          reset($this->campos);

          while (list($nome, $componente) = each($this->campos)) {
            if ($componente[0] == "oculto" || $componente[0] == "rotulo") {
              $janela .=  "<input name='$nome' id='$nome' type='hidden' value='".urlencode($componente[3])."'>";
            }
          }
        }

        $janela .= "<tr><td class='formdktd' colspan='2' height='24'>{$barra}</td></tr>";

        if (empty($this->campos)) {
          $janela .=  "<tr><td class='formlttd' colspan='2'><span class='form'>N&atilde;o existem campos definidos para o formul&aacute;rio</span></td></tr>";
        }
        else {
          $janela .= $this->MakeCampos();
        }

        $janela  .= "<tr><td class='formdktd' colspan='2'></td></tr>";
        $janela  .= "<tr><td colspan='2' align='center'>";
        $retorno .= "<script type=\"text/javascript\" language='javascript'>";

        if ($this->funcAcao) {
          $retorno .=  $this->funcAcao;
        }
        else {
          $retorno .=  "function acao{$this->funcAcaoNome}() { document.$this->__nome.submit(); } ";
        }

        $retorno .= "</script>";
        $janela  .= "<input type='button' class='botaolistagem' value='Busca' onclick='javascript:acao{$this->funcAcaoNome}();'>";
        $janela  .= "</td></tr>";
        $janela  .= "</table>";
        $janela  .= "</form>";

        $janela = str_replace("\"","'", $janela);
        $janela = str_replace("'","\'", $janela);
        $janela = str_replace("\n","", $janela);

        $retorno .= "<br><table class=\"tablelistagem\" width=\"90%\"  border=\"0\" cellpadding=\"3\" cellspacing=\"1\" align=\"center\"  >";
        $retorno .=  "<td align=\"center\" class='formdktd' colspan='2' height='24' valign='middle'><input type=\"button\" class=\"botaolistagem\" onclick=\"javascript:showExpansivel(0,0, '$janela');\" value=\"Pesquisar\">&nbsp;";
        $retorno .=  "</td></tr>";
        $retorno .=  "</table>";
      }
      else {
        $retorno .=  "<!-- begin formulario -->
        <form name='{$this->__nome}' id='{$this->__nome}' method='{$this->method}' action=\"\">
          <input name='busca' type='hidden' value='S'>";

        if ($this->campos) {
          reset($this->campos);

          while (list($nome, $componente) = each($this->campos)) {
            if ($componente[0] == 'oculto' || $componente[0] == 'rotulo') {
              $retorno .=  "<input name='$nome' id='$nome' type='hidden' value='".urlencode($componente[3])."'>";
            }
          }
        }

        $retorno .=  "
          <table class='tablelistagem' $width border='0' cellpadding='2' cellspacing='1'>";
        
        $retorno .=  "
            <tr>
              <td class='fundoLocalizacao' colspan='2' height='24'>{$this->locale}</td>
            </tr>";
              
        $retorno .=  "
            <tr>
              <td class='formdktd' colspan='2' height='24'>{$barra}</td>
            </tr>";

        if (empty($this->campos)) {
          $retorno .=  "
            <tr>
              <td class='formlttd' colspan='2'><span class='form'>N&atilde;o existem campos definidos para o formul&aacute;rio</span></td>
            </tr>";
        }
        else {
          $retorno .= $this->MakeCampos();
        }

        $retorno .=  "
            <tr>
              <td class='formdktd' colspan='2'></td>
            </tr>";
        $retorno .=  "
            <tr>
              <td colspan='2' align='center'>
                <script type=\"text/javascript\" language='javascript'>";

        if ($this->funcAcao) {
          $retorno .=  $this->funcAcao;
        }
        else {
          $retorno .=  "function acao{$this->funcAcaoNome}() { document.{$this->__nome}.submit(); } ";
        }

        $retorno .=  "</script>";

        if ($this->exibirBotaoSubmit) {
          if ($this->botao_submit) {
            $retorno .=  "&nbsp;<input type='submit' class='botaolistagem' value='busca' id='botao_busca'>&nbsp;";
          }
          else {
            $retorno .=  "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:acao{$this->funcAcaoNome}();' value='busca' id='botao_busca'>&nbsp;";
          }
        }

        $retorno .=  "
              </td>
            </tr>
          </table>
        <!-- cadastro end -->
        </form>";
      }
    }

    $retorno .=  "<br>";
    $ncols = 1;
    $width = empty($this->largura) ? '' : "width='$this->largura'";

    if (! $this->__titulo) {
      // Recebe a variavel titulo por motivos de compatibilidade com scripts antigos
      $this->__titulo = $this->titulo;
    }

    $this->method = 'POST';

    $retorno .=  "
        <form name=\"form_resultado\" id=\"form_resultado\" method=\"POST\" action=\"\">
        <!-- listagem begin -->
          <table class='tablelistagem' $width border='0' cellpadding='4' cellspacing='1'>
            <tr>
              <td colspan='$ncols'>{$this->__titulo}</td>
            </tr>";

    $ncols = count( $this->cabecalho );

    // Cabeçalho
    if (!empty($this->cabecalho)) {
      reset($this->cabecalho);

      $ncols = count($this->cabecalho);

      if (!empty($this->colunas)) {
        reset( $this->colunas );
      }

      $retorno .= "<input type='hidden' id='ordenacao' name='ordenacao' value='{$_POST['ordenacao']}'>";
      $retorno .= "<input type='hidden' id='fonte' name='fonte' value='{$_POST['fonte']}'>";
      $retorno .=  "
            <tr>";

      while (list($i, $texto) = each($this->cabecalho)) {
        if (!empty( $this->colunas )) {
          list($i, $fmt) = each($this->colunas);
        }
        else {
          $fmt = alTopLeft;
        }

        if ($texto) {
          $inicio = $fim = '';

          if ($this->campos_ordenacao[$i] != '') {
            $_POST['fonte']  = empty($_POST['fonte']) ? "imagens/nvp_setinha_down.gif" : $_POST['fonte'];
            $inicio = "<img name='seta' src='{$_POST['fonte']}' border='0' /> <a href='#' onclick='definirOrdenacao(\"{$this->campos_ordenacao[$i]}\");document.getElementById(\"form_resultado\").submit();'>";
            $fim = "</a>";
          }

          $retorno .=  "
              <td class='formdktd' $fmt style=\"font-weight:bold;\" valign='middle'>{$inicio}$texto{$fim}</td>";
        }
      }

      $retorno .=  "
            </tr>";
    }

    // Lista
    if (empty($this->linhas)) {
      $retorno .=  "
            <tr>
              <td class='formlttd' colspan='$ncols' align='center'>N&atilde;o h&aacute; informa&ccedil;&atilde;o para ser apresentada</td>
            </tr>";
    }
    else {
      reset($this->linhas);

      while (list($i, $linha) = each($this->linhas)) {
        $classe = ($i % 2) ? 'formmdtd' : 'formlttd';
        $retorno .=  "
            <tr>";

        if (is_array($linha)) {
          reset($linha);

          if (!empty($this->colunas)) {
            reset( $this->colunas );
          }

          while (list($i, $celula) = each($linha)) {
            if (!empty( $this->colunas)) {
              list($i, $fmt) = each($this->colunas);
            }
            else {
              $fmt = alTopLeft;
            }

            if (strpos($celula, "<img src='imagens/noticia.jpg' border=0>" ) !== FALSE) {
              $celula = str_replace("<img src='imagens/noticia.jpg' border=0>", "<img src='imagens/noticia.jpg' border=0 alt=''>", $celula);
            }

            $retorno .=  "
              <td class='$classe' $fmt>$celula</td>";
          }
        }
        else {
          $retorno .=  "
              <td class='formdktd' $fmt colspan='$ncols'>$linha</td>";
        }

        $retorno .=  "
            </tr>";
      }
    }

    $retorno .=  "
            <tr>
              <td class='formdktd' colspan=\"{$ncols}\">&nbsp;</td>
            </tr>";

    if (!empty($this->paginador2)) {
      $retorno .= "
            <tr>
              <td align=\"center\" colspan=\"$ncols\">{$this->paginador2}</td>
            </tr>";
    }

    if (!empty($this->paginador)) {
      $ua = 0;
      $qdt_paginador = 1;
      $i = 0;
      $retorno .=  "
            <tr>
              <td colspan='$ncols' align='center'>";

      foreach ($this->paginador as $pagina) {
        if ($pagina[2]) {
          switch ($pagina[0]) {
            case "<<":
              $retorno .=  "<a href='{$pagina[1]}' class='nvp_paginador'>{$pagina[0]}</a>&nbsp;&nbsp;&nbsp;&nbsp;";
              break;
            case "-10":
              $retorno .=  "<a href='{$pagina[1]}' class='nvp_paginador'>{$pagina[0]}</a>&nbsp;&nbsp;&nbsp;&nbsp;";
              break;
            case ">>":
              $retorno .=  "&nbsp;&nbsp;&nbsp;&nbsp;<a href='{$pagina[1]}' class='nvp_paginador'>{$pagina[0]}</a>";
              break;
            case "p10":
              $retorno .=  "&nbsp;&nbsp;&nbsp;&nbsp;<a href='{$pagina[1]}' class='nvp_paginador'>+10</a>";
              break;
            default:
              $retorno .=  "<a href='{$pagina[1]}' class='nvp_paginador'>{$pagina[0]}</a>&nbsp;";
          }
        }
        else {
          $retorno .=  "<span class='linkBory' style='text-decoration: underline; color: black;'> {$pagina[0]} </span>&nbsp;";
        }

        if ($ua++ > 15) {
          $ua = 0;
          $retorno .= "<br>";
        }
      }

      $retorno .=  "
              </td>
            </tr>";
    }

    if ($this->tituloFormResultado) {
      $retorno .=  "
            <tr>
              <td class='formdktd' colspan=\"$ncols\" height='24'><span class='form'><b>{$this->tituloFormResultado}</b></span></td>
            </tr>";
    }

    if (!empty($this->camposResultado)) {
      reset($this->camposResultado);
      $campo_anterior = '';
      $md = TRUE;

      while (list($nome, $componente) = each( $this->camposResultado)) {
        if ($componente[0] != 'oculto') {
          $tipo = $componente[0];
          $campo = $componente[1] . ':';
          if ($campo == $campo_anterior) {
            $campo = '';
          }
          else {
            $campo_anterior = $campo;
            $md = !$md;
          }

          $classe = $md ? 'formmdtd' : 'formlttd';

          $retorno .= "
            <tr>
              <td class='$classe' valign='top'><span class='form'>$campo</span></td>
              <td class='$classe' valign='top'>
                <span class='form'>
                  ";

          switch ($tipo) {
            case "rotulo":
              $retorno .=  $componente[2];
              break;
            case "texto" :
              $retorno .=  "
                <input class='form' type='text' name='$nome' value='$componente[2]' size='$componente[3]' maxlength='$componente[4]'>\n";
              break;
            case "memo":
              $retorno .=  "
                <textarea class='form' name='$nome' cols='$componente[3]' rows='$componente[4]' wrap='virtual' >$componente[2]</textarea>\n";
              break;
            case "lista":
              $retorno .=  "
                <select class='form' name='$nome'>\n";

              reset($componente[2]);
              while (list($chave, $texto) = each($componente[2])) {
                $retorno .=  "<option value='" . urlencode($chave) . "'";

                if ($chave == $componente[3]) {
                  $retorno .=  ' selected';
                }

                $retorno .=  ">$texto</option>\n";
              }
              $retorno .=  "</select>\n";
              break;
          }

          $retorno .=  "
                </span>
              </td>
            </tr>";
        }
      }
    }

    $botao = '';

    if($this->acao_voltar) {
      $botao = "&nbsp;&nbsp;&nbsp;<input type='button' class='botaolistagem' onclick='javascript: $this->acao_voltar' value=' Voltar '>";
    }
    if($this->acao_imprimir) {
      $botao = "&nbsp;&nbsp;&nbsp;<input type='button' id='imprimir' class='botaolistagem' onclick='javascript: $this->acao_imprimir' value='$this->valor_imprimir'>";
    }
    if ($this->acao && $this->show_botao_novo) {
      $retorno .=  "
            <tr>
              <td colspan=\"$ncols\" align=\"center\"><input type='button' class='botaolistagem' onclick='javascript: $this->acao' value=' $this->nome_acao '>$botao</td>
            </tr>";
    }
    elseif ($this->acao_imprimir) {
      $retorno .=  "
            <tr>
              <td colspan=\"$ncols\" align=\"center\">$botao</td>
            </tr>";
    }

    $retorno .= "
            <tr>
              <td colspan=\"$ncols\" align=\"center\">";

    if (count($this->array_botao_script)) {
      for ($i = 0; $i < count($this->array_botao); $i++) {
        $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='". $this->array_botao_script[$i]."' value='".$this->array_botao[$i]."'>&nbsp;\n";
      }
    }
    else {
      for ($i = 0; $i < count($this->array_botao); $i++) {
        $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"".$this->array_botao_url[$i]."\" );' value='".$this->array_botao[$i]."'>&nbsp;\n";
      }
    }

    $retorno .= "</td>
            </tr>";

    if (!is_null($this->rodape)) {
      $retorno .= "<tr><td colspan=\"$ncols\" align=\"center\" id=\"td_rodape\">\n";
      $retorno .= $this->rodape;
      $retorno .= "
              </td>
            </tr>";
    }

    $retorno .= "
        </table>
      </form>
      <!-- listagem end -->";

    if ($this->bannerClose) {
      $retorno .= "
              <!-- Fechando o Banner (clsListagem) -->
            </td>
          </tr>
        </table>
      ";
    }

    Portabilis_View_Helper_Application::embedJavascriptToFixupFieldsWidth($this);

    return $retorno;
  }

  /**
   * Exibe mensagem de DIE formatada;
   * @param String $msg
   * @param String $url Redirecionar após 1 segundo
   */
  function erro($msg, $redir = 'index.php')
  {
    die("<div style='width: 300px; height: 100px; font: 700 11px Arial,Helv,Sans; background-color: #f6f6f6; color: #e11; position: absolute; left: 50%; top: 50%; margin-top: -20px; margin-left: -100px; text-align: center; border: solid 1px #a1a1f1;'>{$msg}</div><script>setTimeout('window.location=\'$redir\'',5000);</script>");
  }

  public function inputsHelper() {
    if (! isset($this->_inputsHelper))
      $this->_inputsHelper = new Portabilis_View_Helper_Inputs($this);

    return $this->_inputsHelper;
  }
}
