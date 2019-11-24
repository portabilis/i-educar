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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Escola Localiza&ccedil;&atilde;o" );
        $this->processoAp = "562";
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

    var $cod_escola_localizacao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_localizacao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 562, $this->pessoa_logada, 3, "educar_escola_localizacao_lst.php" );

        $this->cod_escola_localizacao=$_GET["cod_escola_localizacao"];

        if( is_numeric( $this->cod_escola_localizacao ) )
        {
            $obj = new clsPmieducarEscolaLocalizacao( $this->cod_escola_localizacao );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->fexcluir = true;
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_escola_localizacao_det.php?cod_escola_localizacao={$registro["cod_escola_localizacao"]}" : "educar_escola_localizacao_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' localização', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_escola_localizacao", $this->cod_escola_localizacao );

        // Filtros de Foreign Keys
        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoTexto( "nm_localizacao", "Localiza&ccedil;&atilde;o", $this->nm_localizacao, 30, 255, true );
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 562, $this->pessoa_logada, 3, "educar_escola_localizacao_lst.php" );

        $obj = new clsPmieducarEscolaLocalizacao( null,null,$this->pessoa_logada,$this->nm_localizacao,null,null,1,$this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $escolaLocalizacao = new clsPmieducarEscolaLocalizacao($cadastrou);
            $escolaLocalizacao = $escolaLocalizacao->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("escola_localizacao", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($escolaLocalizacao);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_escola_localizacao_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $escolaLocalizacaoDetalhe = new clsPmieducarEscolaLocalizacao($this->cod_escola_localizacao);
        $escolaLocalizacaoDetalheAntes = $escolaLocalizacaoDetalhe->detalhe();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 562, $this->pessoa_logada, 3, "educar_escola_localizacao_lst.php" );

        $obj = new clsPmieducarEscolaLocalizacao( $this->cod_escola_localizacao,$this->pessoa_logada,null,$this->nm_localizacao,null,null,1,$this->ref_cod_instituicao );
        $editou = $obj->edita();
        if( $editou )
        {
            $escolaLocalizacaoDetalheDepois = $escolaLocalizacaoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("escola_localizacao", $this->pessoa_logada, $this->cod_escola_localizacao);
            $auditoria->alteracao($escolaLocalizacaoDetalheAntes, $escolaLocalizacaoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_escola_localizacao_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 562, $this->pessoa_logada, 3, "educar_escola_localizacao_lst.php" );

        $obj = new clsPmieducarEscolaLocalizacao( $this->cod_escola_localizacao,$this->pessoa_logada,null,null,null,null,0 );
        $escolaLocalizacao = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("escola_localizacao", $this->pessoa_logada, $this->cod_escola_localizacao);
            $auditoria->exclusao($escolaLocalizacao);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_escola_localizacao_lst.php');
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
