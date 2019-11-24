<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Escola Rede Ensino" );
        $this->processoAp = "647";
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

    var $cod_escola_rede_ensino;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_rede;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Escola Rede Ensino - Detalhe";


        $this->cod_escola_rede_ensino=$_GET["cod_escola_rede_ensino"];

        $tmp_obj = new clsPmieducarEscolaRedeEnsino( $this->cod_escola_rede_ensino );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_escola_rede_ensino_lst.php');
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
        if( $registro["nm_rede"] )
        {
            $this->addDetalhe( array( "Rede Ensino", "{$registro["nm_rede"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 647, $this->pessoa_logada, 3 ) )
        {
            $this->url_novo = "educar_escola_rede_ensino_cad.php";
            $this->url_editar = "educar_escola_rede_ensino_cad.php?cod_escola_rede_ensino={$registro["cod_escola_rede_ensino"]}";
        }

        $this->url_cancelar = "educar_escola_rede_ensino_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe da rede de ensino', [
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
