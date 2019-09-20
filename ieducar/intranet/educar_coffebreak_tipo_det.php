<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Coffebreak Tipo" );
        $this->processoAp = "564";
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

    var $cod_coffebreak_tipo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $desc_tipo;
    var $custo_unitario;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Coffebreak Tipo - Detalhe";


        $this->cod_coffebreak_tipo=$_GET["cod_coffebreak_tipo"];

        $tmp_obj = new clsPmieducarCoffebreakTipo( $this->cod_coffebreak_tipo );
        $registro = $tmp_obj->detalhe();

        if( ! $registro || !$registro["ativo"] )
        {
            $this->simpleRedirect('educar_coffebreak_tipo_lst.php');
        }


        if( $registro["cod_coffebreak_tipo"] )
        {
            $this->addDetalhe( array( "Coffebreak Tipo", "{$registro["cod_coffebreak_tipo"]}") );
        }
        if( $registro["nm_tipo"] )
        {
            $this->addDetalhe( array( "Nome Tipo", "{$registro["nm_tipo"]}") );
        }
        if( $registro["desc_tipo"] )
        {
            $this->addDetalhe( array( "Desc Tipo", "{$registro["desc_tipo"]}") );
        }
        if( $registro["custo_unitario"] )
        {
            $this->addDetalhe( array( "Custo Unitario", str_replace(".",",",$registro["custo_unitario"])) );
        }

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(554, $this->pessoa_logada,7))
        {
            $this->url_novo = "educar_coffebreak_tipo_cad.php";
            $this->url_editar = "educar_coffebreak_tipo_cad.php?cod_coffebreak_tipo={$registro["cod_coffebreak_tipo"]}";
        }
        //**

        $this->url_cancelar = "educar_coffebreak_tipo_lst.php";
        $this->largura = "100%";
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
