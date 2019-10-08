<?php


require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Bloqueio do ano letivo" );
        $this->processoAp = "21251";
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

    var $ref_cod_instituicao;
    var $ref_ano;
    var $ano;
    var $data_inicio;
    var $data_fim;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->ref_cod_instituicao=$_GET["ref_cod_instituicao"];
        $this->ref_ano=$_GET["ref_ano"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 21251, $this->pessoa_logada,3, "educar_bloqueio_ano_letivo_lst.php" );

        if( is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->ref_ano ) )
        {

            $obj = new clsPmieducarBloqueioAnoLetivo( $this->ref_cod_instituicao, $this->ref_ano );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->ano = $this->ref_ano;

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(21251,$this->pessoa_logada,3);
                //**

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro["ref_cod_instituicao"]}&ref_ano={$registro["ref_ano"]}" : "educar_bloqueio_ano_letivo_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' bloqueio ano letivo', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        $this->inputsHelper()->dynamic(array('instituicao', 'ano'));
        $this->inputsHelper()->date( 'data_inicio', array('label' => 'Data inicial permitida', 'value' => dataToBrasil($this->data_inicio), 'placeholder' => ''));
        $this->inputsHelper()->date( 'data_fim', array('label' => 'Data final permitida', 'value' => dataToBrasil($this->data_fim), 'placeholder' => ''));
    }

    function Novo()
    {


        $this->ref_ano = $this->ano;

        $obj = new clsPmieducarBloqueioAnoLetivo( $this->ref_cod_instituicao, $this->ref_ano, dataToBanco($this->data_inicio), dataToBanco($this->data_fim));
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_bloqueio_ano_letivo_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        return false;
    }

    function Editar()
    {


        $this->ref_ano = $this->ano;

        $obj = new clsPmieducarBloqueioAnoLetivo( $this->ref_cod_instituicao, $this->ref_ano, dataToBanco($this->data_inicio), dataToBanco($this->data_fim));
        $editou = $obj->edita();
        if( $editou )
        {
            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_bloqueio_ano_letivo_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        return false;
    }

    function Excluir()
    {


        $this->ref_ano = $this->ano;

        $obj = new clsPmieducarBloqueioAnoLetivo($this->ref_cod_instituicao, $this->ref_ano);
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_bloqueio_ano_letivo_lst.php');
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
