<?php


require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Idioma" );
        $this->processoAp = "590";
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

    var $cod_acervo_idioma;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_idioma;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_biblioteca;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_acervo_idioma=$_GET["cod_acervo_idioma"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 590, $this->pessoa_logada, 11,  "educar_acervo_idioma_lst.php" );

        if( is_numeric( $this->cod_acervo_idioma ) )
        {

            $obj = new clsPmieducarAcervoIdioma( $this->cod_acervo_idioma );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                $this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
                $this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

            $obj_permissoes = new clsPermissoes();
            if( $obj_permissoes->permissao_excluir( 590, $this->pessoa_logada, 11 ) )
            {
                $this->fexcluir = true;
            }

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_acervo_idioma_det.php?cod_acervo_idioma={$registro["cod_acervo_idioma"]}" : "educar_acervo_idioma_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' idioma', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_acervo_idioma", $this->cod_acervo_idioma );

    //foreign keys
    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'biblioteca'));

        // text
        $this->campoTexto( "nm_idioma", "Idioma", $this->nm_idioma, 30, 255, true );
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 590, $this->pessoa_logada, 11,  "educar_acervo_idioma_lst.php" );


        $obj = new clsPmieducarAcervoIdioma( $this->cod_acervo_idioma, $this->pessoa_logada, $this->pessoa_logada, $this->nm_idioma, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_biblioteca );
        $this->cod_acervo_idioma = $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
      $obj->cod_acervo_idioma = $this->cod_acervo_idioma;
      $acervo_idioma = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("acervo_idioma", $this->pessoa_logada, $this->cod_acervo_idioma);
      $auditoria->inclusao($acervo_idioma);
            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_acervo_idioma_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 590, $this->pessoa_logada, 11,  "educar_acervo_idioma_lst.php" );


        $obj = new clsPmieducarAcervoIdioma($this->cod_acervo_idioma, $this->pessoa_logada, $this->pessoa_logada, $this->nm_idioma, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_biblioteca);
    $detalheAntigo = $obj->detalhe();
        $editou = $obj->edita();
        if( $editou )
        {
      $detalheAtual = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("acervo_idioma", $this->pessoa_logada, $this->cod_acervo_idioma);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);
            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_acervo_idioma_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 590, $this->pessoa_logada, 11,  "educar_acervo_idioma_lst.php" );


        $obj = new clsPmieducarAcervoIdioma($this->cod_acervo_idioma, $this->pessoa_logada, $this->pessoa_logada, $this->nm_idioma, $this->data_cadastro, $this->data_exclusao, 0);
        $detalhe = $obj->detalhe();
    $excluiu = $obj->excluir();
        if( $excluiu )
        {
      $auditoria = new clsModulesAuditoriaGeral("acervo_idioma", $this->pessoa_logada, $this->cod_acervo_idioma);
      $auditoria->exclusao($detalhe);
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_acervo_idioma_lst.php');
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
