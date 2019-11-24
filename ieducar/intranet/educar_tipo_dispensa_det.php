<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Dispensa" );
        $this->processoAp = "577";
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

    var $cod_tipo_dispensa;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Tipo Dispensa - Detalhe";


        $this->cod_tipo_dispensa=$_GET["cod_tipo_dispensa"];

        $tmp_obj = new clsPmieducarTipoDispensa( $this->cod_tipo_dispensa );
        $registro = $tmp_obj->detalhe();
        if( ! $registro )
        {
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');
        }
            $obj_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
            $obj_instituicao_det = $obj_instituicao->detalhe();
            $registro["ref_cod_instituicao"] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
        {
            if( $registro["ref_cod_instituicao"] )
            {
                $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
            }
        }
        if( $registro["nm_tipo"] )
        {
            $this->addDetalhe( array( "Tipo Dispensa", "{$registro["nm_tipo"]}") );
        }
        if( $registro["descricao"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
        }

        if( $obj_permissoes->permissao_cadastra( 577, $this->pessoa_logada,7 ) )
        {
            $this->url_novo = "educar_tipo_dispensa_cad.php";
            $this->url_editar = "educar_tipo_dispensa_cad.php?cod_tipo_dispensa={$registro["cod_tipo_dispensa"]}";
        }
        $this->url_cancelar = "educar_tipo_dispensa_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do tipo de dispensa', [
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
