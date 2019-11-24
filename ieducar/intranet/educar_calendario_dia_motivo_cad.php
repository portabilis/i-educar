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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Calend&aacute;rio Dia Motivo" );
        $this->processoAp = "576";
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

    var $cod_calendario_dia_motivo;
    var $ref_cod_escola;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $sigla;
    var $descricao;
    var $tipo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $nm_motivo;

    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_calendario_dia_motivo=$_GET["cod_calendario_dia_motivo"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 576, $this->pessoa_logada, 7, "educar_calendario_dia_motivo_lst.php" );

        if( is_numeric( $this->cod_calendario_dia_motivo ) )
        {

            $obj = new clsPmieducarCalendarioDiaMotivo( $this->cod_calendario_dia_motivo );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;


                $this->fexcluir = $obj_permissoes->permissao_excluir( 576, $this->pessoa_logada, 7 );
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro["cod_calendario_dia_motivo"]}" : "educar_calendario_dia_motivo_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' motivo de dias do calendário', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_calendario_dia_motivo", $this->cod_calendario_dia_motivo );

        if( $this->cod_calendario_dia_motivo )
        {
            $obj_calendario_dia_motivo = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo);
            $obj_calendario_dia_motivo_det = $obj_calendario_dia_motivo->detalhe();
            $this->ref_cod_escola = $obj_calendario_dia_motivo_det['ref_cod_escola'];
            $obj_ref_cod_escola = new clsPmieducarEscola( $this->ref_cod_escola );
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $this->ref_cod_instituicao = $det_ref_cod_escola['ref_cod_instituicao'];
        }

        // foreign keys
        $obrigatorio = true;
        $get_escola = true;
        // foreign keys

        // text
        $this->inputsHelper()->dynamic(array('instituicao','escola'));
        $this->campoTexto( "nm_motivo", "Motivo", $this->nm_motivo, 30, 255, true );
        $this->campoTexto( "sigla", "Sigla", $this->sigla, 15, 15, true );
        $this->campoMemo( "descricao", "Descric&atilde;o", $this->descricao, 60, 5, false );

        $opcoes = array( "" => "Selecione", "e" => "extra", "n" => "n&atilde;o-letivo" );
        $this->campoLista( "tipo", "Tipo", $opcoes, $this->tipo );

    }

    function Novo()
    {


        $obj = new clsPmieducarCalendarioDiaMotivo( null, $this->ref_cod_escola, null, $this->pessoa_logada, $this->sigla, $this->descricao, $this->tipo, null, null, 1, $this->nm_motivo );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $calendarioDiaMotivo = new clsPmieducarCalendarioDiaMotivo($cadastrou);
            $calendarioDiaMotivo = $calendarioDiaMotivo->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("calendario_dia_motivo", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($calendarioDiaMotivo);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_dia_motivo_lst')
            );
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $calendarioDiaMotivoDetalhe = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo);
        $calendarioDiaMotivoDetalheAntes = $calendarioDiaMotivoDetalhe->detalhe();

        $obj = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo, $this->ref_cod_escola, $this->pessoa_logada, null, $this->sigla, $this->descricao, $this->tipo, null, null, 1, $this->nm_motivo );
        $editou = $obj->edita();
        if( $editou )
        {
            $calendarioDiaMotivoDetalheDepois = $calendarioDiaMotivoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("calendario_dia_motivo", $this->pessoa_logada, $this->cod_calendario_dia_motivo);
            $auditoria->alteracao($calendarioDiaMotivoDetalheAntes, $calendarioDiaMotivoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_dia_motivo_lst')
            );
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo, null, $this->pessoa_logada, null, null, null, null, null, null, 0);
        $calendarioDiaMotivo = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("calendario_dia_motivo", $this->pessoa_logada, $this->cod_calendario_dia_motivo);
            $auditoria->exclusao($calendarioDiaMotivo);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_dia_motivo_lst')
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
