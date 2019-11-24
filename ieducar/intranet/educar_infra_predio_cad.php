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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Infra Predio" );
        $this->processoAp = "567";
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

    var $cod_infra_predio;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_cod_escola;
    var $nm_predio;
    var $desc_predio;
    var $endereco;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_infra_predio=$_GET["cod_infra_predio"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 567, $this->pessoa_logada,7, "educar_infra_predio_lst.php" );

        if( is_numeric( $this->cod_infra_predio ) )
        {

            $obj = new clsPmieducarInfraPredio( $this->cod_infra_predio );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;


                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(567,$this->pessoa_logada,7);
                //**
                $retorno = "Editar";
            }
            else
            {
                $this->simpleRedirect('educar_infra_predio_lst.php');
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}" : "educar_infra_predio_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' prédio', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {

        // primary keys
        $this->campoOculto( "cod_infra_predio", $this->cod_infra_predio );

        $this->inputsHelper()->dynamic(array('instituicao', 'escola'));

        // text
        $this->campoTexto( "nm_predio", "Nome Prédio", $this->nm_predio, 30, 255, true );
        $this->campoMemo( "desc_predio", "Descrição Prédio", $this->desc_predio, 60, 10, false );
        $this->campoMemo( "endereco", "Endereço", $this->endereco, 60, 2, true );
    }

    function Novo()
    {


        $obj = new clsPmieducarInfraPredio( $this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, null, null, 1 );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $infraPredio = new clsPmieducarInfraPredio($cadastrou);
            $infraPredio = $infraPredio->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("infra_predio", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($infraPredio);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_infra_predio_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $infraPredioDetalhe = new clsPmieducarInfraPredio($this->cod_infra_predio);
        $infraPredioDetalheAntes = $infraPredioDetalhe->detalhe();

        $obj = new clsPmieducarInfraPredio($this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, null,null, 1);
        $editou = $obj->edita();
        if( $editou )
        {
            $infraPredioDetalheDepois = $infraPredioDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("infra_predio", $this->pessoa_logada, $this->cod_infra_predio);
            $auditoria->alteracao($infraPredioDetalheAntes, $infraPredioDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_infra_predio_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarInfraPredio($this->cod_infra_predio, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_escola, $this->nm_predio, $this->desc_predio, $this->endereco, $this->data_cadastro, $this->data_exclusao, 0);
        $infraPredio = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("infra_predio", $this->pessoa_logada, $this->cod_infra_predio);
            $auditoria->exclusao($infraPredio);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_infra_predio_lst.php');
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
