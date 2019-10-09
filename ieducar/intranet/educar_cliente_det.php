<?php
/**
 *
 *  @author Prefeitura Municipal de Itajaí
 *  @updated 29/03/2007
 *  Pacote: i-PLB Software Público Livre e Brasileiro
 *
 *  Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí
 *                          ctima@itajai.sc.gov.br
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

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Cliente" );
        $this->processoAp = "603";
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

    var $cod_cliente;
    var $ref_cod_cliente_tipo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_idpes;
    var $login;
    var $senha;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $suspenso;
    var $pessoa_logada;

    var $ref_cod_biblioteca;

    function Gerar()
    {
        $this->titulo = "Cliente - Detalhe";


        $this->cod_cliente          = $_GET["cod_cliente"];
        $this->ref_cod_biblioteca   = $_GET["ref_cod_biblioteca"];

        $tmp_obj = new clsPmieducarCliente( $this->cod_cliente );
        $registro = $tmp_obj->lista( $this->cod_cliente, null, null, null, null, null, null, null, null, null, null, null, null, $this->ref_cod_biblioteca );

        if( ! $registro )
        {
            $this->simpleRedirect('educar_cliente_lst.php');
        }
        else {
            foreach ( $registro as $cliente )
            {
                if( $cliente["nome"] )
                {
                    $this->addDetalhe( array( "Cliente", "{$cliente["nome"]}") );
                }
                if( $cliente["login"] )
                {
                    $this->addDetalhe( array( "Login", "{$cliente["login"]}") );
                }
                $obj_banco = new clsBanco();
                $sql_unico = "SELECT ref_cod_motivo_suspensao
                                FROM pmieducar.cliente_suspensao
                               WHERE ref_cod_cliente = {$cliente["cod_cliente"]}
                                 AND data_liberacao IS NULL
                                 AND EXTRACT ( DAY FROM ( NOW() - data_suspensao ) ) < dias";
                $motivo    = $obj_banco->CampoUnico( $sql_unico );
                if ( is_numeric( $motivo ) ) {
                    $this->addDetalhe( array( "Status", "Suspenso" ) );
                        $obj_motivo_suspensao = new clsPmieducarMotivoSuspensao( $motivo );
                        $det_motivo_suspensao = $obj_motivo_suspensao->detalhe();
                        $this->suspenso = $motivo;
                        $this->addDetalhe( array( "Motivo da Suspensão", "{$det_motivo_suspensao["nm_motivo"]}" ) );
                        $this->addDetalhe( array( "Descrição", "{$det_motivo_suspensao["descricao"]}" ) );
                }
                else
                    $this->addDetalhe( array( "Status", "Regular" ) );

                $tipo_cliente = $obj_banco->CampoUnico("SELECT nm_tipo FROM pmieducar.cliente_tipo WHERE ref_cod_biblioteca IN (SELECT ref_cod_biblioteca FROM pmieducar.biblioteca_usuario WHERE ref_cod_usuario = '$this->pessoa_logada') AND cod_cliente_tipo = (SELECT ref_cod_cliente_tipo FROM pmieducar.cliente_tipo_cliente WHERE ref_cod_cliente = '$this->cod_cliente'  AND ref_cod_biblioteca = '$this->ref_cod_biblioteca')");
                if(is_string($tipo_cliente))
                {
                    $this->addDetalhe(array("Tipo", $tipo_cliente));
                }
            }
        }
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 603, $this->pessoa_logada, 11 ) )
        {
            $this->url_novo        = "educar_cliente_cad.php";
            $this->url_editar      = "educar_cliente_cad.php?cod_cliente={$cliente["cod_cliente"]}&ref_cod_biblioteca={$this->ref_cod_biblioteca}";
            if ( is_numeric( $this->suspenso ) ) {
                $this->array_botao     = array( "Liberar" );
                $this->array_botao_url = array( "educar_define_status_cliente_cad.php?cod_cliente={$cliente["cod_cliente"]}&ref_cod_biblioteca={$this->ref_cod_biblioteca}&status=liberar" );
            }
            else {
                $this->array_botao     = array( "Suspender" );
                $this->array_botao_url = array( "educar_define_status_cliente_cad.php?cod_cliente={$cliente["cod_cliente"]}&ref_cod_biblioteca={$this->ref_cod_biblioteca}&status=suspender" );
            }
        }

        $this->url_cancelar = "educar_cliente_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe do cliente', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
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
