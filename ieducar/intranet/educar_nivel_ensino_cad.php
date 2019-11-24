<?php


use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once ("include/modules/clsModulesAuditoriaGeral.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - N&iacute;vel Ensino" );
        $this->processoAp = "571";
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

    var $cod_nivel_ensino;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_nivel;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_nivel_ensino=$_GET["cod_nivel_ensino"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 571, $this->pessoa_logada,3, "educar_nivel_ensino_lst.php" );

        if( is_numeric( $this->cod_nivel_ensino ) )
        {

            $obj = new clsPmieducarNivelEnsino( $this->cod_nivel_ensino );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->fexcluir = $obj_permissoes->permissao_excluir( 571, $this->pessoa_logada,3 );
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_nivel_ensino_det.php?cod_nivel_ensino={$registro["cod_nivel_ensino"]}" : "educar_nivel_ensino_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' nível de ensino', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_nivel_ensino", $this->cod_nivel_ensino );

        // foreign keys
        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoTexto( "nm_nivel", "N&iacute;vel Ensino", $this->nm_nivel, 30, 255, true );
        $this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );
    }

    function Novo()
    {


        $obj = new clsPmieducarNivelEnsino( null, null, $this->pessoa_logada, $this->nm_nivel, $this->descricao,null,null,1,$this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $nivelEnsino = new clsPmieducarNivelEnsino($cadastrou);
            $nivelEnsino = $nivelEnsino->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("nivel_ensino", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($nivelEnsino);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";

            throw new HttpResponseException(
                new RedirectResponse('educar_nivel_ensino_lst.php')
            );
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $nivelEnsinoDetalhe = new clsPmieducarNivelEnsino($this->cod_nivel_ensino);
        $nivelEnsinoDetalheAntes = $nivelEnsinoDetalhe->detalhe();

        $obj = new clsPmieducarNivelEnsino( $this->cod_nivel_ensino, $this->pessoa_logada, null, $this->nm_nivel, $this->descricao, null, null, 1, $this->ref_cod_instituicao );
        $editou = $obj->edita();
        if( $editou )
        {
            $nivelEnsinoDetalheDepois = $nivelEnsinoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("nivel_ensino", $this->pessoa_logada, $this->cod_nivel_ensino);
            $auditoria->alteracao($nivelEnsinoDetalheAntes, $nivelEnsinoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";

            throw new HttpResponseException(
                new RedirectResponse('educar_nivel_ensino_lst.php')
            );
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarNivelEnsino( $this->cod_nivel_ensino, $this->pessoa_logada, null, null, null, null, null, 0 );
        $nivelEnsino = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("nivel_ensino", $this->pessoa_logada, $this->cod_nivel_ensino);
            $auditoria->exclusao($nivelEnsino);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";

            throw new HttpResponseException(
                new RedirectResponse('educar_nivel_ensino_lst.php')
            );
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
