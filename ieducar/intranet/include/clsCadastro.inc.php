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

if (class_exists('clsPmiajudaPagina')) {
  require_once 'include/pmiajuda/clsPmiajudaPagina.inc.php';
}

require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/View/Helper/Inputs.php';
require_once 'Portabilis/Utils/User.php';

/**
 * clsCadastro class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Include
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsCadastro extends clsCampos
{
  var $__nome = 'formcadastro';
  var $banner;
  var $bannerLateral;
  var $titulo_barra;
  var $target = '_self';

  var $largura;
  var $tipoacao;
  var $campos;
  var $erros;
  var $mensagem;

  var $nome_pai;
  var $chave;
  var $item_campo_pai;

  var $fexcluir;
  var $excluir_Img;
  var $nome_excluirImg;
  var $url_cancelar;
  var $nome_url_cancelar;
  var $url_sucesso;
  var $nome_url_sucesso;
  var $action;
  var $script_sucesso;
  var $script_cancelar;
  var $script;
  var $submete = FALSE;
  var $acao_executa_submit = TRUE;
  var $executa_submete = FALSE;
  var $bot_alt = FALSE;
  var $nome_url_alt;
  var $url_alt;
  var $help_images = FALSE;

  var $array_botao;
  var $array_botao_url;
  var $array_botao_id;
  var $array_botao_url_script;
  var $controle;
  var $acao_enviar ='acao()';
  var $botao_enviar = TRUE;

  var $onSubmit = 'acao()';

  var $form_enctype;

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

  function clsCadastro()
  {
    parent::__construct();
    $this->tipoacao = @$_POST['tipoacao'];
  }

  function PreCadastrar()
  {
  }

  function Processar()
  {
    $this->excluir = @$_GET['excluir'];

    if ($this->excluir) {
      $this->tipoacao = "Excluir";
    }
    if (empty($this->tipoacao)) {
      $this->tipoacao = $this->Inicializar();
      $this->Formular();
    }
    else {
      reset($_POST);
      while (list($variavel, $valor) = each($_POST)) {
        $this->$variavel = $valor;
      }

      reset($_FILES);
      while (list($variavel, $valor) = each($_FILES)) {
        $this->$variavel = $valor;
      }

      // Realiza cadastro
      $this->PreCadastrar();
      $sucesso = FALSE;

      if ($this->tipoacao == 'Novo') {
          $sucesso = $this->Novo();
          if ($sucesso && !empty($this->script_sucesso)) {
            $this->script = "<script type=\"text/javascript\">
              window.opener.AdicionaItem($this->chave, '$this->item_campo_pai', '$this->nome_pai', $this->submete );
              window.close();
            </script>";
          }

          if (!$sucesso && empty($this->erros) && empty($this->mensagem)) {
            $this->mensagem = "N&atilde;o foi poss&iacute;vel inserir a informa&ccedil;&atilde;o. [CAD01]";
          }
      }
      elseif ($this->tipoacao == 'Editar') {
          $sucesso = $this->Editar();
          if (!$sucesso && empty($this->erros) && empty($this->mensagem)) {
            $this->mensagem = "N&atilde;o foi poss&iacute;vel editar a informa&ccedil;&atilde;o. [CAD02]";
          }
      }
      elseif ($this->tipoacao == 'Excluir') {
        $sucesso = $this->Excluir();
        if (!$sucesso && empty($this->erros) && empty($this->mensagem)) {
          $this->mensagem = "N&atilde;o foi poss&iacute;vel excluir a informa&ccedil;&atilde;o. [CAD03]";
        }
      }
      elseif ($this->tipoacao == 'ExcluirImg') {
        $sucesso = $this->ExcluirImg();
        if (!$sucesso && empty( $this->erros ) && empty( $this->mensagem )) {
          $this->mensagem = "N&atilde;o foi poss&iacute;vel excluir a informa&ccedil;&atilde;o. [CAD04]";
        }
      }
      if (empty($script) && $sucesso && !empty($this->url_sucesso)) {
        redirecionar( $this->url_sucesso );
      }
      else {
        $this->Formular();
      }
    }
  }

  function Inicializar()
  {
  }

  function Formular()
  {
  }

  function Novo()
  {
    return FALSE;
  }

  function Editar()
  {
    return FALSE;
  }

  function Excluir()
  {
    return FALSE;
  }

  function ExcluirImg()
  {
    return FALSE;
  }

  function Gerar()
  {
    return FALSE;
  }

  protected function flashMessage() {
    if (empty($this->mensagem) && isset($_GET['mensagem']) && $_GET['mensagem'] == 'sucesso') {
      $this->mensagem = 'Registro incluido com sucesso!';
    }

    return empty($this->mensagem) ? "" : "<p class='form_erro error'>$this->mensagem</p>";
  }

  function RenderHTML()
  {
    $this->_preRender();

    $this->bannerLateral = 'imagens/nvp_vert_intranet.jpg';
    $this->titulo_barra = 'Intranet';
    $this->Processar();

    $retorno = '';

    if ($this->banner) {
      $retorno .= "<table width='100%' style=\"height:100%\" border='0' cellpadding='0' cellspacing='0'><tr>";
      $retorno .= "<td class=\"barraLateral\" width=\"21\" valign=\"top\"><a href='#'><img src=\"{$this->bannerLateral}\" align=\"right\" border=\"0\" alt=\"$this->titulo_barra\" title=\"$this->titulo_barra\"></a></td><td valign='top'>";
    }

    $this->Gerar();

    $script = explode("/", $_SERVER["PHP_SELF"]);
    $script = $script[count($script)-1];

    $this->nome_excluirImg = empty( $this->nome_excluirImg ) ? "Excluir Imagem" : $this->nome_excluirImg;
    $this->nome_url_cancelar = empty( $this->nome_url_cancelar ) ? "Cancelar" : $this->nome_url_cancelar;
    $this->nome_url_sucesso = empty( $this->nome_url_sucesso ) ? "Salvar" : $this->nome_url_sucesso;

    $width = empty($this->largura) ? "width='100%'" : "width='$this->largura'";

    $retorno .=  "\n<!-- cadastro begin -->\n";
    $retorno .=  "<form name='$this->__nome' id='$this->__nome' onsubmit='return $this->onSubmit' action='$this->action'  method='post' target='$this->target' $this->form_enctype>\n";
    $retorno .=  "<input name='tipoacao' id='tipoacao' type='hidden' value='$this->tipoacao'>\n";
    $retorno .=  "<input name='__sequencia_fluxo' id='__sequencia_fluxo' type='hidden' value='$this->__sequencia_fluxo'>";

    if ($this->campos) {
      reset($this->campos);

      while (list($nome, $componente) = each($this->campos)) {
        if ($componente[0] == 'oculto' || $componente[0] == 'rotulo') {
          $retorno .=  "<input name='$nome' id='$nome' type='hidden' value='".urlencode($componente[3])."'>\n";
        }
      }
    }

    $retorno .= "<center>\n<table class='tablecadastro' $width border='0' cellpadding='2' cellspacing='0'>\n";
    $titulo = $this->titulo ? $this->titulo : "<b>{$this->tipoacao} {$this->titulo_aplication}</b>";

    /**
     * Adiciona os botoes de help para a pagina atual
     */
    $url = parse_url($_SERVER['REQUEST_URI']);
    $url = ereg_replace('^/', '', $url['path']);
    if (strpos($url, '_det.php') !== FALSE) {
      $tipo = 'det';
    }
    elseif(strpos($url,'_lst.php') !== FALSE) {
      $tipo = 'lst';
    }
    elseif(strpos($url,'_pdf.php') !== FALSE) {
      $tipo = 'pdf';
    }
    else {
      $tipo = 'cad';
    }
    $barra = $titulo;

    // @todo Remover código, funcionalidade não existente.
    if (class_exists('clsPmiajudaPagina')) {
      $ajudaPagina = new clsPmiajudaPagina();
      $lista = $ajudaPagina->lista(null,null,$url);
      if ($lista) {
        $barra = "
        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
          <tr>
          <script type=\"text/javascript\">document.help_page_index = 0;</script>
          <td width=\"20\"><a href=\"javascript:showExpansivelIframe(700,500,'ajuda_mostra.php?cod_topico={$lista[0]["ref_cod_topico"]}&tipo={$tipo}');\"><img src=\"imagens/banco_imagens/interrogacao.gif\" border=\"0\" alt=\"Botão de Ajuda\" title=\"Clique aqui para obter ajuda sobre esta página\"></a></td>
          <td>{$titulo}</td>
          <td align=\"right\"><a href=\"javascript:showExpansivelIframe(700,500,'ajuda_mostra.php?cod_topico={$lista[0]["ref_cod_topico"]}&tipo={$tipo}');\"><img src=\"imagens/banco_imagens/interrogacao.gif\" border=\"0\" alt=\"Botão de Ajuda\" title=\"Clique aqui para obter ajuda sobre esta página\"></a></td>
          </tr>
        </table>";
      }
    }

    $retorno .= "<tr><td class='formdktd' colspan='2' height='24'>{$barra}</td></tr>";

    $flashMessage = $this->flashMessage();

    if (! empty($flashMessage)) {
      $retorno .=  "<tr><td class='formmdtd' colspan='2' height='24'><div id='flash-container'>{$flashMessage}</div></td></tr>";
    }

    if (empty($this->campos)) {
      $retorno .=  "<tr><td class='linhaSim' colspan='2'><span class='form'>N&atilde;o existe informa&ccedil;&atilde;o dispon&iacute;vel</span></td></tr>";
    }
    else {
      // Verifica se houve erros no controller
      $retorno .= $this->_getControllerErrors();
      $retorno .= $this->MakeCampos();
    }

    $retorno .=
    "<tr><td class='tableDetalheLinhaSeparador' colspan='2'></td></tr>
    <tr class='linhaBotoes'><td colspan='2' align='center'>
    <script type=\"text/javascript\">
    var goodIE = (document.all) ? 1:0;
    var netscape6 = (document.getElementById && !document.all) ? 1:0;
    var aux = '';
    var aberto = false;";

    $retorno .= $this->MakeFormat();
    $retorno .= "
    function setColor(color)
    { \n";
      reset($this->campos);
      while (list($nome, $componente) = each($this->campos)) {
        $validador = $componente[4];

        if (!empty($validador)) {
          if ($validador == 'cor') {
            $retorno .=  "
            if (color) {
              document.$this->__nome.$nome.value = color;
            }
            document.getElementById('".$nome."1').style.background = '#' + document.$this->__nome.$nome.value; ";
          }
        }
      }

    $retorno .= "}\n";
    $retorno .=  "function acao(){ ";

    unset($this->campos['desabilitado_tab']);
    unset($this->campos['cabecalho_tab']);

    reset($this->campos);

    while (list($nome, $componente) = each($this->campos)) {
      $nomeCampo = $componente[0];
      $validador = $componente[2];

      if (empty($validador) && $nomeCampo == 'cpf' && ereg("^(tab_add_[0-9])", $nome) !== 1) {
        $retorno .=
        "if( document.getElementById('$nome').value != \"\")
        {
          if (! (/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/.test(document.getElementById('$nome').value) ) )
          {

            alert('Preencha o campo $nome Corretamente');
            return false;
          }else
          {
            if(! DvCpfOk( document.getElementById('$nome')) ) return false;
          }
        }";
      }

      /**
       * Campo tabela
       */
      if (ereg("^(tab_add_[0-9])", $nome) === 1) {
        $nome_campos = $componente['cabecalho'];
        $componente = array_shift($componente);

        unset($componente['oculto']);
        reset($componente);

        $ct_campo = 0;
        $retorno .= "for(var id_campo=0;id_campo<$nome.getId();id_campo++)\n{\n";

        while (list($name,$componente_campo ) = each( $componente)) {
          $nomeCampo =$componente_campo[1];
          $validador = $componente_campo[2];

          if(!empty($validador)) {
            if($componente_campo[0] == 'idFederal') {
              $campo = "document.getElementById(\"{$nomeCampo}[\"+id_campo+\"]\")";
              $validador= explode('+', $validador);
              $retorno .= " if (";
              $retorno .= "!({$validador[0]}.test( $campo.value ))) { \n";
              $retorno .= "if( !({$validador[1]}.test( $campo.value ))) { ";
              $retorno .= " alert( 'Preencha o campo \'{$nome_campos[$ct_campo]}\' corretamente!' ); \n  return false; }";
              $retorno .= "else { if(! DvCnpjOk( $campo) ) return false; }  }";
              $retorno .= "else{ if(! DvCpfOk( $campo) ) return false; }";
            }
            elseif ($componente_campo[0] != 'oculto') {
              $campo = "document.getElementById(\"{$nomeCampo}[\"+id_campo+\"]\")";
              $fim_for = "";
              if ($validador[0] == '*') {
                $validador = substr( $validador, 1 );
                $campo = "campos";
                $retorno .= " var campos = document.getElementById('{$nomeCampo}['+id_campo+']');\n
                              if(campos.value!='' &&
                          ";
              }
              else {
                $retorno .=  " \n if (";
              }

              $retorno .=  "!($validador.test( $campo.value )))\n";
              $retorno .=  "{\n";

              $retorno .=  " mudaClassName( 'formdestaque', 'obrigatorio' );\n";
              $retorno .=  " $campo.className = \"formdestaque\";\n";
              $retorno .=  " alert( 'Preencha o campo \'" . extendChars( $nome_campos[$ct_campo], true ) . "\' corretamente!' ); \n";
              $retorno .=  " $campo.focus(); \n";
              $retorno .=  " return false;\n";
              $retorno .=  "}\n{$fim_for}";
            }

            if (!empty($nomeCampo)) {
              if ($nomeCampo == 'cpf') {
                $retorno .= " else { if(! DvCpfOk( document.getElementById('{$nomeCampo}['+id_campo+']')) ) return false; }";
              }
            }
            if (!empty($nomeCampo)) {
              if ($nomeCampo == 'cnpj' || $nomeCampo == 'cnpj_pesq') {
                $retorno .= " else { if(document.getElementById('{$nomeCampo}['+id_campo+']').value != ''){ if(! DvCnpjOk( document.getElementById('{$nomeCampo}['+id_campo+']')) ) return false; }}";
              }
            }
          }

          if (empty($validador) && $nomeCampo == 'cpf') {
            $retorno .=
            "if( document.getElementById('{$nomeCampo}['+id_campo+']').value != \"\") {
              if (! (/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/.test(document.getElementById('{$nomeCampo}['+id_campo+']').value))) {
                alert('Preencha o campo \'{$nome_campos[$ct_campo]}\' Corretamente');
                document.getElementById('{$nomeCampo}['+id_campo+']').focus();
                return false;
              }
              else {
                if (! DvCpfOk(document.getElementById('{$nomeCampo}['+id_campo+']'))) {
                  document.getElementById('{$nomeCampo}['+id_campo+']').focus();
                  return false;
                }
              }
            }";
          }

          $ct_campo++;
        }

        $retorno .= "\n}\n";
        continue;
      }

      if (!empty($validador)) {
        if ($validador == 'lat') {
          $retorno .= "if(!(/^-2[5-9]/.test( document.$this->__nome.".$nome."_graus.value ))) { \n";
          $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
          $retorno .= " document.$this->__nome.".$nome."_graus.focus(); \n";
          $retorno .= " return false; } ";

          $retorno .= "if(!(/^([0-5])?[0-9]$/.test( document.$this->__nome.".$nome."_min.value ))) { \n";
          $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
          $retorno .= " document.$this->__nome.".$nome."_min.focus(); \n";
          $retorno .= " return false; } ";

          $retorno .= "if(!(/^([0-5])?[0-9]$/.test( document.$this->__nome.".$nome."_seg.value ))) { \n";
          $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
          $retorno .= " document.$this->__nome.".$nome."_seg.focus(); \n";
          $retorno .= " return false; } ";
        }
        elseif ($validador == 'lon') {
          $retorno .= "if(!(/^(-4[7-9])|(-5[0-4])/.test( document.$this->__nome.".$nome."_graus.value ))) { \n";
          $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
          $retorno .= " document.$this->__nome.".$nome."_graus.focus(); \n";
          $retorno .= " return false; } ";

          $retorno .= "if(!(/^([0-5])?[0-9]$/.test( document.$this->__nome.".$nome."_min.value ))) { \n";
          $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
          $retorno .= " document.$this->__nome.".$nome."_min.focus(); \n";
          $retorno .= " return false; } ";

          $retorno .= "if(!(/^([0-5])?[0-9]$/.test( document.$this->__nome.".$nome."_seg.value ))) { \n";
          $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
          $retorno .= " document.$this->__nome.".$nome."_seg.focus(); \n";
          $retorno .= " return false; } ";
        }
        else {
          if ($nomeCampo == 'idFederal') {
            $validador= explode('+',$validador);
            $retorno .=  " if (";
            $retorno .=  "!({$validador[0]}.test( document.getElementById('$nome').value ))) { \n";
            $retorno .=  "if( !({$validador[1]}.test( document.getElementById('$nome').value ))) { ";
            $retorno .=  " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n  return false; }";
            $retorno .=  "else { if(! DvCnpjOk( document.getElementById('$nome')) ) return false; }  }";
            $retorno .=  "else{ if(! DvCpfOk( document.getElementById('$nome')) ) return false; }";
          }
          elseif ($nomeCampo == 'listaativarpeso') {
            $retorno .=  "if(!($validador.test( document.{$this->__nome}.{$nome}_val.value ))) { \n";
            $retorno .=  " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
            $retorno .=  " document.$this->__nome.{$nome}_val.focus(); \n";
            $retorno .=  " return false; } ";
          }
          else {
            //substituito referencia a elementos por padrão W3C document.getElementById()
            //quando se referenciava um nome de elemento como um array ex: cadastro[aluno]
            //nao funcionava na referencia por nome
            //16-08-2006
            $retorno .=  ' if (';

            if ($validador[0] == '*') {
              $validador = substr( $validador, 1 );
              $retorno .=  "document.getElementById(\"{$nome}\").value!='' && ";
            }

            $retorno .=  "!($validador.test( document.getElementById(\"{$nome}\").value )))\n";
            $retorno .=  "{\n";
            $retorno .=  "  mudaClassName( 'formdestaque', 'obrigatorio' );\n";
            $retorno .=  "  document.getElementById(\"{$nome}\").className = \"formdestaque\";\n";
            $retorno .=  "  alert( 'Preencha o campo \'" . extendChars( $componente[1], true ) . "\' corretamente!' ); \n";

            if ($this->__nm_tab) {
              $retorno .= "
                  var item = document.getElementById('$nome');
                  var prox = 1;
                  do{
                    item = item.parentNode;
                    if(item == null)
                    {
                      prox = 0;
                    }
                    else
                    {
                      if(/content[0-9]+/.exec(item.id) != null)
                      {
                        prox = 2;
                      }
                    }
                  }while(prox == 1);
                  if(prox == 2)
                  {
                    num_content = +/[0-9]+/.exec(item.id);
                    num_aba = 2 * num_content - 2;
                    LTb0('0', num_aba);
                  }
              ";
            }

            $retorno .=  "  document.getElementById(\"{$nome}\").focus(); \n";
            $retorno .=  "  return false;\n";
            $retorno .=  "}\n";

            if (!empty($nomeCampo)) {
              if ($nomeCampo == 'cpf') {
                $retorno .= " else { if(! DvCpfOk( document.getElementById('$nome')) ) return false; }";
              }
            }

            if (!empty($nomeCampo)) {
              if ($nomeCampo == 'cnpj' || $nomeCampo == 'cnpj_pesq') {
                $retorno .= " else { if(document.$this->__nome.$nome.value != ''){ if(! DvCnpjOk( document.$this->__nome.$nome) ) return false; }}";
              }
            }
          }
        }
      }
    }
    // Fim while

    if ($this->acao_executa_submit) {
      $retorno .= "
      if (document.getElementById('btn_enviar')) {
        document.getElementById('btn_enviar').disabled = true;
        document.getElementById('btn_enviar').value = 'Aguarde...';
        document.getElementById('btn_enviar').className = 'botaolistagemdisabled';
      }
      ";

      $retorno .=  "\ndocument.$this->__nome.submit(); ";
    }
    else {
      $retorno .= " \n return true; \n";
    }

    $retorno .= "\n}\n";
    $retorno .=  "</script>\n";

    if ($this->acao_enviar && $this->botao_enviar) {
      $retorno .=  "&nbsp;<input type='button' id='btn_enviar' class='botaolistagem' onclick='{$this->acao_enviar};' value='{$this->nome_url_sucesso}'>&nbsp;";
    }

    if ($this->fexcluir) {
      $retorno .=  "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:excluir();' value=' Excluir '>&nbsp;";
    }
    if ($this->bot_alt) {
      $retorno .=  "&nbsp;<input type='button' class='botaolistagem' onclick='javascript: go( \"$this->url_alt\" );' value=' $this->nome_url_alt '>&nbsp;";
    }
    if ($this->excluir_Img) {
      $retorno .=  "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:ExcluirImg();' value=' $this->nome_excluirImg '>&nbsp;";
    }
    if ($this->acao) {
      $retorno .=  "&nbsp;<input type='button' class='botaolistagem' onclick='javascript: $this->acao' value=' $this->nome_acao '>&nbsp;";
    }
    if ($this->url_cancelar || $this->script_cancelar) {
      $retorno .=  "&nbsp;<input type='button' class='botaolistagem' onclick='javascript: $this->script_cancelar go( \"$this->url_cancelar\" );' value=' $this->nome_url_cancelar '>&nbsp;";
    }

    if ($this->array_botao_url) {
      for ($i = 0; $i < count($this->array_botao); $i++) {
        if($this->array_botao_id[$i]) {
          $retorno .=  "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"".$this->array_botao_url[$i]."\" );' value='".$this->array_botao[$i]."' id=\"{$this->array_botao_id[$i]}\">&nbsp;";
        }
        else {
          $retorno .=  "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:go( \"".$this->array_botao_url[$i]."\" );' value='".$this->array_botao[$i]."' id=\"arr_bot_{$this->array_botao[$i]}\">&nbsp;";
        }
      }
    }
    elseif ($this->array_botao_url_script) {
      for ($i = 0; $i < count($this->array_botao); $i++) {
        if ($this->array_botao_id[$i]) {
          $id = $this->array_botao_id[$i];
          $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick=\"".$this->array_botao_url_script[$i]."\" value=\"".$this->array_botao[$i]."\" id=\"{$id}\">&nbsp;\n";
        }
        else {
          $id = $this->array_botao[$i];
          $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick=\"".$this->array_botao_url_script[$i]."\" value=\"".$this->array_botao[$i]."\" id=\"arr_bot_{$id}\">&nbsp;\n";
        }
      }
    }

    $retorno .=  "</td>\n</tr>\n";
    $retorno .=  "</table>\n</center>\n<!-- cadastro end -->\n";
    $retorno .=  "</form>\n";

    if ($this->bannerClose) {
      $retorno .= "</td></tr></table>";
    }

    if ($this->executa_script) {
      $retorno .= "<script type=\"text/javascript\">{$this->executa_script}</script>";
    }

    Portabilis_View_Helper_Application::embedJavascriptToFixupFieldsWidth($this);

    return $retorno;
  }

  function isNullNow()
  {
    $args = func_get_args();

    foreach ($args as $ind => $arg) {
      if (empty($arg)) {
        $args[$ind] = 'NULL';
      }
    }

    return ($args);
  }

  function isOnNow()
  {
    $args = func_get_args();

    foreach ($args as $ind => $arg) {
      $args[$ind] = $arg == 'on' ? 1 : 0;
    }

    return ($args);
  }

  /**
   * Retorna uma lista formatada de erros que possam ter sido lançadas pela
   * integração CoreExt_Controller_Page_Interface com CoreExt_DataMapper e
   * CoreExt_Entity.
   *
   * @return string|NULL
   */
  protected function _getControllerErrors()
  {
    try {
      $hasErrors = $this->hasErrors();
    }
    catch (Core_Controller_Page_Exception $e) {
      return NULL;
    }

    // Verifica se houve erros
    if ($hasErrors) {
      $htmlError = '
        <div class="form error">
          <p>Por favor, verifique a lista de erros e corrija as informações necessárias no formulário.</p>
          <ul>%s</ul>
        </div>
        ';

      $errors = '';
      foreach ($this->getErrors() as $key => $error) {
        if (!is_null($error)) {
          $errors .= sprintf('<li>%s: %s</li>%s', $this->_getEntityLabel($key), $error, PHP_EOL);
        }
      }

      return sprintf($htmlError, $errors);
    }
    return NULL;
  }


  protected function inputsHelper() {
    if (! isset($this->_inputsHelper))
      $this->_inputsHelper = new Portabilis_View_Helper_Inputs($this);

    return $this->_inputsHelper;
  }

  protected function currentUserId() {
    return Portabilis_Utils_User::currentUserId();
  }
}
