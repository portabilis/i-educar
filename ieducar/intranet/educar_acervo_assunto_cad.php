<?php
//error_reporting(E_ERROR);
//ini_set("display_errors", 1);
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    *                                                                        *
    *   @author Prefeitura Municipal de Itajaí                               *
    *   @updated 29/03/2007                                                  *
    *   Pacote: i-PLB Software Público Livre e Brasileiro                    *
    *                                                                        *
    *   Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí             *
    *                       ctima@itajai.sc.gov.br                           *
    *                                                                        *
    *   Este  programa  é  software livre, você pode redistribuí-lo e/ou     *
    *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme     *
    *   publicada pela Free  Software  Foundation,  tanto  a versão 2 da     *
    *   Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.    *
    *                                                                        *
    *   Este programa  é distribuído na expectativa de ser útil, mas SEM     *
    *   QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-     *
    *   ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-     *
    *   sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.     *
    *                                                                        *
    *   Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU     *
    *   junto  com  este  programa. Se não, escreva para a Free Software     *
    *   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
    *   02111-1307, USA.                                                     *
    *                                                                        *
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Acervo Assunto" );
        $this->processoAp = "592";
        $this->addEstilo('localizacaoSistema');
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

    var $cod_acervo_assunto;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_assunto;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    #var $ref_cod_biblioteca;

    function Inicializar()
    {
        $retorno = "Novo";

        $this->cod_acervo_assunto=$_GET["cod_acervo_assunto"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 592, $this->pessoa_logada, 11,  "educar_acervo_assunto_lst.php" );

        if( is_numeric( $this->cod_acervo_assunto ) )
        {

            $obj = new clsPmieducarAcervoAssunto( $this->cod_acervo_assunto );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

            $obj_permissoes = new clsPermissoes();
            if( $obj_permissoes->permissao_excluir( 592, $this->pessoa_logada, 11 ) )
            {
                $this->fexcluir = true;
            }

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_acervo_assunto_det.php?cod_acervo_assunto={$registro["cod_acervo_assunto"]}" : "educar_acervo_assunto_lst.php";
        $this->nome_url_cancelar = "Cancelar";

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_biblioteca_index.php"                  => "Biblioteca",
         ""        => "{$nomeMenu} assunto"
    ));
    $this->enviaLocalizacao($localizacao->montar());

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_acervo_assunto", $this->cod_acervo_assunto );

    //foreign keys
    #$this->inputsHelper()->dynamic(array('instituicao', 'escola', 'biblioteca'));

        // text
        $this->campoTexto( "nm_assunto", "Assunto", $this->nm_assunto, 30, 255, true );
        $this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 592, $this->pessoa_logada, 11,  "educar_acervo_assunto_lst.php" );


        $obj = new clsPmieducarAcervoAssunto( null, null, $this->pessoa_logada, $this->nm_assunto, $this->descricao, null, null, 1);#, $this->ref_cod_biblioteca );
        $this->cod_acervo_assunto = $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
      $obj->cod_acervo_assunto = $this->cod_acervo_assunto;
      $acervo_assunto = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("acervo_assunto", $this->pessoa_logada, $this->cod_acervo_assunto);
      $auditoria->inclusao($acervo_assunto);
            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";

            $this->simpleRedirect('educar_abandono_tipo_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar clsPmieducarAcervoAssunto\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada ) && is_string( $this->nm_assunto )\n-->";
        return false;
    }

    function Editar()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 592, $this->pessoa_logada, 11,  "educar_acervo_assunto_lst.php" );


        $obj = new clsPmieducarAcervoAssunto($this->cod_acervo_assunto, $this->pessoa_logada, null, $this->nm_assunto, $this->descricao, null, null, 1);#, $this->ref_cod_biblioteca);
    $detalheAntigo = $obj->detalhe();
        $editou = $obj->edita();
        if( $editou )
        {
      $detalheAtual = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("acervo_assunto", $this->pessoa_logada, $this->cod_acervo_assunto);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);
            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";

            $this->simpleRedirect('educar_abandono_tipo_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao editar clsPmieducarAcervoAssunto\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_acervo_assunto ) && is_numeric( $this->pessoa_logada ) )\n-->";
        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 592, $this->pessoa_logada, 11,  "educar_acervo_assunto_lst.php" );


        $obj = new clsPmieducarAcervoAssunto($this->cod_acervo_assunto, $this->pessoa_logada, null, null, null, null, null, 0);
    $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {

      $auditoria = new clsModulesAuditoriaGeral("acervo_assunto", $this->pessoa_logada, $this->cod_acervo_assunto);
      $auditoria->exclusao($detalhe);
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";

            $this->simpleRedirect('educar_abandono_tipo_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPmieducarAcervoAssunto\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_acervo_assunto ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
