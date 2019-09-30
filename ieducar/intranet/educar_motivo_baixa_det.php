<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Motivo Baixa" );
        $this->processoAp = "600";
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

    var $cod_motivo_baixa;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_motivo_baixa;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Motivo Baixa - Detalhe";


        $this->cod_motivo_baixa=$_GET["cod_motivo_baixa"];

        $tmp_obj = new clsPmieducarMotivoBaixa( $this->cod_motivo_baixa );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_motivo_baixa_lst.php');
        }

        if( $registro["nm_motivo_baixa"] )
        {
            $this->addDetalhe( array( "Motivo Baixa", "{$registro["nm_motivo_baixa"]}") );
        }
        if( $registro["descricao"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 600, $this->pessoa_logada, 11 ) )
        {
            $this->url_novo = "educar_motivo_baixa_cad.php";
            $this->url_editar = "educar_motivo_baixa_cad.php?cod_motivo_baixa={$registro["cod_motivo_baixa"]}";
        }

        $this->url_cancelar = "educar_motivo_baixa_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do motivo de baixa', [
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
