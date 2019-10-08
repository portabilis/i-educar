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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Etapa" );
        $this->processoAp = "584";
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

    var $cod_modulo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $descricao;
    var $num_etapas;
    var $num_meses;
    var $num_semanas;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_modulo=$_GET["cod_modulo"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            584,
            $this->pessoa_logada,
            3,
            "educar_modulo_lst.php"
        );

        if (is_numeric($this->cod_modulo))
        {
            $obj = new clsPmieducarModulo($this->cod_modulo);
            $registro  = $obj->detalhe();
            if ($registro)
            {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro AS $campo => $val) {
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir( 584, $this->pessoa_logada, 3))
                {
                    $this->fexcluir = true;
                }
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_modulo_det.php?cod_modulo={$registro["cod_modulo"]}" : "educar_modulo_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' etapa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_modulo", $this->cod_modulo );

        // Filtros de Foreign Keys
        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        $option = false;
        if ($this->existeEtapaNaEscola() or $this->existeEtapaNaTurma())
        {
            $option = true;
        }

        $this->campoTexto( "nm_tipo", "Etapa", $this->nm_tipo, 30, 255, true );
        $this->campoMemo( "descricao", "Descrição", $this->descricao, 60, 5, false );
        $this->campoNumero( "num_etapas", "Número de etapas", $this->num_etapas, 2, 2, true, null, null, null, null, null, $option);
        $this->campoNumero( "num_meses", "Número de meses", $this->num_meses, 2, 2, false );
        $this->campoNumero( "num_semanas", "Número de semanas", $this->num_semanas, 2, 2, false );
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(584, $this->pessoa_logada, 3,  "educar_modulo_lst.php");

        $obj = new clsPmieducarModulo( null, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, $this->num_meses, $this->num_semanas, null, null, 1, $this->ref_cod_instituicao, $this->num_etapas);
        $cadastrou = $obj->cadastra();
        if ($cadastrou)
        {
            $modulo = new clsPmieducarModulo($cadastrou);
            $modulo = $modulo->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("modulo", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($modulo);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_modulo_lst.php');
        }

        $this->mensagem = "Cadastro não realizado.<br>";

        return false;
    }

    function Editar()
    {


        $moduloDetalhe = new clsPmieducarModulo($this->cod_modulo);
        $moduloDetalheAntes = $moduloDetalhe->detalhe();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(584, $this->pessoa_logada, 3,  "educar_modulo_lst.php");

        $obj = new clsPmieducarModulo($this->cod_modulo, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, $this->num_meses, $this->num_semanas, null, null, 1, $this->ref_cod_instituicao, $this->num_etapas );
        $editou = $obj->edita();
        if ($editou)
        {
            $moduloDetalheDepois = $moduloDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("modulo", $this->pessoa_logada, $this->cod_modulo);
            $auditoria->alteracao($moduloDetalheAntes, $moduloDetalheDepois);

            $this->mensagem .= "Edição efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_modulo_lst.php');
        }

        $this->mensagem = "Edição não realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 584, $this->pessoa_logada, 3,  "educar_modulo_lst.php" );

        $obj = new clsPmieducarModulo($this->cod_modulo, $this->pessoa_logada, null,null,null,null,null,null,null, 0 );
        $modulo = $obj->detalhe();

        if ($this->existeEtapaNaEscola() or $this->existeEtapaNaTurma())
        {
            $this->mensagem = "Exclusão não realizada.<br>";
            $this->url_cancelar = "educar_modulo_det.php?cod_modulo={$modulo["cod_modulo"]}";
            return false;
        }

        $excluiu = $obj->excluir();
        if ($excluiu)
        {
            $auditoria = new clsModulesAuditoriaGeral("modulo", $this->pessoa_logada, $this->cod_modulo);
            $auditoria->exclusao($modulo);

            $this->mensagem .= "Exclusão efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_modulo_lst.php');
        }

        $this->mensagem = "Exclusão não realizada.<br>";

        return false;
    }

    function existeEtapaNaEscola()
    {
        if (! $this->cod_modulo)
        {
            return false;
        }

        $obj = new clsPmieducarAnoLetivoModulo($this->cod_modulo);
        $result = $obj->lista(null, null, null, $this->cod_modulo);

        return !empty($result);
    }

    function existeEtapaNaTurma()
    {
        if (! $this->cod_modulo) {
            return false;
        }

        $obj = new clsPmieducarTurmaModulo($this->cod_modulo);
        $result = $obj->lista(null, $this->cod_modulo);

        if (! $result > 0) {
            return false;
        }

        return true;
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
