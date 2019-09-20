<?php


require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Idioma" );
        $this->processoAp = "590";
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

    var $cod_acervo_idioma;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_idioma;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Idioma - Detalhe";


        $this->cod_acervo_idioma=$_GET["cod_acervo_idioma"];

        $tmp_obj = new clsPmieducarAcervoIdioma( $this->cod_acervo_idioma );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_acervo_idioma_lst.php');
        }

        if( $registro["cod_acervo_idioma"] )
        {
            $this->addDetalhe( array( "C&oacute;digo Idioma", "{$registro["cod_acervo_idioma"]}") );
        }
        if( $registro["nm_idioma"] )
        {
            $this->addDetalhe( array( "Idioma", "{$registro["nm_idioma"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 590, $this->pessoa_logada, 11 ) )
        {
        $this->url_novo = "educar_acervo_idioma_cad.php";
        $this->url_editar = "educar_acervo_idioma_cad.php?cod_acervo_idioma={$registro["cod_acervo_idioma"]}";
        }

        $this->url_cancelar = "educar_acervo_idioma_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do idioma', [
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
