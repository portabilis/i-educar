<?php


require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );


class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Acervo Assunto" );
        $this->processoAp = "592";
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

    var $cod_acervo_assunto;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_assunto;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Acervo Assunto - Detalhe";


        $this->cod_acervo_assunto=$_GET["cod_acervo_assunto"];

        $tmp_obj = new clsPmieducarAcervoAssunto( $this->cod_acervo_assunto );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_acervo_assunto_lst.php');
        }

        if( $registro["nm_assunto"] )
        {
            $this->addDetalhe( array( "Assunto", "{$registro["nm_assunto"]}") );
        }
        if( $registro["descricao"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 592, $this->pessoa_logada, 11 ) )
        {
            $this->url_novo = "educar_acervo_assunto_cad.php";
            $this->url_editar = "educar_acervo_assunto_cad.php?cod_acervo_assunto={$registro["cod_acervo_assunto"]}";
        }

        $this->url_cancelar = "educar_acervo_assunto_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Listagem de assuntos', [
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
