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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Dispensa" );
        $this->processoAp = "577";
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

    var $cod_tipo_dispensa;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_tipo_dispensa=$_GET["cod_tipo_dispensa"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 577, $this->pessoa_logada, 7, "educar_tipo_dispensa_lst.php" );

        if( is_numeric( $this->cod_tipo_dispensa ) )
        {

            $obj = new clsPmieducarTipoDispensa( $this->cod_tipo_dispensa );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->ref_cod_instituicao = $det_ref_cod_escola["ref_cod_instituicao"];

                $this->fexcluir = $obj_permissoes->permissao_excluir( 577, $this->pessoa_logada,7 );
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_tipo_dispensa_det.php?cod_tipo_dispensa={$registro["cod_tipo_dispensa"]}" : "educar_tipo_dispensa_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' tipo de dispensa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_tipo_dispensa", $this->cod_tipo_dispensa );

        // foreign keys
        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoTexto( "nm_tipo", "Tipo Dispensa", $this->nm_tipo, 30, 255, true );
        $this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );
    }

    function Novo()
    {


//      echo "null, null, {$this->pessoa_logada}, {$this->nm_tipo}, {$this->descricao}, null, null, 1, {$this->ref_cod_escola}, {$this->ref_cod_instituicao}<br>";
        $obj = new clsPmieducarTipoDispensa( null, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, null, null, 1, $this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $tipoDispensa = new clsPmieducarTipoDispensa($cadastrou);
            $tipoDispensa = $tipoDispensa->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("tipo_dispensa", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($tipoDispensa);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $tipoDispensaDetalhe = new clsPmieducarTipoDispensa($this->cod_tipo_dispensa);
        $tipoDispensaDetalheAntes = $tipoDispensaDetalhe->detalhe();

        $obj = new clsPmieducarTipoDispensa( $this->cod_tipo_dispensa, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, null, null, 1, $this->ref_cod_instituicao );
        $editou = $obj->edita();
        if( $editou )
        {
            $tipoDispensaDetalheDepois = $tipoDispensaDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("tipo_dispensa", $this->pessoa_logada, $this->cod_tipo_dispensa);
            $auditoria->alteracao($tipoDispensaDetalheAntes, $tipoDispensaDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarTipoDispensa( $this->cod_tipo_dispensa, $this->pessoa_logada, null, null, null, null, null, 0 );
        $tipoDispensa = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("tipo_dispensa", $this->pessoa_logada, $this->cod_tipo_dispensa);
            $auditoria->exclusao($tipoDispensa);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');
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
