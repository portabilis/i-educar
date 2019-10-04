<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Infra Predio" );
        $this->processoAp = "567";
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

    var $cod_infra_predio;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_cod_escola;
    var $nm_predio;
    var $desc_predio;
    var $endereco;
    var $data_cadastro;
    var $data_descricao;
    var $ativo;

    function Gerar()
    {
        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        $this->titulo = "Infra Predio - Detalhe";


        $this->cod_infra_predio=$_GET["cod_infra_predio"];

        $tmp_obj = new clsPmieducarInfraPredio( $this->cod_infra_predio );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_infra_predio_lst.php');
        }

        $obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $registro["ref_cod_escola"] = $det_ref_cod_escola["nm_escola"];

        if( $registro["cod_infra_predio"] )
        {
            $this->addDetalhe( array( "Infra Predio", "{$registro["cod_infra_predio"]}") );
        }
        if( $registro["ref_cod_escola"] )
        {
            $this->addDetalhe( array( "Escola", "{$registro["ref_cod_escola"]}") );
        }
        if( $registro["nm_predio"] )
        {
            $this->addDetalhe( array( "Nome Predio", "{$registro["nm_predio"]}") );
        }
        if( $registro["desc_predio"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o Pr&eacute;dio", "{$registro["desc_predio"]}") );
        }
        if( $registro["endereco"] )
        {
            $this->addDetalhe( array( "Endere&ccedil;o", "{$registro["endereco"]}") );
        }

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(567, $this->pessoa_logada,7))
        {
            $this->url_novo = "educar_infra_predio_cad.php";
            $this->url_editar = "educar_infra_predio_cad.php?cod_infra_predio={$registro["cod_infra_predio"]}";
        }
        //**

        $this->url_cancelar = "educar_infra_predio_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do prédio', [
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
