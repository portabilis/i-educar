<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Escola Localiza&ccedil;&atilde;o" );
        $this->processoAp = "562";
        $this->renderBanner = false;
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
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


        $this->nome_url_cancelar = "Cancelar";
        $this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_escola_localizacao", $this->cod_escola_localizacao );

        // Filtros de Foreign Keys
//      $obrigatorio = true;
//      include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoOculto("ref_cod_instituicao", $this->ref_cod_instituicao);
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
            echo "<script>
                        parent.document.getElementById('ref_cod_escola_localizacao').options[parent.document.getElementById('ref_cod_escola_localizacao').options.length] = new Option('$this->nm_localizacao', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_escola_localizacao').value = '$cadastrou';
                        parent.document.getElementById('ref_cod_escola_localizacao').disabled = false;
                        window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
                    </script>";
            die();
            return true;
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {

    }

    function Excluir()
    {

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

<script>

Event.observe(window, 'load', Init, false);

function Init()
{
    $('ref_cod_instituicao').value = parent.document.getElementById('ref_cod_instituicao').value;
}

</script>
