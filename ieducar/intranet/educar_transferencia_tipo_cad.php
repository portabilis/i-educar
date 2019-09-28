<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once ("include/modules/clsModulesAuditoriaGeral.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Motivo Transfer&ecirc;ncia" );
        $this->processoAp = "575";
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    var $cod_transferencia_tipo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $desc_tipo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_transferencia_tipo=$_GET["cod_transferencia_tipo"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 575, $this->pessoa_logada, 7, "educar_transferencia_tipo_lst.php" );

        if( is_numeric( $this->cod_transferencia_tipo ) )
        {
            $obj = new clsPmieducarTransferenciaTipo();
            $lst  = $obj->lista( $this->cod_transferencia_tipo );
            $registro  = array_shift($lst);
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->fexcluir = $obj_permissoes->permissao_excluir( 575, $this->pessoa_logada,7 );
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_transferencia_tipo_det.php?cod_transferencia_tipo={$registro["cod_transferencia_tipo"]}" : "educar_transferencia_tipo_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' tipo de transferência', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_transferencia_tipo", $this->cod_transferencia_tipo );

        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoTexto( "nm_tipo", "Motivo Transfer&ecirc;ncia", $this->nm_tipo, 30, 255, true );
        $this->campoMemo( "desc_tipo", "Descri&ccedil;&atilde;o", $this->desc_tipo, 60, 5, false );
    }

    function Novo()
    {


        $obj = new clsPmieducarTransferenciaTipo( null,null,$this->pessoa_logada,$this->nm_tipo,$this->desc_tipo,null,null,1,$this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $transferenciaTipo = new clsPmieducarTransferenciaTipo($cadastrou);
            $transferenciaTipo = $transferenciaTipo->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("transferencia_tipo", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($transferenciaTipo);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_transferencia_tipo_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $transferenciaTipoDetalhe = new clsPmieducarTransferenciaTipo($this->cod_transferencia_tipo);
        $transferenciaTipoDetalheAntes = $transferenciaTipoDetalhe->detalhe();

        $obj = new clsPmieducarTransferenciaTipo( $this->cod_transferencia_tipo,$this->pessoa_logada,null,$this->nm_tipo,$this->desc_tipo,null,null,1,$this->ref_cod_instituicao );
        $editou = $obj->edita();
        if( $editou )
        {
            $transferenciaTipoDetalheDepois = $transferenciaTipoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("transferencia_tipo", $this->pessoa_logada, $this->cod_transferencia_tipo);
            $auditoria->alteracao($transferenciaTipoDetalheAntes, $transferenciaTipoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_transferencia_tipo_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarTransferenciaTipo( $this->cod_transferencia_tipo, $this->pessoa_logada, null, null, null, null, null, 0);
        $transferenciaTipo = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("transferencia_tipo", $this->pessoa_logada, $this->cod_transferencia_tipo);
            $auditoria->exclusao($transferenciaTipo);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_transferencia_tipo_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";

        return false;
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
