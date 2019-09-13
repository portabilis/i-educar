<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Ensino" );
        $this->processoAp = "558";
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

    var $cod_tipo_ensino;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";


        //** Verificacao de permissao para exclusao
        $obj_permissao = new clsPermissoes();

        $obj_permissao->permissao_cadastra(558, $this->pessoa_logada,7,"educar_tipo_ensino_lst.php");
        //**

        $this->cod_tipo_ensino=$_GET["cod_tipo_ensino"];

        if( is_numeric( $this->cod_tipo_ensino ) )
        {

            $obj = new clsPmieducarTipoEnsino($this->cod_tipo_ensino,null,null,null,null,null,1);
            if(!$registro = $obj->detalhe()){
                $this->simpleRedirect('educar_tipo_ensino_lst.php');
            }

            if(!$registro["ativo"] )
                $this->simpleRedirect('educar_tipo_ensino_lst.php');

            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissao->permissao_excluir(558,$this->pessoa_logada,7);
                //**

                $retorno = "Editar";
            }
        }
//      $this->url_cancelar = ($retorno == "Editar") ? "educar_tipo_ensino_det.php?cod_tipo_ensino={$registro["cod_tipo_ensino"]}" : "educar_tipo_ensino_lst.php";
        $this->nome_url_cancelar = "Cancelar";
        $this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_tipo_ensino", $this->cod_tipo_ensino );
        if ($_GET['precisa_lista'])
        {
            // foreign keys
            $get_escola = false;
            $obrigatorio = true;
            include("include/pmieducar/educar_campo_lista.php");
        }// text
        else
        {
            $this->campoOculto("ref_cod_instituicao", $this->ref_cod_instituicao);
        }
        $this->campoTexto( "nm_tipo", "Tipo de Ensino", $this->nm_tipo, 30, 255, true );

        // data

    }

    function Novo()
    {


        $obj = new clsPmieducarTipoEnsino( $this->cod_tipo_ensino, null, $this->pessoa_logada, $this->nm_tipo, null, null, 1, $this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            echo "<script>
                        if (parent.document.getElementById('ref_cod_tipo_ensino').disabled)
                            parent.document.getElementById('ref_cod_tipo_ensino').options[0] = new Option('Selecione um tipo de ensino', '', false, false);
                        parent.document.getElementById('ref_cod_tipo_ensino').options[parent.document.getElementById('ref_cod_tipo_ensino').options.length] = new Option('$this->nm_tipo', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_tipo_ensino').value = '$cadastrou';
                        parent.document.getElementById('ref_cod_tipo_ensino').disabled = false;
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();
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

<script>

<?php
if (!$_GET['precisa_lista'])
{?>

    Event.observe(window, 'load', Init);

    function Init()
    {
        $('ref_cod_instituicao').value = parent.document.getElementById('ref_cod_instituicao').value;
    }

<?php } ?>

</script>
