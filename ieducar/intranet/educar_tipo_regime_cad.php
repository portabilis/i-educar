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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Regime" );
        $this->processoAp = "568";
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

    var $cod_tipo_regime;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";




        $this->cod_tipo_regime=$_GET["cod_tipo_regime"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 568, $this->pessoa_logada,3, "educar_tipo_regime_lst.php" );

        if( is_numeric( $this->cod_tipo_regime ) )
        {

            $obj = new clsPmieducarTipoRegime( $this->cod_tipo_regime );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;


                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(568,$this->pessoa_logada,3);
                //**
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_tipo_regime_det.php?cod_tipo_regime={$registro["cod_tipo_regime"]}" : "educar_tipo_regime_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' tipo de regime', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_tipo_regime", $this->cod_tipo_regime );

        // foreign keys
        // foreign keys
        $get_escola = false;
        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");
        // text
        $this->campoTexto( "nm_tipo", "Nome Tipo", $this->nm_tipo, 30, 255, true );

        // data

    }

    function Novo()
    {


        $obj = new clsPmieducarTipoRegime( $this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $tipoRegime = new clsPmieducarTipoRegime($cadastrou);
            $tipoRegime = $tipoRegime->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("tipo_regime", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($tipoRegime);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_regime_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $tipoRegimeDetalhe = new clsPmieducarTipoRegime($this->cod_tipo_regime);
        $tipoRegimeDetalheAntes = $tipoRegimeDetalhe->detalhe();

        $obj = new clsPmieducarTipoRegime($this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_instituicao);
        $editou = $obj->edita();
        if( $editou )
        {
            $tipoRegimeDetalheDepois = $tipoRegimeDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("tipo_regime", $this->pessoa_logada, $this->cod_tipo_regime);
            $auditoria->alteracao($tipoRegimeDetalheAntes, $tipoRegimeDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_regime_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarTipoRegime($this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, 0, $this->ref_cod_instituicao);
        $tipoRegime = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("tipo_regime", $this->pessoa_logada, $this->cod_tipo_regime);
            $auditoria->exclusao($tipoRegime);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_regime_lst.php');
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
