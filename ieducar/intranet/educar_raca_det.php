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
        $this->addEstilo("localizacaoSistema");
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

        /*if( class_exists( "clsCadastroFisica" ) )
        {
            $obj_idpes_exc = new clsCadastroFisica( $registro["idpes_exc"] );
            $det_idpes_exc = $obj_idpes_exc->detalhe();
            $registro["idpes_exc"] = $det_idpes_exc[""];
        }
        else
        {
            $registro["idpes_exc"] = "Erro na geracao";
            echo "<!--\nErro\nClasse nao existente: clsCadastroFisica\n-->";
        }
*/
        /*if( class_exists( "clsCadastroFisica" ) )
        {
            $obj_idpes_cad = new clsCadastroFisica( $registro["idpes_cad"] );
            $det_idpes_cad = $obj_idpes_cad->detalhe();
            $registro["idpes_cad"] = $det_idpes_cad[""];
        }
        else
        {
            $registro["idpes_cad"] = "Erro na geracao";
            echo "<!--\nErro\nClasse nao existente: clsCadastroFisica\n-->";
        }*/


        /*if( $registro["cod_raca"] )
        {
            $this->addDetalhe( array( "Raca", "{$registro["cod_raca"]}") );
        }
        if( $registro["idpes_exc"] )
        {
            $this->addDetalhe( array( "Idpes Exc", "{$registro["idpes_exc"]}") );
        }
        if( $registro["idpes_cad"] )
        {
            $this->addDetalhe( array( "Idpes Cad", "{$registro["idpes_cad"]}") );
        }*/
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
