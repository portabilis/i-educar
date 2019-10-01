<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Nivel Ensino" );
        $this->processoAp = "571";
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

    var $cod_nivel_ensino;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_nivel;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "N&iacute;vel Ensino - Detalhe";


        $this->cod_nivel_ensino=$_GET["cod_nivel_ensino"];

        $tmp_obj = new clsPmieducarNivelEnsino( $this->cod_nivel_ensino );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_nivel_ensino_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro["ref_cod_instituicao"] = $obj_instituicao_det["nm_instituicao"];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
        {
            if( $registro["ref_cod_instituicao"] )
            {
                $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
            }
        }
        if( $registro["nm_nivel"] )
        {
            $this->addDetalhe( array( "N&iacute;vel Ensino", "{$registro["nm_nivel"]}") );
        }
        if( $registro["descricao"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
        }

        if( $obj_permissoes->permissao_cadastra( 571, $this->pessoa_logada,3 ) )
        {
            $this->url_novo = "educar_nivel_ensino_cad.php";
            $this->url_editar = "educar_nivel_ensino_cad.php?cod_nivel_ensino={$registro["cod_nivel_ensino"]}";
        }
        $this->url_cancelar = "educar_nivel_ensino_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do nível de ensino', [
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
