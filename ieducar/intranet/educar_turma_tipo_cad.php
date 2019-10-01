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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Turma Tipo" );
        $this->processoAp = "570";
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

    var $cod_turma_tipo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $sgl_tipo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;
    var $ref_cod_escola;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_turma_tipo=$_GET["cod_turma_tipo"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 570, $this->pessoa_logada,7, "educar_turma_tipo_lst.php" );

        if( is_numeric( $this->cod_turma_tipo ) )
        {

            $obj = new clsPmieducarTurmaTipo( $this->cod_turma_tipo );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                //$obj_ref_cod_escola = new clsPmieducarEscola( $this->ref_cod_escola );
                //$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                //$this->ref_cod_instituicao = $det_ref_cod_escola["ref_cod_instituicao"];

                $this->fexcluir = $obj_permissoes->permissao_excluir( 570, $this->pessoa_logada,7 );
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_turma_tipo_det.php?cod_turma_tipo={$registro["cod_turma_tipo"]}" : "educar_turma_tipo_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Novo";

        $this->breadcrumb($nomeMenu . ' tipo de turma', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_turma_tipo", $this->cod_turma_tipo );

        $obrigatorio = true;
        // foreign keys
        $get_escola = false;
        include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoTexto( "nm_tipo", "Turma Tipo", $this->nm_tipo, 30, 255, true );
        $this->campoTexto( "sgl_tipo", "Sigla", $this->sgl_tipo, 15, 15, true );
    }

    function Novo()
    {


        $obj = new clsPmieducarTurmaTipo( null, null, $this->pessoa_logada, $this->nm_tipo, $this->sgl_tipo, null, null, 1, $this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $turmaTipo = new clsPmieducarTurmaTipo($cadastrou);
            $turmaTipo = $turmaTipo->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("turma_tipo", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($turmaTipo);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_turma_tipo_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $turmaTipoDetalhe = new clsPmieducarTurmaTipo($this->cod_turma_tipo);
        $turmaTipoDetalheAntes = $turmaTipoDetalhe->detalhe();

        $obj = new clsPmieducarTurmaTipo($this->cod_turma_tipo, $this->pessoa_logada, null, $this->nm_tipo, $this->sgl_tipo, null, null, 1, $this->ref_cod_instituicao);
        $editou = $obj->edita();
        if( $editou )
        {
            $turmaTipoDetalheDepois = $turmaTipoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("turma_tipo", $this->pessoa_logada, $this->cod_turma_tipo);
            $auditoria->alteracao($turmaTipoDetalheAntes, $turmaTipoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_turma_tipo_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarTurmaTipo($this->cod_turma_tipo, $this->pessoa_logada, null, null, null, null, null, 0);
        $turmaTipo = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("turma_tipo", $this->pessoa_logada, $this->cod_turma_tipo);
            $auditoria->exclusao($turmaTipo);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_turma_tipo_lst.php');
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
