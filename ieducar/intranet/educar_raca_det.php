<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Ra&ccedil;a" );
        $this->processoAp = "678";
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

    var $cod_raca;
    var $idpes_exc;
    var $idpes_cad;
    var $nm_raca;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $pessoa_logada;

    function Gerar()
    {
        $this->titulo = "Ra&ccedil;a - Detalhe";


        $this->cod_raca=$_GET["cod_raca"];

        $tmp_obj = new clsCadastroRaca( $this->cod_raca );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_raca_lst.php');
        }

        if( $registro["nm_raca"] )
        {
            $this->addDetalhe( array( "Ra&ccedil;a", "{$registro["nm_raca"]}") );
        }

        $obj_permissao = new clsPermissoes();
        if( $obj_permissao->permissao_cadastra(678, $this->pessoa_logada, 7) )
        {
            $this->url_novo = "educar_raca_cad.php";
            $this->url_editar = "educar_raca_cad.php?cod_raca={$registro["cod_raca"]}";
        }

        $this->url_cancelar = "educar_raca_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe da raça', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
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
