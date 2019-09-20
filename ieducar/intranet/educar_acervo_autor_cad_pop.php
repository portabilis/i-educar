<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Autor" );
        $this->processoAp = "594";
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

    var $cod_acervo_autor;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_autor;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_biblioteca;
    var $ref_cod_escola;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";

        $this->cod_acervo_autor=$_GET["cod_acervo_autor"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 594, $this->pessoa_logada, 11,  "educar_acervo_autor_lst.php" );

        return $retorno;
    }

    function Gerar()
    {
        echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir')}</script>";
        // primary keys
        $this->campoOculto( "cod_acervo_autor", $this->cod_acervo_autor );


    /*  // foreign keys
        $get_escola     = 1;
        $get_biblioteca = 1;
        $obrigatorio    = true;
        include("include/pmieducar/educar_campo_lista.php");*/
        $this->campoOculto("ref_cod_biblioteca", $this->ref_cod_biblioteca);


        // text
        $this->campoTexto( "nm_autor", "Autor", $this->nm_autor, 30, 255, true );
        $this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );
        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);


    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 594, $this->pessoa_logada, 11,  "educar_acervo_autor_lst.php" );


        $obj = new clsPmieducarAcervoAutor( null, null, $this->pessoa_logada, $this->nm_autor, $this->descricao, null, null, 1,$this->ref_cod_biblioteca );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            echo "<script>
                    parent.document.getElementById('autor').value = '$cadastrou';
                    parent.document.getElementById('tipoacao').value = '';
                    parent.document.getElementById('formcadastro').submit();
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
    document.getElementById('ref_cod_biblioteca').value = parent.document.getElementById('ref_cod_biblioteca').value;
</script>
