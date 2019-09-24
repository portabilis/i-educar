<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Motivo Suspens&atilde;o" );
        $this->processoAp = "607";
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

    var $cod_motivo_suspensao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_motivo;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Motivo Suspens&atilde;o - Detalhe";


        $this->cod_motivo_suspensao=$_GET["cod_motivo_suspensao"];

        $tmp_obj = new clsPmieducarMotivoSuspensao( $this->cod_motivo_suspensao );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_motivo_suspensao_lst.php');
        }

        if( $registro["nm_motivo"] )
        {
            $this->addDetalhe( array( "Motivo Suspens&atilde;o", "{$registro["nm_motivo"]}") );
        }
        if( $registro["descricao"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 607, $this->pessoa_logada, 11 ) )
        {
        $this->url_novo = "educar_motivo_suspensao_cad.php";
        $this->url_editar = "educar_motivo_suspensao_cad.php?cod_motivo_suspensao={$registro["cod_motivo_suspensao"]}";
        }

        $this->url_cancelar = "educar_motivo_suspensao_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do motivo de suspensão', [
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
