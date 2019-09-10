<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Operador" );
        $this->processoAp = "589";
        $this->renderBanner = false;
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    var $cod_operador;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nome;
    var $valor;
    var $fim_sentenca;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_operador=$_GET["cod_operador"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 589, $this->pessoa_logada, 0,  "educar_operador_lst.php", true );

        /*if( is_numeric( $this->cod_operador ) )
        {

            $obj = new clsPmieducarOperador( $this->cod_operador );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                $this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
                $this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

                $obj_permissoes = new clsPermissoes();
                if( $obj_permissoes->permissao_excluir( 589, $this->pessoa_logada, 0, null, true ) )
                {
                    $this->fexcluir = true;
                }

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_operador_det.php?cod_operador={$registro["cod_operador"]}" : "educar_operador_lst.php";*/
        $this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_operador", $this->cod_operador );

        // foreign keys

        // text
        $this->campoTexto( "nome", "Nome", $this->nome, 30, 255, true );
        $this->campoMemo( "valor", "Valor", $this->valor, 60, 10, true );
        $opcoes = array( "Não", "Sim" );
        $this->campoLista( "fim_sentenca", "Fim Sentenca", $opcoes, $this->fim_sentenca );

        // data

    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 589, $this->pessoa_logada, 0,  "educar_operador_lst.php", true );


        $obj = new clsPmieducarOperador( $this->cod_operador, $this->pessoa_logada, $this->pessoa_logada, $this->nome, $this->valor, $this->fim_sentenca, $this->data_cadastro, $this->data_exclusao, $this->ativo );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            echo "<script>
                        parent.document.getElementById('ref_cod_operador').options[parent.document.getElementById('ref_cod_operador').options.length] = new Option('$this->nome', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_operador').value = '$cadastrou';
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();
            return true;
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {
    }

    function Excluir()
    {
    }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
