<?php


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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Motivo Transfer&ecirc;ncia" );
        $this->processoAp = "575";
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

    var $cod_transferencia_tipo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $desc_tipo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Transferencia Tipo - Detalhe";


        $this->cod_transferencia_tipo=$_GET["cod_transferencia_tipo"];

        $tmp_obj = new clsPmieducarTransferenciaTipo( $this->cod_transferencia_tipo );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            throw new HttpResponseException(
                new RedirectResponse('educar_transferencia_tipo_lst.php')
            );
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
            $this->addDetalhe( array( "Motivo Transfer&ecirc;ncia", "{$registro["nm_tipo"]}") );
        }
        if( $registro["desc_tipo"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["desc_tipo"]}") );
        }

        if( $obj_permissoes->permissao_cadastra( 575, $this->pessoa_logada, 7 ) )
        {
            $this->url_novo = "educar_transferencia_tipo_cad.php";
            $this->url_editar = "educar_transferencia_tipo_cad.php?cod_transferencia_tipo={$registro["cod_transferencia_tipo"]}";
        }
        $this->url_cancelar = "educar_transferencia_tipo_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do tipo de transferência', [
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
