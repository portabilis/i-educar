<?php

/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Cliente" );
        $this->processoAp = "623";
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

    var $cod_cliente;
    var $nm_cliente;
    var $nm_biblioteca;
    var $ref_cod_instituicao;
    var $ref_cod_escola;
    var $ref_cod_biblioteca;
    var $ref_cod_cliente_tipo;
    var $ref_cod_cliente_tipo_original;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_idpes;
    var $login;
    var $senha;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $del_cod_cliente;
    var $del_cod_cliente_tipo;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_cliente                   = $_GET["cod_cliente"];
        $this->ref_cod_cliente_tipo          = $_GET["cod_cliente_tipo"];
        $this->ref_cod_cliente_tipo_original = $_GET["cod_cliente_tipo"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 623, $this->pessoa_logada, 11,  "educar_definir_cliente_tipo_lst.php" );

        if( is_numeric( $this->cod_cliente ) && is_numeric( $this->ref_cod_cliente_tipo ) )
        {

            $obj_cliente = new clsPmieducarCliente();
            $lst_cliente = $obj_cliente->listaCompleta( $this->cod_cliente,
                                                    null,
                                                    null,
                                                    null,
                                                    null,
                                                    null,
                                                    null,
                                                    null,
                                                    null,
                                                    null,
                                                    1,
                                                    null,
                                                    null,
                                                    $this->ref_cod_cliente_tipo );
            if ( $lst_cliente ) {
                foreach ( $lst_cliente as $cliente ) {
                    $this->ref_idpes           = $cliente["ref_idpes"];
                    $this->nm_cliente          = $cliente["nome"];
                    $this->nm_biblioteca       = $cliente["nm_biblioteca"];
                    $this->ref_cod_instituicao = $cliente["cod_instituicao"];
                    $this->ref_cod_escola      = $cliente["cod_escola"];
                    $this->ref_cod_biblioteca  = $cliente["cod_biblioteca"];
                    $obj_permissoes      = new clsPermissoes();
                    if( $obj_permissoes->permissao_excluir( 623, $this->pessoa_logada, 11 ) )
                    {
                        $this->fexcluir = true;
                    }

                    $retorno = "Editar";
                }
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_definir_cliente_tipo_det.php?cod_cliente={$this->cod_cliente}&cod_cliente_tipo={$this->ref_cod_cliente_tipo}" : "educar_definir_cliente_tipo_lst.php";
        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "ref_cod_cliente_tipo_original", $this->ref_cod_cliente_tipo_original );

        $instituicao_obrigatorio  = true;
        $escola_obrigatorio       = false;
        $biblioteca_obrigatorio   = true;
        $cliente_tipo_obrigatorio = true;
        $get_instituicao          = true;
        $get_escola               = true;
        $get_biblioteca           = true;
        $get_cliente_tipo         = true;

        if($this->cod_cliente){
            $instituicao_desabilitado = true;
            $escola_desabilitado = true;
            $biblioteca_desabilitado = true;
        }
        include( "include/pmieducar/educar_campo_lista.php" );
        if ( !$this->cod_cliente ) {
            $opcoes_cliente = array( "" => "Pesquise a pessoa clicando na lupa ao lado" );

            $this->campoListaPesq( "cod_cliente", "Cliente", $opcoes_cliente, $this->cod_cliente, "educar_pesquisa_cliente_lst.php?campo1=cod_cliente", "", false, "", "", null, null, "", true );
        }
        else {
            $this->campoOculto( "cod_cliente", $this->cod_cliente );
            $this->campoRotulo( "nm_cliente", "Cliente", $this->nm_cliente );
        }
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 623, $this->pessoa_logada, 11,  "educar_definir_cliente_tipo_lst.php" );

        $obj_cliente = new clsPmieducarCliente( $this->cod_cliente );
        $det_cliente = $obj_cliente->detalhe();

        if( $det_cliente ) {
            $obj_cliente_tipo = new clsPmieducarClienteTipoCliente( $this->ref_cod_cliente_tipo, $det_cliente["cod_cliente"], null, null, null, null );
            if ( $obj_cliente_tipo->existeCliente() )
            {
                $obj_cliente_tipo = new clsPmieducarClienteTipoCliente( $this->ref_cod_cliente_tipo, $det_cliente["cod_cliente"], null, null, null, $this->pessoa_logada, 1 );
                if ( $obj_cliente_tipo->trocaTipo() )
                {
                    $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
                    $this->simpleRedirect("educar_definir_cliente_tipo_lst.php");
                }
            }
            else
            {
                $obj_cliente_tipo = new clsPmieducarClienteTipoCliente( $this->ref_cod_cliente_tipo, $det_cliente["cod_cliente"], null, null, $this->pessoa_logada, null, 1 );
                if ( $obj_cliente_tipo->cadastra() )
                {
                    $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
                    $this->simpleRedirect("educar_definir_cliente_tipo_lst.php");
                }
            }
            $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

            return false;
        }
        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 623, $this->pessoa_logada, 11,  "educar_definir_cliente_tipo_lst.php" );

        $obj_cliente_tipo = new clsPmieducarClienteTipoCliente( $this->ref_cod_cliente_tipo, $this->cod_cliente, null, null, null, $this->pessoa_logada );
        if ( $obj_cliente_tipo->existeCliente() ) {
            //$obj_cliente_tipo = new clsPmieducarClienteTipoCliente( $this->ref_cod_cliente_tipo, $this->cod_cliente, null, null, null, $this->pessoa_logada, 1 );
            //if( $obj_cliente_tipo->edita() )
            //{
                //$obj_cliente_tipo = new clsPmieducarClienteTipoCliente( $this->ref_cod_cliente_tipo_original, $this->cod_cliente, null, null, null, $this->pessoa_logada, 0 );
                if ( $obj_cliente_tipo->trocaTipo() ) {
                    $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
                    $this->simpleRedirect("educar_definir_cliente_tipo_lst.php");
                }
        //  }
            $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

            return false;
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 623, $this->pessoa_logada, 11,  "educar_definir_cliente_tipo_lst.php" );


        $obj_cliente_tipo = new clsPmieducarClienteTipoCliente( $this->ref_cod_cliente_tipo, $this->cod_cliente, null, null, null, $this->pessoa_logada, 1 );
        if( $obj_cliente_tipo->existe() )
        {
            if ( $obj_cliente_tipo->excluir() ) {
                $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
                $this->simpleRedirect("educar_definir_cliente_tipo_lst.php");
            }
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
<script>
if ( document.getElementById( 'ref_cod_instituicao' ) ) {
    var ref_cod_instituicao = document.getElementById( 'ref_cod_instituicao' );
    ref_cod_instituicao.onchange = function() { getEscola(); getBiblioteca(1); getClienteTipo(); }
}
if ( document.getElementById( 'ref_cod_escola' ) ) {
    var ref_cod_escola = document.getElementById( 'ref_cod_escola' );
    ref_cod_escola.onchange = function() { if ( document.getElementById('ref_cod_escola').value != '' ) { getBiblioteca(2); } else { getBiblioteca(1); } getClienteTipo(); }
}
if ( document.getElementById( 'ref_cod_biblioteca' ) ) {
    var ref_cod_biblioteca = document.getElementById( 'ref_cod_biblioteca' );
    ref_cod_biblioteca.onchange = function() { getClienteTipo(); }
}
</script>
