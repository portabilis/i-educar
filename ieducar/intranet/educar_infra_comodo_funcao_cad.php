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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo de ambiente" );
        $this->processoAp = "572";
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

    var $cod_infra_comodo_funcao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_funcao;
    var $desc_funcao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_escola;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";



        $this->cod_infra_comodo_funcao=$_GET["cod_infra_comodo_funcao"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 572, $this->pessoa_logada,7, "educar_infra_comodo_funcao_lst.php" );

        if( is_numeric( $this->cod_infra_comodo_funcao ) )
        {

            $obj = new clsPmieducarInfraComodoFuncao();
            $lst  = $obj->lista( $this->cod_infra_comodo_funcao );
            if (is_array($lst))
            {
                $registro = array_shift($lst);
                if( $registro )
                {
                    foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                        $this->$campo = $val;

                    //** verificao de permissao para exclusao
                    $this->fexcluir = $obj_permissoes->permissao_excluir(572,$this->pessoa_logada,7);
                    //**

                    $retorno = "Editar";
                }else{
                    $this->simpleRedirect('educar_infra_comodo_funcao_lst.php');
                }
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_infra_comodo_funcao_det.php?cod_infra_comodo_funcao={$registro["cod_infra_comodo_funcao"]}" : "educar_infra_comodo_funcao_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' tipo de ambiente', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_infra_comodo_funcao", $this->cod_infra_comodo_funcao );

        $this->inputsHelper()->dynamic(array('instituicao', 'escola'));

        // text
        $this->campoTexto( "nm_funcao", "Tipo", $this->nm_funcao, 30, 255, true );
        $this->campoMemo( "desc_funcao", "Descri&ccedil;&atilde;o do tipo", $this->desc_funcao, 60, 5, false );

        // data

    }

    function Novo()
    {


        $obj = new clsPmieducarInfraComodoFuncao( null, null, $this->pessoa_logada, $this->nm_funcao, $this->desc_funcao, null, null, 1, $this->ref_cod_escola );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $infraComodoFuncao = new clsPmieducarInfraComodoFuncao($cadastrou);
            $infraComodoFuncao = $infraComodoFuncao->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("infra_comodo_funcao", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($infraComodoFuncao);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_infra_comodo_funcao_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $infraComodoFuncaoDetalhe = new clsPmieducarInfraComodoFuncao($this->cod_infra_comodo_funcao);
        $infraComodoFuncaoDetalheAntes = $infraComodoFuncaoDetalhe->detalhe();

        $obj = new clsPmieducarInfraComodoFuncao($this->cod_infra_comodo_funcao, $this->pessoa_logada, null, $this->nm_funcao, $this->desc_funcao, null, null, 1, $this->ref_cod_escola );
        $editou = $obj->edita();
        if( $editou )
        {
            $infraComodoFuncaoDetalheDepois = $infraComodoFuncaoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("infra_comodo_funcao", $this->pessoa_logada, $this->cod_infra_comodo_funcao);
            $auditoria->alteracao($infraComodoFuncaoDetalheAntes, $infraComodoFuncaoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_infra_comodo_funcao_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarInfraComodoFuncao($this->cod_infra_comodo_funcao, $this->pessoa_logada, null,null,null,null,null, 0);
        $infraComodoFuncao = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("infra_comodo_funcao", $this->pessoa_logada, $this->cod_infra_comodo_funcao);
            $auditoria->exclusao($infraComodoFuncao);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_infra_comodo_funcao_lst.php');
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
