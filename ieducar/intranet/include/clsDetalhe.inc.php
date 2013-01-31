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

require_once 'Core/Controller/Page/Abstract.php';

/**
 * clsDetalhe class.
 *
 * Cria um template para a visualização de um registro de alguma tabela do banco
 * de dados.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Include
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsDetalhe extends Core_Controller_Page_Abstract
{
  var $titulo;
  var $banner = FALSE;
  var $bannerLateral = FALSE;
  var $titulo_barra;
  var $bannerClose = FALSE;
  var $largura;
  var $detalhe = array();

  var $url_novo;
  var $caption_novo = "Novo";
  var $url_editar;
  var $url_cancelar;
  var $nome_url_cancelar = "Voltar";

  var $array_botao;
  var $array_botao_url;
  var $array_botao_url_script;

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


  function addDetalhe($detalhe) {
    $this->detalhe[] = $detalhe;
  }

  function Gerar() {
    return FALSE;
  }

  function RenderHTML()
  {
    $this->_preRender();

    $this->titulo_barra= 'Intranet';
    $this->Gerar();

    $retorno = '';
    if ($this->banner) {
      $retorno .= "<table width='100%' style=\"height:100%\" border='0' cellpadding='0' cellspacing='0'><tr>";
      $retorno .= "<td class=\"barraLateral\" width=\"21\" valign=\"top\"><a href='#'><img src=\"{$this->bannerLateral}\" align=\"right\" border=\"0\" alt=\"$this->titulo_barra\" title=\"$this->titulo_barra\"></a></td><td valign='top'>";
    }

    $script = explode('/', $_SERVER['PHP_SELF']);
    $script = $script[count($script)-1];

    $width = empty($this->largura) ? '' : 'width=' . $this->largura;

    /*
     * adiciona os botoes de help para a pagina atual
     */
    $url = parse_url($_SERVER['REQUEST_URI']);
    $url = ereg_replace( "^/", "", $url["path"] );

    if (strpos($url, '_det.php') !== FALSE) {
      $tipo = "det";
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

    $barra = '<b>' . $this->titulo . '</b>';
    if (class_exists('clsPmiajudaPagina')) {
      $ajudaPagina = new clsPmiajudaPagina();
      $lista = $ajudaPagina->lista(null,null,$url);

      if ($lista) {
        $barra = "
          <table border=\"0\" cellpading=\"0\" cellspacing=\"0\" width=\"100%\">
            <tr>
              <script type=\"text/javascript\">document.help_page_index = 0;</script>
              <td width=\"20\"><a href=\"javascript:showExpansivelIframe(700,500,'ajuda_mostra.php?cod_topico={$lista[0]["ref_cod_topico"]}&tipo={$tipo}');\"><img src=\"imagens/banco_imagens/interrogacao.gif\" border=\"0\" alt=\"Botï¿½o de Ajuda\" title=\"Clique aqui para obter ajuda sobre esta pï¿½gina\"></a></td>
              <td><b>{$this->titulo}</b></td>
              <td align=\"right\"><a href=\"javascript:showExpansivelIframe(700,500,'ajuda_mostra.php?cod_topico={$lista[0]["ref_cod_topico"]}&tipo={$tipo}');\"><img src=\"imagens/banco_imagens/interrogacao.gif\" border=\"0\" alt=\"Botï¿½o de Ajuda\" title=\"Clique aqui para obter ajuda sobre esta pï¿½gina\"></a></td>
            </tr>
          </table>";
      }
    }

    $retorno .= "
      <!-- detalhe begin -->
      <table class='tableDetalhe' $width border='0' cellpadding='2' cellspacing='2'>
        <tr>
          <td class='formdktd' colspan='2' height='24'>{$barra}</td>
        </tr>
      ";

    if (empty($this->detalhe)) {
      $retorno .= "<tr><td class='tableDetalheLinhaSim' colspan='2'>N&atilde;o h&aacute; informa&ccedil;&atilde;o a ser apresentada.</td></tr>\n";
    }
    else
    {
      if (is_array($this->detalhe))
      {
        reset($this->detalhe);

        $campo_anterior = "";
        $md = TRUE;

        foreach ($this->detalhe as $pardetalhe)
        {
          if (is_array($pardetalhe))
          {
            $campo = $pardetalhe[0].":";
            $texto = $pardetalhe[1];

            if ($campo == $campo_anterior)
            {
              $campo = "";
            }
            else
            {
              $campo_anterior = $campo;
              $md = !$md;
            }

            if ($campo == "-:")
            {
              if (empty($texto))
              {
                $texto = '&nbsp;';
              }
              $retorno .= "<tr><td colspan='2' class='' width='20%'><span class='form'><b>$texto</b></span></td></tr>\n";
            }
            else
            {
              $classe = $md ? 'formmdtd' : 'formlttd';
              $retorno .= "<tr><td class='$classe' width='20%'>$campo</td><td class='$classe'>$texto</td></tr>\n";
            }
          }
          else
          {

            $retorno .= "<tr><td colspan='2'>$pardetalhe</td></tr>";

          }
        }
      }
    }

    $retorno .= "<tr><td class='tableDetalheLinhaSeparador' colspan='2'></td></tr>\n";

    if (!empty($this->url_editar) || !empty($this->url_cancelar) || $this->array_botao) {
      $retorno .= "
        <tr>
          <td colspan='2' align='center'>
            <script type='text/javascript'>
              function go(url) {
                document.location = url;
              }
            </script>";

      if ($this->url_novo) {
        $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"$this->url_novo\" );' value=' {$this->caption_novo} '>&nbsp;\n";
      }

      if ($this->url_editar) {
        $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"$this->url_editar\" );' value=' Editar '>&nbsp;\n";
      }

      if ($this->url_cancelar) {
        $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"$this->url_cancelar\" );' value=' $this->nome_url_cancelar '>&nbsp;\n";
      }
      $retorno .= "</td></tr>";

      if ($this->array_botao_url || $this->array_botao_url_script) {
        $retorno .= "<tr><td colspan=2><table width='100%' summary=''><tr><td></td><td height='1' width='90%' bgcolor='#858585' style='font-size: 0px;'>&nbsp;</td><td></td></tr></table></td></tr><tr><td colspan='2' align='center'>";
      }

      if ($this->array_botao_url) {
        for ($i = 0, $total = count($this->array_botao); $i < $total; $i++) {
          $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"".$this->array_botao_url[$i]."\" );' value='".$this->array_botao[$i]."'>&nbsp;\n";
        }
      }
      elseif ($this->array_botao_url_script) {
        for ($i = 0, $total = count($this->array_botao); $i < $total; $i++) {
          $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='{$this->array_botao_url_script[$i]}' value='".$this->array_botao[$i]."'>&nbsp;\n";
        }
      }

      if ($this->array_botao_url || $this->array_botao_url_script) {
        $retorno .= "</td></tr>";
      }

      $retorno .= "<tr><td colspan='2' height='1' bgcolor='black' style='font-size: 0px;'>&nbsp;</td></tr>";
    }

    $retorno .= "
      </table><br><br>
      <!-- detalhe end -->";

    if ($this->bannerClose) {
      $retorno .= "
        <!-- Fechando o Banner (clsDetalhe) -->
          </td>
        </tr>
        </table>";
    }

    return $retorno;
  }
}
