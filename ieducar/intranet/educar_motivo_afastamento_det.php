<?php

/**
 * @author Adriano Erik Weiguert Nagasava
 */

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Servidores - Motivo Afastamento" );
        $this->processoAp = "633";
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

    var $cod_motivo_afastamento;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_motivo;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    //var $ref_cod_escola;
    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Motivo Afastamento - Detalhe";


        $this->cod_motivo_afastamento=$_GET["cod_motivo_afastamento"];

        $tmp_obj = new clsPmieducarMotivoAfastamento( $this->cod_motivo_afastamento );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            throw new HttpResponseException(
                new RedirectResponse('educar_motivo_afastamento_lst.php')
            );
        }

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro['ref_cod_instituicao'] );
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
        {
            if( $det_ref_cod_instituicao["nm_instituicao"] )
            {
                $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$det_ref_cod_instituicao["nm_instituicao"]}") );
            }
        }
        if( $registro["nm_motivo"] )
        {
            $this->addDetalhe( array( "Motivo de Afastamento", "{$registro["nm_motivo"]}") );
        }
        if( $registro["descricao"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 633, $this->pessoa_logada, 7 ) )
        {
            $this->url_novo = "educar_motivo_afastamento_cad.php";
            $this->url_editar = "educar_motivo_afastamento_cad.php?cod_motivo_afastamento={$registro["cod_motivo_afastamento"]}";
        }

        $this->url_cancelar = "educar_motivo_afastamento_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do motivo de afastamento', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
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
