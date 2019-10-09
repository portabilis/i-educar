<?php


require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Benef&iacute;cio Aluno" );
        $this->processoAp = "581";
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

    var $cod_aluno_beneficio;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_beneficio;
    var $desc_beneficio;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Aluno Beneficio - Detalhe";


        $this->cod_aluno_beneficio=$_GET["cod_aluno_beneficio"];

        $tmp_obj = new clsPmieducarAlunoBeneficio( $this->cod_aluno_beneficio );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_aluno_beneficio_lst.php');
        }

        if( $registro["cod_aluno_beneficio"] )
        {
            $this->addDetalhe( array( "C&oacute;digo Benef&iacute;cio", "{$registro["cod_aluno_beneficio"]}") );
        }
        if( $registro["nm_beneficio"] )
        {
            $this->addDetalhe( array( "Benef&iacute;cio", "{$registro["nm_beneficio"]}") );
        }
        if( $registro["desc_beneficio"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", nl2br("{$registro["desc_beneficio"]}")) );
        }

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(581, $this->pessoa_logada,3))
        {
            $this->url_novo = "educar_aluno_beneficio_cad.php";
            $this->url_editar = "educar_aluno_beneficio_cad.php?cod_aluno_beneficio={$registro["cod_aluno_beneficio"]}";
        }
        //**
        $this->url_cancelar = "educar_aluno_beneficio_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do benefício de alunos', [
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
