<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Pre Requisito" );
        $this->processoAp = "601";
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

    var $cod_pre_requisito;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $schema_;
    var $tabela;
    var $nome;
    var $sql;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_pre_requisito=$_GET["cod_pre_requisito"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 601, $this->pessoa_logada, 3,  "educar_pre_requisito_lst.php", true );

        if( is_numeric( $this->cod_pre_requisito ) )
        {

            $obj = new clsPmieducarPreRequisito( $this->cod_pre_requisito );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                $this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
                $this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

            $obj_permissoes = new clsPermissoes();
            if( $obj_permissoes->permissao_excluir( 601, $this->pessoa_logada, 3, null, true ) )
            {
                $this->fexcluir = true;
            }

                $retorno = "Editar";
            }
        }
//      $this->url_cancelar = ($retorno == "Editar") ? "educar_pre_requisito_det.php?cod_pre_requisito={$registro["cod_pre_requisito"]}" : "educar_pre_requisito_lst.php";
        $this->nome_url_cancelar = "Cancelar";
        $this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
        return $retorno;
    }

    function Gerar()
    {
        $db = new clsBanco();

        // primary keys
        $this->campoOculto( "cod_pre_requisito", $this->cod_pre_requisito );

        // foreign keys
        $opcoes = array( "Selecione o Schema" );
        $db->Consulta( "SELECT DISTINCT schemaname FROM pg_catalog.pg_tables WHERE schemaname NOT IN ('pg_catalog', 'information_schema', 'pg_toast') ORDER BY schemaname" );
        while ( $db->ProximoRegistro() )
        {
            list( $schema ) = $db->Tupla();
            $opcoes[$schema] = $schema;
        }
        $this->campoLista( "schema_", "Schema", $opcoes, $this->schema_, "buscaTabela( 'tabela' )" );

        $opcoes = array( "Selecione a Tabela" );
        $this->campoLista( "tabela", "Tabela", $opcoes, $this->tabela, "", false, "", "", true );

        $this->campoTexto( "nome", "Nome", $this->nome, 30, 255, true );
        $this->campoMemo( "sql", "Sql", $this->sql, 60, 10, false );
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 601, $this->pessoa_logada, 3,  "educar_pre_requisito_lst.php", true );


        $obj = new clsPmieducarPreRequisito( $this->cod_pre_requisito, $this->pessoa_logada, $this->pessoa_logada, $this->schema_, $this->tabela, $this->nome, $this->sql, $this->data_cadastro, $this->data_exclusao, $this->ativo );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            echo "<script>
                        parent.document.getElementById('ref_cod_pre_requisito').options[parent.document.getElementById('ref_cod_pre_requisito').options.length] = new Option('$this->nome', '$cadastrou', false, false);
                        parent.document.getElementById('ref_cod_pre_requisito').value = '$cadastrou';
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
