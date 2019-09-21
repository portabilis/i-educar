<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Operador" );
        $this->processoAp = "589";
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

    var $cod_operador;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nome;
    var $valor;
    var $fim_sentenca;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Operador - Detalhe";


        $this->cod_operador=$_GET["cod_operador"];

        $tmp_obj = new clsPmieducarOperador( $this->cod_operador );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_operador_lst.php');
        }

        $obj_ref_usuario_exc = new clsPmieducarUsuario( $registro["ref_usuario_exc"] );
        $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
        $registro["ref_usuario_exc"] = $det_ref_usuario_exc["data_cadastro"];

        $obj_ref_usuario_cad = new clsPmieducarUsuario( $registro["ref_usuario_cad"] );
        $det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
        $registro["ref_usuario_cad"] = $det_ref_usuario_cad["data_cadastro"];

        if( $registro["cod_operador"] )
        {
            $this->addDetalhe( array( "Operador", "{$registro["cod_operador"]}") );
        }
        if( $registro["nome"] )
        {
            $this->addDetalhe( array( "Nome", "{$registro["nome"]}") );
        }
        if( $registro["valor"] )
        {
            $this->addDetalhe( array( "Valor", "{$registro["valor"]}") );
        }
        if( ! is_null( $registro["fim_sentenca"] ) )
        {
            $registro["fim_sentenca"] = ( $registro["fim_sentenca"] ) ? "Sim": "Não";
            $this->addDetalhe( array( "Fim Sentenca", "{$registro["fim_sentenca"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 589, $this->pessoa_logada, 0, null, true ) )
        {
        $this->url_novo = "educar_operador_cad.php";
        $this->url_editar = "educar_operador_cad.php?cod_operador={$registro["cod_operador"]}";
        }

        $this->url_cancelar = "educar_operador_lst.php";
        $this->largura = "100%";
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
