<?php
error_reporting(E_ERROR);
ini_set("display_errors", 1);
/**
 *
 *  @author Prefeitura Municipal de Itajaí
 *  @updated 29/03/2007
 *   Pacote: i-PLB Software Público Livre e Brasileiro
 *
 *  Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí
 *                      ctima@itajai.sc.gov.br
 *
 *  Este  programa  é  software livre, você pode redistribuí-lo e/ou
 *  modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 *  publicada pela Free  Software  Foundation,  tanto  a versão 2 da
 *  Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.
 *
 *  Este programa  é distribuído na expectativa de ser útil, mas SEM
 *  QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-
 *  ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-
 *  sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.
 *
 *  Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU
 *  junto  com  este  programa. Se não, escreva para a Free Software
 *  Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA
 *  02111-1307, USA.
 *
 */

/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Cliente" );
        $this->processoAp = "603";
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
    var $ref_cod_instituicao;
    var $ref_cod_escola;
    var $ref_cod_biblioteca;
    var $ref_cod_cliente_tipo;
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
    var $acao_status;
    var $cod_motivo_suspensao;
    var $descricao;
    var $dias;
    var $sequencial;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_cliente        = $_GET["cod_cliente"];
        $this->acao_status        = $_GET["status"];
        $this->ref_cod_biblioteca = $_GET["ref_cod_biblioteca"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 603, $this->pessoa_logada, 11,  "educar_cliente_det.php" );

        if( is_numeric( $this->cod_cliente ) )
        {
            $obj = new clsPmieducarCliente( $this->cod_cliente );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                if ( $this->acao_status == "liberar" )
                    $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_cliente_det.php?cod_cliente={$registro["cod_cliente"]}" : "educar_cliente_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $this->breadcrumb('Motivo de suspensão do cliente', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        if ( $this->acao_status == "suspender" ) {

            $this->campoOculto("cod_cliente", $this->cod_cliente);
            $this->campoOculto("ref_cod_biblioteca", $this->ref_cod_biblioteca);

            if ( $this->ref_idpes ) {

                $objTemp = new clsPessoaFisica( $this->ref_idpes );
                $detalhe = $objTemp->detalhe();

                $this->campoRotulo( "nm_cliente", "Cliente", $detalhe["nome"] );
            }
            $this->campoNumero( "dias", "Dias", $this->dias, 9, 9, true );
                echo "<script> descricao = new Array();\n </script>";
                $opcoes[""] = "Selecione um motivo";
                $todos_motivos = "";
                $obj_motivo_suspensao = new clsPmieducarMotivoSuspensao();
                $lst_motivo_suspensao = $obj_motivo_suspensao->listaClienteBiblioteca( $this->cod_cliente );
                if ( $lst_motivo_suspensao )
                {
                    foreach ( $lst_motivo_suspensao as $motivo_suspensao ) {
                        $todos_motivos .= "descricao[descricao.length] = new Array( {$motivo_suspensao["cod_motivo_suspensao"]}, '{$motivo_suspensao["descricao"]}' );\n";
                        $opcoes["{$motivo_suspensao["cod_motivo_suspensao"]}"] = "{$motivo_suspensao["nm_motivo"]}";
                    }
                    echo "<script>{$todos_motivos}</script>";
                    $this->campoLista( "cod_motivo_suspensao", "Motivo da Suspensão", $opcoes, $this->cod_motivo_suspensao, "", false, "", "", false, true );
                    $this->campoMemo( "descricao", "Descrição", $this->descricao, 50, 5, false, "", "", false, false, "onClick", true );
                    echo "<script>
                            var before_getDescricao = function(){}
                            var after_getDescricao  = function(){}

                            function getDescricao()
                            {
                                before_getDescricao();

                                var campoMotivoSuspensao = document.getElementById( 'cod_motivo_suspensao' ).value;
                                var campoDescricao       = document.getElementById( 'descricao' );
                                for ( var j = 0; j < descricao.length; j++ )
                                {
                                    if ( descricao[j][0] == campoMotivoSuspensao )
                                    {
                                        campoDescricao.value = descricao[j][1];
                                    }
                                    else if ( campoMotivoSuspensao == '' )
                                    {
                                        campoDescricao.value = 'Sem descrição...';
                                    }
                                }
                                if ( campoDescricao.length == 0 && campoMotivoSuspensao != '' ) {
                                    campoDescricao.value = 'Sem descrição...';
                                }

                                after_getDescricao();
                            }
                         </script>";
                }
                else
                {
                    $this->campoLista( "cod_motivo_suspensao", "Motivo da Suspensão", array("" => "Não há motivo cadastrado"), "", "", false, "", "", true, true );
                }
        }
        elseif ( $this->acao_status == "liberar" ) {
            $db               = new clsBanco();
            $this->sequencial = $db->CampoUnico( "SELECT MAX( sequencial ) FROM pmieducar.cliente_suspensao WHERE ref_cod_cliente = {$this->cod_cliente} AND data_liberacao IS NULL" );
            $this->campoOculto("sequencial", $this->sequencial );

            $this->Editar();
        }
    }

    function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 603, $this->pessoa_logada, 11,  "educar_cliente_lst.php" );

        $obj = new clsPmieducarClienteSuspensao( null, $this->cod_cliente, $this->cod_motivo_suspensao, null, $this->pessoa_logada, $this->dias, null, null );

        // Caso suspensão tenha sido efetuada, envia para página de detalhes
        if ($sequencial = $obj->cadastra())
        {
      $obj->sequencial = $sequencial;
      $clienteSuspensao = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("cliente_suspensao", $this->pessoa_logada);
      $auditoria->inclusao($clienteSuspensao);

            $this->mensagem .= "Suspens&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect("educar_cliente_det.php?cod_cliente={$this->cod_cliente}&ref_cod_biblioteca={$this->ref_cod_biblioteca}");
        }

        $this->mensagem = "Suspens&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Editar()
    {

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 603, $this->pessoa_logada, 11,  "educar_cliente_lst.php" );

        $obj_suspensao = new clsPmieducarClienteSuspensao( $this->sequencial, $this->cod_cliente, null, $this->pessoa_logada, null, null, null, null );
        $detalheAntigo = $obj_suspensao->detalhe();
    if ( $obj_suspensao->edita() ) {
      $detalheAtual = $obj_suspensao->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("cliente_suspensao", $this->pessoa_logada);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);
            $this->mensagem .= "Libera&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect("educar_cliente_lst.php?cod_cliente={$this->cod_cliente}");
        }
        $obj = new clsPmieducarCliente( $this->cod_cliente, $this->pessoa_logada, $this->pessoa_logada, $this->ref_idpes, $this->login, $senha, $this->data_cadastro, $this->data_exclusao, $this->ativo );
        $editou = $obj->edita();
        $this->mensagem = "Libera&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 603, $this->pessoa_logada, 11,  "educar_cliente_lst.php" );


        $obj = new clsPmieducarCliente($this->cod_cliente, $this->ref_cod_cliente_tipo, $this->pessoa_logada, $this->pessoa_logada, $this->ref_idpes, $this->login, $this->senha, $this->data_cadastro, $this->data_exclusao, 0);
    $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
      $auditoria = new clsModulesAuditoriaGeral("cliente", $this->pessoa_logada, $this->cod_cliente);
      $auditoria->exclusao($detalhe);
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect("educar_cliente_det.php?cod_cliente={$this->cod_cliente}");
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

var ref_cod_motivo_suspensao = document.getElementById( 'cod_motivo_suspensao' );
ref_cod_motivo_suspensao.onchange = function() { getDescricao(); };


</script>
