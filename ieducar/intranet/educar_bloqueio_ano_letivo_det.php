<?php


require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Bloqueio do ano letivo" );
        $this->processoAp = "21251";
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

    var $ref_cod_instituicao;
    var $ref_ano;
    var $data_inicio;
    var $data_fim;

    function Gerar()
    {
        $this->titulo = "Bloqueio do ano letivo - Detalhe";


        $this->ref_cod_instituicao=$_GET["ref_cod_instituicao"];
        $this->ref_ano=$_GET["ref_ano"];

        $tmp_obj = new clsPmieducarBloqueioAnoLetivo( $this->ref_cod_instituicao, $this->ref_ano );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_bloqueio_ano_letivo_lst.php');
        }

        if( $registro["instituicao"] )
        {
            $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["instituicao"]}") );
        }
        if( $registro["ref_ano"] )
        {
            $this->addDetalhe( array( "Ano", "{$registro["ref_ano"]}") );
        }
        if( $registro["data_inicio"] )
        {
            $this->addDetalhe( array( "Data inicial permitida", dataToBrasil($registro['data_inicio'])) );
        }
        if( $registro["data_fim"] )
        {
            $this->addDetalhe( array( "Data final permitida", dataToBrasil($registro['data_fim'])) );
        }

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(21251, $this->pessoa_logada,3))
        {
            $this->url_novo = "educar_bloqueio_ano_letivo_cad.php";
            $this->url_editar = "educar_bloqueio_ano_letivo_cad.php?ref_cod_instituicao={$registro["ref_cod_instituicao"]}&ref_ano={$registro["ref_ano"]}";
        }
        //**
        $this->url_cancelar = "educar_bloqueio_ano_letivo_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do bloqueio do ano letivo', [
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
