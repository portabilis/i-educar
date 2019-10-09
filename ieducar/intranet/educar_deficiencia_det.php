<?php

/**
 * @author Adriano Erik Weiguert Nagasava
 */

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/Geral.inc.php" );
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Defici&ecirc;ncia" );
        $this->processoAp = "631";
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

    var $cod_deficiencia;
    var $nm_deficiencia;

    function Gerar()
    {
        $this->titulo = "Defici&ecirc;ncia - Detalhe";


        $this->cod_deficiencia=$_GET["cod_deficiencia"];

        $tmp_obj = new clsCadastroDeficiencia( $this->cod_deficiencia );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_deficiencia_lst.php');
        }

        if( $registro["nm_deficiencia"] )
        {
            $this->addDetalhe( array( "Deficiência", "{$registro["nm_deficiencia"]}") );
        }
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 631, $this->pessoa_logada, 7 ) )
        {
            $this->url_novo = "educar_deficiencia_cad.php";
            $this->url_editar = "educar_deficiencia_cad.php?cod_deficiencia={$registro["cod_deficiencia"]}";
        }
        $this->url_cancelar = "educar_deficiencia_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe da deficiência', [
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
