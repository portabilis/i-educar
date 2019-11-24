<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Servidores -  Funções do servidor" );
        $this->processoAp = "634";
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

    var $cod_funcao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_funcao;
    var $abreviatura;
    var $professor;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_funcao=$_GET["cod_funcao"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 634, $this->pessoa_logada, 3,  "educar_funcao_lst.php" );

        if( is_numeric( $this->cod_funcao ) )
        {
            $obj = new clsPmieducarFuncao( $this->cod_funcao );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                if( $obj_permissoes->permissao_excluir( 634, $this->pessoa_logada, 3 ) )
                {
                    $this->fexcluir = true;
                }
                $retorno = "Editar";
            }

            if($this->professor == '0')
                $this->professor =  "N";
            elseif($this->professor == '1')
                $this->professor = "S";

        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_funcao_det.php?cod_funcao={$registro["cod_funcao"]}" : "educar_funcao_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' função', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_funcao", $this->cod_funcao );

        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoTexto( "nm_funcao", "Func&atilde;o", $this->nm_funcao, 30, 255, true );
        $this->campoTexto( "abreviatura", "Abreviatura", $this->abreviatura, 30, 255, true );
        $opcoes = array('' => 'Selecione',
                        'S' => 'Sim',
                        'N' => 'N&atilde;o'
                        );

        $this->campoLista( "professor", "Professor",$opcoes, $this->professor,"",false,"","",false,true);
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 634, $this->pessoa_logada, 3,  "educar_funcao_lst.php" );

        if($this->professor == 'N')
            $this->professor =  "0";
        elseif($this->professor == 'S')
            $this->professor = "1";

        $obj = new clsPmieducarFuncao( null, null, $this->pessoa_logada, $this->nm_funcao, $this->abreviatura, $this->professor, null, null, 1, $this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {

            $funcao = new clsPmieducarFuncao($cadastrou);
            $funcao = $funcao->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("servidor_funcao", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($funcao);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_funcao_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $funcao = new clsPmieducarFuncao($this->cod_funcao);
        $funcaoAntes = $funcao->detalhe();

        if($this->professor == 'N')
            $this->professor =  "0";
        elseif($this->professor == 'S')
            $this->professor = "1";

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 634, $this->pessoa_logada, 3,  "educar_funcao_lst.php" );


        $obj = new clsPmieducarFuncao($this->cod_funcao, $this->pessoa_logada, null, $this->nm_funcao, $this->abreviatura, $this->professor, null, null, 1, $this->ref_cod_instituicao );
        $editou = $obj->edita();
        if( $editou )
        {
            $funcaoDepois = $funcao->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("servidor_funcao", $this->pessoa_logada, $this->cod_funcao);
            $auditoria->alteracao($funcaoAntes, $funcaoDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_funcao_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 634, $this->pessoa_logada, 3,  "educar_funcao_lst.php" );


        $obj = new clsPmieducarFuncao( $this->cod_funcao, $this->pessoa_logada, null,null,null,null,null,null,0,$this->ref_cod_instituicao );
        $funcao = $obj->detalhe();

        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("servidor_funcao", $this->pessoa_logada, $this->cod_funcao);
            $auditoria->exclusao($funcao);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_funcao_lst.php');
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
