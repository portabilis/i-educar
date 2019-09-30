<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Fonte" );
        $this->processoAp = "608";
    }
}

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    var $cod_fonte;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_fonte;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Fonte - Detalhe";


        $this->cod_fonte=$_GET["cod_fonte"];

        $tmp_obj = new clsPmieducarFonte( $this->cod_fonte );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_fonte_lst.php');
        }


        if( $registro["cod_fonte"] )
        {
            $this->addDetalhe( array( "Código Fonte", "{$registro["cod_fonte"]}") );
        }
        if( $registro["nm_fonte"] )
        {
            $this->addDetalhe( array( "Fonte", "{$registro["nm_fonte"]}") );
        }
        if( $registro["descricao"] )
        {
            $registro["descricao"] = nl2br($registro["descricao"]);
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 608, $this->pessoa_logada, 11 ) )
        {
        $this->url_novo = "educar_fonte_cad.php";
        $this->url_editar = "educar_fonte_cad.php?cod_fonte={$registro["cod_fonte"]}";
        }

        $this->url_cancelar = "educar_fonte_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe da fonte', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
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
