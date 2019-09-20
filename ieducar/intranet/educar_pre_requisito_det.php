<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Pre Requisito" );
        $this->processoAp = "601";
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

    var $cod_pre_requisito;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $schema_;
    var $tabela;
    var $nome;
    var $sql;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Pre Requisito - Detalhe";


        $this->cod_pre_requisito=$_GET["cod_pre_requisito"];

        $tmp_obj = new clsPmieducarPreRequisito( $this->cod_pre_requisito );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_pre_requisito_lst.php');
        }

            $obj_ref_usuario_exc = new clsPmieducarUsuario( $registro["ref_usuario_exc"] );
            $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
            $registro["ref_usuario_exc"] = $det_ref_usuario_exc["data_cadastro"];

            $obj_ref_usuario_cad = new clsPmieducarUsuario( $registro["ref_usuario_cad"] );
            $det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
            $registro["ref_usuario_cad"] = $det_ref_usuario_cad["data_cadastro"];

        if( $registro["cod_pre_requisito"] )
        {
            $this->addDetalhe( array( "Pre Requisito", "{$registro["cod_pre_requisito"]}") );
        }
        if( $registro["schema_"] )
        {
            $this->addDetalhe( array( "Schema ", "{$registro["schema_"]}") );
        }
        if( $registro["tabela"] )
        {
            $this->addDetalhe( array( "Tabela", "{$registro["tabela"]}") );
        }
        if( $registro["nome"] )
        {
            $this->addDetalhe( array( "Nome", "{$registro["nome"]}") );
        }
        if( $registro["sql"] )
        {
            $this->addDetalhe( array( "Sql", "{$registro["sql"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 601, $this->pessoa_logada, 3, null, true ) )
        {
        $this->url_novo = "educar_pre_requisito_cad.php";
        $this->url_editar = "educar_pre_requisito_cad.php?cod_pre_requisito={$registro["cod_pre_requisito"]}";
        }

        $this->url_cancelar = "educar_pre_requisito_lst.php";
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
