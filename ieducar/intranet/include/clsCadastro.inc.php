<?php

use iEducar\Support\Navigation\Breadcrumb;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

require_once 'include/clsCampos.inc.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/View/Helper/Inputs.php';
require_once 'Portabilis/Utils/User.php';
require_once 'include/localizacaoSistema.php';

class clsCadastro extends clsCampos
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;
    public $__nome = 'formcadastro';
    public $target = '_self';
    public $largura;
    public $tipoacao;
    public $campos = [];
    public $erros;
    private $_mensagem;
    public $nome_pai;
    public $chave;
    public $item_campo_pai;
    public $fexcluir;
    public $excluir_Img;
    public $script_excluir = 'excluir();';
    public $nome_excluirImg;
    public $url_cancelar;
    public $nome_url_cancelar;
    public $url_sucesso;
    public $nome_url_sucesso;
    public $action;
    public $script_sucesso;
    public $script_cancelar;
    public $script;
    public $submete = false;
    public $acao_executa_submit = true;
    public $executa_submete = false;
    public $bot_alt = false;
    public $nome_url_alt;
    public $url_alt;
    public $help_images = false;
    public $array_botao;
    public $array_botao_url;
    public $array_botao_id;
    public $array_botao_url_script;
    public $controle;
    public $acao_enviar = 'acao()';
    public $botao_enviar = true;
    public $sucesso;
    public $onSubmit = 'acao()';
    public $form_enctype;

    /**
     * @deprecated
     */
    public function addBanner(
        $strBannerUrl = '',
        $strBannerLateralUrl = '',
        $strBannerTitulo = '',
        $boolFechaBanner = true
    ) {
        // Método deixado para compatibilidade
    }

    public function __construct()
    {
        parent::__construct();
        $this->tipoacao = @$_POST['tipoacao'];
    }

    public function enviaLocalizacao($localizao)
    {
        if ($localizao) {
            $this->locale = $localizao;
        }
    }

    public function PreCadastrar()
    {
    }

    public function Processar()
    {
        $this->excluir = @$_GET['excluir'];

        if ($this->excluir) {
            $this->tipoacao = 'Excluir';
        }
        if (empty($this->tipoacao)) {
            $this->tipoacao = $this->Inicializar();
            $this->Formular();
        } else {
            reset($_POST);
            foreach ($_POST as $variavel => $valor) {
                $this->$variavel = $valor;
            }

            reset($_FILES);
            foreach ($_FILES as $variavel => $valor) {
                $this->$variavel = $valor;
            }

            // Realiza cadastro
            $this->PreCadastrar();
            $this->sucesso = false;

            if ($this->tipoacao == 'Novo') {
                $this->sucesso = $this->Novo();
                if ($this->sucesso && !empty($this->script_sucesso)) {
                    $this->script = "<script type=\"text/javascript\">
              window.opener.AdicionaItem($this->chave, '$this->item_campo_pai', '$this->nome_pai', $this->submete );
              window.close();
            </script>";
                }

                if (!$this->sucesso && empty($this->erros) && empty($this->_mensagem)) {
                    $this->_mensagem = 'N&atilde;o foi poss&iacute;vel inserir a informa&ccedil;&atilde;o. [CAD01]';
                }
            } elseif ($this->tipoacao == 'Editar') {
                $this->sucesso = $this->Editar();
                if (!$this->sucesso && empty($this->erros) && empty($this->_mensagem)) {
                    $this->_mensagem = 'N&atilde;o foi poss&iacute;vel editar a informa&ccedil;&atilde;o. [CAD02]';
                }
            } elseif ($this->tipoacao == 'Excluir') {
                $this->sucesso = $this->Excluir();
                if (!$this->sucesso && empty($this->erros) && empty($this->_mensagem)) {
                    $this->_mensagem = 'N&atilde;o foi poss&iacute;vel excluir a informa&ccedil;&atilde;o. [CAD03]';
                }
            } elseif ($this->tipoacao == 'ExcluirImg') {
                $this->sucesso = $this->ExcluirImg();
                if (!$this->sucesso && empty($this->erros) && empty($this->_mensagem)) {
                    $this->_mensagem = 'N&atilde;o foi poss&iacute;vel excluir a informa&ccedil;&atilde;o. [CAD04]';
                }
            } elseif ($this->tipoacao == 'Enturmar') {
                $this->sucesso = $this->Enturmar();
                if (!$this->sucesso && empty($this->erros) && empty($this->_mensagem)) {
                    $this->_mensagem = 'N&atilde;o foi poss&iacute;vel copiar as entruma&ccedil;&otilde;es. [CAD05]';
                }
            }

            $this->setFlashMessage();

            if (empty($script) && $this->sucesso && !empty($this->url_sucesso)) {
                redirecionar($this->url_sucesso);
            } else {
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

    protected function setFlashMessage()
    {
        Session::remove('legacy');

        $hasMessage = false;
        $flashKeys = ['success', 'error', 'notice', 'info', 'legacy'];

        foreach ($flashKeys as $k) {
            if (Session::has($k)) {
                $hasMessage = true;
                break;
            }
        }

        if ($hasMessage) {
            return;
        }

        if (empty($this->_mensagem)) {
            if ($_GET['mensagem'] ?? '' === 'sucesso') {
                Session::now('success', 'Registro incluido com sucesso!');
            }
        } else {
            if ($this->sucesso) {
                Session::now('success', $this->_mensagem);
            } else {
                Session::now('error', $this->_mensagem);
            }
        }
    }

    public function RenderHTML()
    {
        ob_start();

        $this->_preRender();
        $this->Processar();
        $this->Gerar();

        $retorno = ob_get_contents();

        ob_end_clean();

        $this->nome_excluirImg = empty($this->nome_excluirImg) ? 'Excluir Imagem' : $this->nome_excluirImg;
        $this->nome_url_cancelar = empty($this->nome_url_cancelar) ? 'Cancelar' : $this->nome_url_cancelar;
        $this->nome_url_sucesso = empty($this->nome_url_sucesso) ? 'Salvar' : $this->nome_url_sucesso;

        $width = empty($this->largura) ? 'width=\'100%\'' : "width='$this->largura'";

        $retorno .= "\n<!-- cadastro begin -->\n";
        $retorno .= "<form name='$this->__nome' id='$this->__nome' onsubmit='return $this->onSubmit' action='$this->action'  method='post' target='$this->target' $this->form_enctype>\n";
        $retorno .= "<input name='tipoacao' id='tipoacao' type='hidden' value='$this->tipoacao'>\n";

        if ($this->campos) {
            reset($this->campos);

            foreach ($this->campos as $nome => $componente) {
                if ($componente[0] == 'oculto' || $componente[0] == 'rotulo') {
                    $retorno .= "<input name='$nome' id='$nome' type='hidden' value='" . urlencode($componente[3]) . "'>\n";
                }
            }
        }

        if ($this->locale) {
            app(Breadcrumb::class)->setLegacy($this->locale);
        }

        $retorno .= "<center>\n<table class='tablecadastro' $width border='0' cellpadding='2' cellspacing='0'>\n";
        $applicationTitle = $this->titulo_aplication ?? '';
        $titulo = isset($this->titulo) ? $this->titulo : "<b>{$this->tipoacao} {$applicationTitle}</b>";

        View::share('title', $this->getPageTitle());

        $barra = $titulo;

        $retorno .= "<tr><td class='formdktd' colspan='2' height='24'>{$barra}</td></tr>";

        if (empty($this->campos)) {
            $retorno .= '<tr><td class=\'linhaSim\' colspan=\'2\'><span class=\'form\'>N&atilde;o existe informa&ccedil;&atilde;o dispon&iacute;vel</span></td></tr>';
        } else {
            // Verifica se houve erros no controller
            $retorno .= $this->_getControllerErrors();
            $retorno .= $this->MakeCampos();
        }

        $retorno .=
            '<tr><td class=\'tableDetalheLinhaSeparador\' colspan=\'2\'></td></tr>
    <tr class=\'linhaBotoes\'><td colspan=\'2\' align=\'center\'>
    <script type="text/javascript">
    var goodIE = (document.all) ? 1:0;
    var netscape6 = (document.getElementById && !document.all) ? 1:0;
    var aux = \'\';
    var aberto = false;';

        $retorno .= $this->MakeFormat();

        $retorno .= 'function acao(){ ';

        unset($this->campos['desabilitado_tab']);
        unset($this->campos['cabecalho_tab']);

        reset($this->campos);

        foreach ($this->campos as $nome => $componente) {
            $nomeCampo = $componente[0];
            $validador = $componente[2];

            if ($nomeCampo === 'avulso') {
                continue;
            }

            if (empty($validador) && $nomeCampo == 'cpf' && preg_match('/^(tab_add_[0-9])/', $nome) !== 1) {
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

            if (preg_match('/^(tab_add_[0-9])/', $nome) === 1) {
                $nome_campos = $componente['cabecalho'];
                $componente = array_shift($componente);

                unset($componente['oculto']);
                reset($componente);

                $ct_campo = 0;
                $retorno .= "for(var id_campo=0;id_campo<$nome.getId();id_campo++)\n{\n";

                foreach ($componente as $name => $componente_campo) {
                    $nomeCampo = $componente_campo[1];
                    $validador = $componente_campo[2];

                    if (!empty($validador) && strlen($validador) > 1) {
                        if ($componente_campo[0] == 'idFederal') {
                            $campo = "document.getElementById(\"{$nomeCampo}[\"+id_campo+\"]\")";
                            $validador = explode('+', $validador);
                            $retorno .= ' if (';
                            $retorno .= "!({$validador[0]}.test( $campo.value ))) { \n";
                            $retorno .= "if( !({$validador[1]}.test( $campo.value ))) { ";
                            $retorno .= " alert( 'Preencha o campo \'{$nome_campos[$ct_campo]}\' corretamente!' ); \n  return false; }";
                            $retorno .= "else { if(! DvCnpjOk( $campo) ) return false; }  }";
                            $retorno .= "else{ if(! DvCpfOk( $campo) ) return false; }";
                        } elseif ($componente_campo[0] != 'oculto') {
                            $campo = "document.getElementById(\"{$nomeCampo}[\"+id_campo+\"]\")";
                            $fim_for = '';
                            if ($validador[0] == '*') {
                                $validador = substr($validador, 1);
                                $campo = 'campos';
                                $retorno .= " var campos = document.getElementById('{$nomeCampo}['+id_campo+']');\n
                              if(campos.value!='' &&
                          ";
                            } else {
                                $retorno .= " \n if (";
                            }

                            $retorno .= "($campo != null)&&!($validador.test( $campo.value )))\n";
                            $retorno .= "{\n";

                            $retorno .= " mudaClassName( 'formdestaque', 'obrigatorio' );\n";
                            $retorno .= " $campo.className = \"formdestaque\";\n";
                            $retorno .= " alert( 'Preencha o campo \'" . extendChars($nome_campos[$ct_campo], true) . "\' corretamente!' ); \n";
                            $retorno .= " $campo.focus(); \n";
                            $retorno .= " return false;\n";
                            $retorno .= "}\n{$fim_for}";
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
                    $retorno .= "if(!(/^-2[5-9]/.test( document.$this->__nome." . $nome . "_graus.value ))) { \n";
                    $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
                    $retorno .= " document.$this->__nome." . $nome . "_graus.focus(); \n";
                    $retorno .= ' return false; } ';

                    $retorno .= "if(!(/^([0-5])?[0-9]$/.test( document.$this->__nome." . $nome . "_min.value ))) { \n";
                    $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
                    $retorno .= " document.$this->__nome." . $nome . "_min.focus(); \n";
                    $retorno .= ' return false; } ';

                    $retorno .= "if(!(/^([0-5])?[0-9]$/.test( document.$this->__nome." . $nome . "_seg.value ))) { \n";
                    $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
                    $retorno .= " document.$this->__nome." . $nome . "_seg.focus(); \n";
                    $retorno .= ' return false; } ';
                } elseif ($validador == 'lon') {
                    $retorno .= "if(!(/^(-4[7-9])|(-5[0-4])/.test( document.$this->__nome." . $nome . "_graus.value ))) { \n";
                    $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
                    $retorno .= " document.$this->__nome." . $nome . "_graus.focus(); \n";
                    $retorno .= ' return false; } ';

                    $retorno .= "if(!(/^([0-5])?[0-9]$/.test( document.$this->__nome." . $nome . "_min.value ))) { \n";
                    $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
                    $retorno .= " document.$this->__nome." . $nome . "_min.focus(); \n";
                    $retorno .= ' return false; } ';

                    $retorno .= "if(!(/^([0-5])?[0-9]$/.test( document.$this->__nome." . $nome . "_seg.value ))) { \n";
                    $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n";
                    $retorno .= " document.$this->__nome." . $nome . "_seg.focus(); \n";
                    $retorno .= ' return false; } ';
                } else {
                    if ($nomeCampo == 'idFederal') {
                        $validador = explode('+', $validador);
                        $retorno .= ' if (';
                        $retorno .= "!({$validador[0]}.test( document.getElementById('$nome').value ))) { \n";
                        $retorno .= "if( !({$validador[1]}.test( document.getElementById('$nome').value ))) { ";
                        $retorno .= " alert( 'Preencha o campo \'$componente[1]\' corretamente!' ); \n  return false; }";
                        $retorno .= "else { if(! DvCnpjOk( document.getElementById('$nome')) ) return false; }  }";
                        $retorno .= "else{ if(! DvCpfOk( document.getElementById('$nome')) ) return false; }";
                    } else {
                        //substituito referencia a elementos por padrï¿½o W3C document.getElementById()
                        //quando se referenciava um nome de elemento como um array ex: cadastro[aluno]
                        //nao funcionava na referencia por nome
                        //16-08-2006
                        $retornoNaFalha = "  mudaClassName( 'formdestaque', 'obrigatorio' );\n";
                        $retornoNaFalha .= "  document.getElementById(\"{$nome}\").className = \"formdestaque\";\n";
                        $retornoNaFalha .= "  alert( 'Preencha o campo \'" . extendChars($componente[1], true) . "\' corretamente!' ); \n";

                        $retornoNaFalha .= "  document.getElementById(\"{$nome}\").focus(); \n";
                        $retornoNaFalha .= "  return false;\n";
                        if ($validador == '/[^ ]/') {
                            $retorno .= " if (typeof \$j == 'function' && \$j('#{$nome}').val() != null &&
                                \$j('#{$nome}').val().constructor === Array ) {\n
                    if (!\$j('#{$nome}').val().filter((val) => val.toString().trim().length > 0).length) {\n";
                            $retorno .= $retornoNaFalha;
                            $retorno .= "}\n";
                            $retorno .= '
                                } else if (
            ';
                        } else {
                            $retorno .= '  if (';
                        }

                        if ($validador[0] == '*') {
                            $validador = substr($validador, 1);
                            $retorno .= "document.getElementById(\"{$nome}\").value!='' && ";
                        }

                        $retorno .= "!($validador.test( document.getElementById(\"{$nome}\").value )))\n";
                        $retorno .= "{\n";
                        $retorno .= $retornoNaFalha;
                        $retorno .= "}\n";

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
            $retorno .= '
      if (document.getElementById(\'btn_enviar\')) {
        document.getElementById(\'btn_enviar\').disabled = true;
        document.getElementById(\'btn_enviar\').value = \'Aguarde...\';
        document.getElementById(\'btn_enviar\').className = \'botaolistagemdisabled\';
      }
      ';

            $retorno .= "\ndocument.$this->__nome.submit(); ";
        } else {
            $retorno .= " \n return true; \n";
        }

        $retorno .= "\n}\n";
        $retorno .= "</script>\n";

        if (!empty($this->acao_enviar) && !empty($this->botao_enviar)) {
            $retorno .= "&nbsp;<input type='button' id='btn_enviar' class='botaolistagem' onclick='{$this->acao_enviar};' value='{$this->nome_url_sucesso}'>&nbsp;";
        }

        if (!empty($this->fexcluir)) {
            $retorno .= "&nbsp;<input id='btn_excluir' type='button' class='botaolistagem' onclick='javascript:{$this->script_excluir}' value=' Excluir '>&nbsp;";
        }
        if (!empty($this->bot_alt)) {
            $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript: go( \"$this->url_alt\" );' value=' $this->nome_url_alt '>&nbsp;";
        }
        if (!empty($this->excluir_Img)) {
            $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript:ExcluirImg();' value=' $this->nome_excluirImg '>&nbsp;";
        }
        if (!empty($this->acao)) {
            $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript: $this->acao' value=' $this->nome_acao '>&nbsp;";
        }
        if (!empty($this->url_cancelar) || !empty($this->script_cancelar)) {
            $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript: $this->script_cancelar go( \"$this->url_cancelar\" );' value=' $this->nome_url_cancelar '>&nbsp;";
        }
        if (!empty($this->url_copiar_enturmacoes)) {
            $retorno .= "&nbsp;<input type='button' class='botaolistagem' onclick='javascript: go( \"$this->url_copiar_enturmacoes\" );' value=' $this->nome_url_copiar_enturmacoes '>&nbsp;";
        }

        if ($this->array_botao_url) {
            for ($i = 0; $i < count($this->array_botao); $i++) {
                if ($this->array_botao_id[$i]) {
                    $retorno .= '&nbsp;<input type=\'button\' class=\'botaolistagem\' onclick=\'javascript:go( "' . $this->array_botao_url[$i] . '" );\' value=\'' . $this->array_botao[$i] . "' id=\"{$this->array_botao_id[$i]}\">&nbsp;";
                } else {
                    $retorno .= '&nbsp;<input type=\'button\' class=\'botaolistagem\' onclick=\'javascript:go( "' . $this->array_botao_url[$i] . '" );\' value=\'' . $this->array_botao[$i] . "' id=\"arr_bot_{$this->array_botao[$i]}\">&nbsp;";
                }
            }
        } elseif ($this->array_botao_url_script) {
            for ($i = 0; $i < count($this->array_botao); $i++) {
                if ($this->array_botao_id[$i]) {
                    $id = $this->array_botao_id[$i];
                    $retorno .= '&nbsp;<input type=\'button\' class=\'botaolistagem\' onclick="' . $this->array_botao_url_script[$i] . '" value="' . $this->array_botao[$i] . "\" id=\"{$id}\">&nbsp;\n";
                } else {
                    $id = $this->array_botao[$i];
                    $retorno .= '&nbsp;<input type=\'button\' class=\'botaolistagem\' onclick="' . $this->array_botao_url_script[$i] . '" value="' . $this->array_botao[$i] . "\" id=\"arr_bot_{$id}\">&nbsp;\n";
                }
            }
        }

        $retorno .= "</td>\n</tr>\n";
        $retorno .= "</table>\n</center>\n<!-- cadastro end -->\n";
        $retorno .= "</form>\n";

        if (!empty($this->executa_script)) {
            $retorno .= "<script type=\"text/javascript\">{$this->executa_script}</script>";
        }

        Portabilis_View_Helper_Application::embedJavascriptToFixupFieldsWidth($this);

        return $retorno;
    }

    public function isNullNow()
    {
        $args = func_get_args();

        foreach ($args as $ind => $arg) {
            if (empty($arg)) {
                $args[$ind] = 'NULL';
            }
        }

        return ($args);
    }

    public function isOnNow()
    {
        $args = func_get_args();

        foreach ($args as $ind => $arg) {
            $args[$ind] = $arg == 'on' ? 1 : 0;
        }

        return ($args);
    }

    /**
     * Retorna uma lista formatada de erros que possam ter sido lanï¿½adas pela
     * integraï¿½ï¿½o CoreExt_Controller_Page_Interface com CoreExt_DataMapper e
     * CoreExt_Entity.
     *
     * @return string|NULL
     */
    protected function _getControllerErrors()
    {
        try {
            $hasErrors = $this->hasErrors();
        } catch (Core_Controller_Page_Exception $e) {
            return null;
        }

        // Verifica se houve erros
        if ($hasErrors) {
            $htmlError = '
        <div class="form error">
          <p>Por favor, verifique a lista de erros e corrija as informaï¿½ï¿½es necessï¿½rias no formulï¿½rio.</p>
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

        return null;
    }

    // TODO: Abstrair lógica em Trait ao atualizar PHP
    protected function validarCamposObrigatoriosCenso()
    {
        $obj = new clsPmieducarInstituicao($this->ref_cod_instituicao);
        $instituicao = empty($this->ref_cod_instituicao) ? $obj->primeiraAtiva() : $obj->detalhe();

        return dbBool($instituicao['obrigar_campos_censo']);
    }

    protected function sugestaoAnosLetivos()
    {
        $anoAtual = date('Y');
        $anos = range($anoAtual - 10, $anoAtual + 1);

        return array_combine($anos, $anos);
    }

    protected function inputsHelper()
    {
        if (!isset($this->_inputsHelper)) {
            $this->_inputsHelper = new Portabilis_View_Helper_Inputs($this);
        }

        return $this->_inputsHelper;
    }

    protected function currentUserId()
    {
        return Portabilis_Utils_User::currentUserId();
    }

    protected function nivelAcessoPessoaLogada()
    {
        $obj_permissoes = new clsPermissoes();

        return $obj_permissoes->nivel_acesso($this->currentUserId());
    }

    /**
     * @return string
     */
    private function getPageTitle()
    {
        if (isset($this->titulo)) {
            return $this->titulo;
        }

        if (isset($this->_titulo)) {
            return $this->_titulo;
        }

        return '';
    }

    public function __set($name, $value)
    {
        if ($name === 'mensagem') {
            $this->_mensagem = $value;

            Session::flash('legacy', $value);
        } else {
            $this->{$name} = $value;
        }
    }
}
