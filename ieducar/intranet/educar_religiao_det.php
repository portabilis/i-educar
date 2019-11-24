<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Religiao" );
        $this->processoAp = "579";
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

    var $cod_religiao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_religiao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Religiao - Detalhe";


        $this->cod_religiao=$_GET["cod_religiao"];

        $tmp_obj = new clsPmieducarReligiao( $this->cod_religiao );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_religiao_lst.php');
        }


        if( $registro["cod_religiao"] )
        {
            $this->addDetalhe( array( "Religi&atilde;o", "{$registro["cod_religiao"]}") );
        }
        if( $registro["nm_religiao"] )
        {
            $this->addDetalhe( array( "Nome Religi&atilde;o", "{$registro["nm_religiao"]}") );
        }

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(579, $this->pessoa_logada,3))
        {
            $this->url_novo = "educar_religiao_cad.php";
            $this->url_editar = "educar_religiao_cad.php?cod_religiao={$registro["cod_religiao"]}";
        }
        //**


        $this->url_cancelar = "educar_religiao_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe da religião', [
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
