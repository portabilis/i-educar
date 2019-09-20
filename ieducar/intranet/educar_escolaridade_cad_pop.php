<?php

/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/Geral.inc.php" );
require_once( "include/pmieducar/geral.inc.php" );
require_once 'lib/Portabilis/String/Utils.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Escolaridade" );
        $this->processoAp = "632";
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

    var $idesco;
    var $descricao;
    var $escolaridade;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->idesco=$_GET["idesco"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 632, $this->pessoa_logada, 4,  "educar_escolaridade_lst.php" );

        if( is_numeric( $this->idesco ) )
        {

            $obj = new clsCadastroEscolaridade( $this->idesco );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                if( $obj_permissoes->permissao_excluir( 632, $this->pessoa_logada, 3 ) )
                {
                    $this->fexcluir = true;
                }

                $retorno = "Editar";
            }
        }
//      $this->url_cancelar = ($retorno == "Editar") ? "educar_escolaridade_det.php?idesco={$registro["idesco"]}" : "educar_escolaridade_lst.php";
        $this->nome_url_cancelar = "Cancelar";
        $this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "idesco", $this->idesco );

        // text
        $this->campoTexto( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 30, 255, true );

        $resources = array(1 => 'Fundamental incompleto',
                           2 => 'Fundamental completo',
                           3 => 'Ensino médio - Normal/Magistério',
                           4 => 'Ensino médio - Normal/Magistério Indígena',
                           5 => 'Ensino médio',
                           6 => 'Superior');

        $options = array('label' => Portabilis_String_Utils::toLatin1('Escolaridade'), 'resources' => $resources, 'value' => $this->escolaridade);
        $this->inputsHelper()->select('escolaridade', $options);


    }

    function Novo()
    {


        $obj = new clsCadastroEscolaridade( null, $this->descricao, $this->escolaridade );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            echo "<script>
                        parent.document.getElementById('ref_idesco').options[parent.document.getElementById('ref_idesco').options.length] = new Option('$this->descricao', '$cadastrou', false, false);
                        parent.document.getElementById('ref_idesco').value = '$cadastrou';
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
