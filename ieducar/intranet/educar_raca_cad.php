<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once "lib/Portabilis/String/Utils.php";
require_once "include/modules/clsModulesAuditoriaGeral.inc.php";

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Ra&ccedil;a" );
        $this->processoAp = "678";
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

    var $cod_raca;
    var $idpes_exc;
    var $idpes_cad;
    var $nm_raca;
    var $data_cadastro;
    var $data_exclusao;
    var $raca_educacenso;
    var $ativo;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_raca=$_GET["cod_raca"];

        $obj_permissao = new clsPermissoes();
        $obj_permissao->permissao_cadastra(678, $this->pessoa_logada, 7, "educar_raca_lst.php");

        if( is_numeric( $this->cod_raca ) )
        {

            $obj = new clsCadastroRaca( $this->cod_raca );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                $this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
                $this->data_exclusao = dataFromPgToBr( $this->data_exclusao );


                $this->fexcluir = $obj_permissao->permissao_cadastra(678, $this->pessoa_logada, 7);

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_raca_det.php?cod_raca={$registro["cod_raca"]}" : "educar_raca_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' raça', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);

        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_raca", $this->cod_raca );

        $this->campoTexto( "nm_raca", "Ra&ccedil;a", $this->nm_raca, 30, 255, true );

        $resources = array   (  0 => 'Não declarada',
                                1 => "Branca",
                                2 => "Preta",
                                3 => "Parda",
                                4 => "Amarela",
                                5 => "Indígena");

        $options = array('label' => Portabilis_String_Utils::toLatin1('Raça educacenso'), 'resources' => $resources, 'value' => $this->raca_educacenso);
        $this->inputsHelper()->select('raca_educacenso', $options);

    }

    function Novo()
    {


        $obj = new clsCadastroRaca( $this->cod_raca, null, $this->pessoa_logada , $this->nm_raca, $this->data_cadastro, $this->data_exclusao, $this->ativo );
        $obj->raca_educacenso = $this->raca_educacenso;
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $raca = new clsCadastroRaca($cadastrou);
            $raca = $raca->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("raca", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($raca);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_raca_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $racaDetalhe = new clsCadastroRaca($this->cod_raca);
        $racaDetalheAntes = $racaDetalhe->detalhe();

        $obj = new clsCadastroRaca($this->cod_raca, $this->pessoa_logada, null, $this->nm_raca, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $obj->raca_educacenso = $this->raca_educacenso;
        $editou = $obj->edita();
        if( $editou )
        {
            $racaDetalheDepois = $racaDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("raca", $this->pessoa_logada, $this->cod_raca);
            $auditoria->alteracao($racaDetalheAntes, $racaDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_raca_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsCadastroRaca($this->cod_raca, $this->pessoa_logada, null, $this->nm_raca, $this->data_cadastro, $this->data_exclusao, 0);
        $detalhe = $obj->detalhe();

        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("raca", $this->pessoa_logada, $this->cod_raca);
            $auditoria->exclusao($detalhe);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_raca_lst.php');
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
