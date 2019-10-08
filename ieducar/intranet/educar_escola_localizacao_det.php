<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Escola Localiza&ccedil;&atilde;o" );
        $this->processoAp = "562";
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

    var $cod_escola_localizacao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_localizacao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Escola Localiza&ccedil;&atilde;o - Detalhe";


        $this->cod_escola_localizacao=$_GET["cod_escola_localizacao"];

        $tmp_obj = new clsPmieducarEscolaLocalizacao( $this->cod_escola_localizacao );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_escola_localizacao_lst.php');
        }

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
        {
            if( $registro["ref_cod_instituicao"] )
            {
                $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
            }
        }
        if( $registro["nm_localizacao"] )
        {
            $this->addDetalhe( array( "Localiza&ccedil;&atilde;o", "{$registro["nm_localizacao"]}") );
        }

        if( $obj_permissoes->permissao_cadastra( 562, $this->pessoa_logada, 3 ) )
        {
            $this->url_novo = "educar_escola_localizacao_cad.php";
            $this->url_editar = "educar_escola_localizacao_cad.php?cod_escola_localizacao={$registro["cod_escola_localizacao"]}";
        }
        $this->url_cancelar = "educar_escola_localizacao_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe da localização', [
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
