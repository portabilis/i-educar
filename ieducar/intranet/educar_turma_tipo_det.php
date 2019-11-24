<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Turma Tipo" );
        $this->processoAp = "570";
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

    var $cod_turma_tipo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $sgl_tipo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;
    var $ref_cod_escola;

    function Gerar()
    {
        $this->titulo = "Turma Tipo - Detalhe";


        $this->cod_turma_tipo=$_GET["cod_turma_tipo"];

        $tmp_obj = new clsPmieducarTurmaTipo( $this->cod_turma_tipo );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_turma_tipo_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro["ref_cod_instituicao"] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
        {
            if( $registro["ref_cod_instituicao"] )
            {
                $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
            }
        }

        if( $registro["nm_tipo"] )
        {
            $this->addDetalhe( array( "Turma Tipo", "{$registro["nm_tipo"]}") );
        }
        if( $registro["sgl_tipo"] )
        {
            $this->addDetalhe( array( "Sigla", "{$registro["sgl_tipo"]}") );
        }

        $obj_permissao = new clsPermissoes();
        if( $obj_permissao->permissao_cadastra( 570, $this->pessoa_logada,7 ) ) {
            $this->url_novo = "educar_turma_tipo_cad.php";
            $this->url_editar = "educar_turma_tipo_cad.php?cod_turma_tipo={$registro["cod_turma_tipo"]}";
        }
        $this->url_cancelar = "educar_turma_tipo_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do tipo de turma', [
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
