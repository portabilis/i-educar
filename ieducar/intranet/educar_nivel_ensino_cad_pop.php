<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - N&iacute;vel Ensino" );
        $this->processoAp = "571";
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

    var $cod_nivel_ensino;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_nivel;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_nivel_ensino=$_GET["cod_nivel_ensino"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 571, $this->pessoa_logada,3, "educar_nivel_ensino_lst.php" );

        if( is_numeric( $this->cod_nivel_ensino ) )
        {

            $obj = new clsPmieducarNivelEnsino( $this->cod_nivel_ensino );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->fexcluir = $obj_permissoes->permissao_excluir( 571, $this->pessoa_logada,3 );
                $retorno = "Editar";
            }
        }
//      $this->url_cancelar = ($retorno == "Editar") ? "educar_nivel_ensino_det.php?cod_nivel_ensino={$registro["cod_nivel_ensino"]}" : "educar_nivel_ensino_lst.php";
        $this->nome_url_cancelar = "Cancelar";
        $this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_nivel_ensino", $this->cod_nivel_ensino );

        // foreign keys
        if ($_GET['precisa_lista'])
        {
            $obrigatorio = true;
            include("include/pmieducar/educar_campo_lista.php");
        }
        else
        {
            $this->campoOculto("ref_cod_instituicao", $this->ref_cod_instituicao);
        }
        // text
        $this->campoTexto( "nm_nivel", "N&iacute;vel Ensino", $this->nm_nivel, 30, 255, true );
        $this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );
    }

    function Novo()
    {


        $obj = new clsPmieducarNivelEnsino( null, null, $this->pessoa_logada, $this->nm_nivel, $this->descricao,null,null,1,$this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            echo "<script>
                        if (parent.document.getElementById('ref_cod_nivel_ensino').disabled)
                            parent.document.getElementById('ref_cod_nivel_ensino').options[0] = new Option('Selecione um nível de ensino', '', false, false);
                        parent.document.getElementById('ref_cod_nivel_ensino').options[parent.document.getElementById('ref_cod_nivel_ensino').options.length] = new Option('$this->nm_nivel', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_nivel_ensino').value = '$cadastrou';
                        parent.document.getElementById('ref_cod_nivel_ensino').disabled = false;
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

<script>

<?php
if (!$_GET['ref_cod_instituicao'])
{
?>
    Event.observe(window, 'load', Init, false);

    function Init()
    {
        $('ref_cod_instituicao').value = parent.document.getElementById('ref_cod_instituicao').value;
    }

<?php
}
?>

</script>
