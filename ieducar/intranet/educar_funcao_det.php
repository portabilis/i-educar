<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
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

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

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

    function Gerar()
    {
        $this->titulo = "Funcao - Detalhe";


        $this->cod_funcao=$_GET["cod_funcao"];
        $this->ref_cod_instituicao=$_GET["ref_cod_instituicao"];

        $tmp_obj = new clsPmieducarFuncao( $this->cod_funcao,null,null,null,null,null,null,null,null,$this->ref_cod_instituicao );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_fonte_lst.php');
        }

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
        {
            if( $registro["ref_cod_instituicao"] )
            {
                $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
            }
        }
        if( $registro["cod_funcao"] )
        {
            $this->addDetalhe( array( "Func&atilde;o", "{$registro["cod_funcao"]}") );
        }
        if( $registro["nm_funcao"] )
        {
            $this->addDetalhe( array( "Nome Func&atilde;o", "{$registro["nm_funcao"]}") );
        }
        if( $registro["abreviatura"] )
        {
            $this->addDetalhe( array( "Abreviatura", "{$registro["abreviatura"]}") );
        }

        $opcoes = array('1' => 'Sim',
                        '0' => 'N&atilde;o'
                        );

        if( is_numeric($registro["professor"]) )
        {
            $this->addDetalhe( array( "Professor", "{$opcoes[$registro["professor"]]}") );
        }

        if( $obj_permissoes->permissao_cadastra( 634, $this->pessoa_logada, 3 ) )
        {
            $this->url_novo = "educar_funcao_cad.php";
            $this->url_editar = "educar_funcao_cad.php?cod_funcao={$registro["cod_funcao"]}";
        }

        $this->url_cancelar = "educar_funcao_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe da função', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
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
