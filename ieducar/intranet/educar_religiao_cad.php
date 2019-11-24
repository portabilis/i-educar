<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once "include/modules/clsModulesAuditoriaGeral.inc.php";

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Religiao" );
        $this->processoAp = "579";
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

    var $cod_religiao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_religiao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_religiao=$_GET["cod_religiao"];

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        $obj_permissao->permissao_cadastra(579, $this->pessoa_logada,3,"educar_religiao_lst.php");
        //**

        if( is_numeric( $this->cod_religiao ) )
        {

            $obj = new clsPmieducarReligiao( $this->cod_religiao );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissao->permissao_excluir(579,$this->pessoa_logada,3);
                //**

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_religiao_det.php?cod_religiao={$registro["cod_religiao"]}" : "educar_religiao_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' religião', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_religiao", $this->cod_religiao );

        // foreign keys

        // text
        $this->campoTexto( "nm_religiao", "Religi&atilde;o", $this->nm_religiao, 30, 255, true );

        // data

    }

    function Novo()
    {


        $obj = new clsPmieducarReligiao( $this->cod_religiao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_religiao, $this->data_cadastro, $this->data_exclusao, $this->ativo );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $religiao = new clsPmieducarReligiao($cadastrou);
            $religiao = $religiao->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("religiao", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($religiao);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_religiao_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $religiaoDetalhe = new clsPmieducarReligiao($this->cod_religiao);
        $religiaoDetalheAntes = $religiaoDetalhe->detalhe();

        $obj = new clsPmieducarReligiao($this->cod_religiao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_religiao, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $editou = $obj->edita();
        if( $editou )
        {
            $religiaoDetalheDepois = $religiaoDetalhe->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("religiao", $this->pessoa_logada, $this->cod_religiao);
            $auditoria->alteracao($religiaoDetalheAntes, $religiaoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_religiao_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarReligiao($this->cod_religiao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_religiao, $this->data_cadastro, $this->data_exclusao, 0);
        $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("religiao", $this->pessoa_logada, $this->cod_religiao);
            $auditoria->exclusao($detalhe);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_religiao_lst.php');
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
