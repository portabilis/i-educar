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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Projeto" );
        $this->processoAp = "21250";
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

    var $cod_projeto;
    var $nome;
    var $observacao;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_projeto=$_GET["cod_projeto"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 21250, $this->pessoa_logada,3, "educar_projeto_lst.php" );

        if( is_numeric( $this->cod_projeto ) )
        {

            $obj = new clsPmieducarProjeto( $this->cod_projeto );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;


                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(21250,$this->pessoa_logada,3);
                //**

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_projeto_det.php?cod_projeto={$registro["cod_projeto"]}" : "educar_projeto_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' projeto', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_projeto", $this->cod_projeto );

        // foreign keys

        // text
        $this->campoTexto( "nome", "Nome do projeto", $this->nome, 50, 50, true );
        $this->campoMemo( "observacao", "Observa&ccedil;&atilde;o", $this->observacao, 52, 5, false );

        // data

    }

    function Novo()
    {


        $obj = new clsPmieducarProjeto( null, $this->nome, $this->observacao);
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $projeto = new clsPmieducarProjeto($cadastrou);
            $projeto = $projeto->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("projeto", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($projeto);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_projeto_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $projetoDetalhe = new clsPmieducarProjeto($this->cod_projeto);
        $projetoDetalheAntes = $projetoDetalhe->detalhe();

        $obj = new clsPmieducarProjeto($this->cod_projeto, $this->nome, $this->observacao);
        $editou = $obj->edita();
        if( $editou )
        {
            $projetoDetalheDepois = $projetoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("projeto", $this->pessoa_logada, $this->cod_projeto);
            $auditoria->alteracao($projetoDetalheAntes, $projetoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_projeto_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarProjeto($this->cod_projeto);
        $projeto = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("projeto", $this->pessoa_logada, $this->cod_projeto);
            $auditoria->exclusao($projeto);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_projeto_lst.php');
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
