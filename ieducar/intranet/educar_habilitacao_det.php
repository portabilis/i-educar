<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Habilita&ccedil;&atilde;o" );
        $this->processoAp = "573";
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

    var $cod_habilitacao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Habilitacao - Detalhe";


        $this->cod_habilitacao=$_GET["cod_habilitacao"];

        $tmp_obj = new clsPmieducarHabilitacao( $this->cod_habilitacao );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_habilitacao_lst.php');
        }
        if( $registro["ref_cod_instituicao"] )
        {
            $obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
            $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
            $registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];

            $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
        }
        if( $registro["nm_tipo"] )
        {
            $this->addDetalhe( array( "Habilita&ccedil;&atilde;o", "{$registro["nm_tipo"]}") );
        }
        if( $registro["descricao"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
        }

        $obj_permissao = new clsPermissoes();
        if( $obj_permissao->permissao_cadastra( 573, $this->pessoa_logada,3 ) ) {
            $this->url_novo = "educar_habilitacao_cad.php";
            $this->url_editar = "educar_habilitacao_cad.php?cod_habilitacao={$registro["cod_habilitacao"]}";
        }
        $this->url_cancelar = "educar_habilitacao_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe da habilitação', [
            url('intranet/educar_index.php') => 'Escola',
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
