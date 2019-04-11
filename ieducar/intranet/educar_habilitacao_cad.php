<?php
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

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once ("include/modules/clsModulesAuditoriaGeral.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Habilita&ccedil;&atilde;o" );
        $this->processoAp = "573";
        $this->addEstilo("localizacaoSistema");
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

    var $cod_habilitacao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";
        

        $this->cod_habilitacao=$_GET["cod_habilitacao"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 573, $this->pessoa_logada,3, "educar_habilitacao_lst.php" );

        if( is_numeric( $this->cod_habilitacao ) )
        {

            $obj = new clsPmieducarHabilitacao( $this->cod_habilitacao );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                $this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
                $this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

                $this->fexcluir = $obj_permissoes->permissao_excluir( 573, $this->pessoa_logada,3 );
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_habilitacao_det.php?cod_habilitacao={$registro["cod_habilitacao"]}" : "educar_habilitacao_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""        => "{$nomeMenu} habilita&ccedil;&acirc;o"             
        ));
        $this->enviaLocalizacao($localizacao->montar());

        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_habilitacao", $this->cod_habilitacao );
        // foreign keys

        $get_escola = false;
        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");
        // text
        $this->campoTexto( "nm_tipo", "Habilita&ccedil;&atilde;o", $this->nm_tipo, 30, 255, true );
        $this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );
    }

    function Novo()
    {
        

        $obj = new clsPmieducarHabilitacao( null, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao,null,null,1,$this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $habilitacao = new clsPmieducarHabilitacao($cadastrou);
            $habilitacao = $habilitacao->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("habilitacao", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($habilitacao);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";

            throw new HttpResponseException(
                new RedirectResponse('educar_habilitacao_lst.php')
            );
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar clsPmieducarHabilitacao\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada ) && is_string( $this->nm_tipo )\n-->";
        return false;
    }

    function Editar()
    {
        

        $habilitacaoDetalhe = new clsPmieducarHabilitacao($this->cod_habilitacao);
        $habilitacaoDetalheAntes = $habilitacaoDetalhe->detalhe();

        $obj = new clsPmieducarHabilitacao($this->cod_habilitacao, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, null, null, 1,$this->ref_cod_instituicao);
        $editou = $obj->edita();
        if( $editou )
        {
            $habilitacaoDetalheDepois = $habilitacaoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("habilitacao", $this->pessoa_logada, $this->cod_habilitacao);
            $auditoria->alteracao($habilitacaoDetalheAntes, $habilitacaoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";

            throw new HttpResponseException(
                new RedirectResponse('educar_habilitacao_lst.php')
            );
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao editar clsPmieducarHabilitacao\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_habilitacao ) && is_numeric( $this->pessoa_logada ) )\n-->";
        return false;
    }

    function Excluir()
    {
        

        $obj = new clsPmieducarHabilitacao($this->cod_habilitacao, $this->pessoa_logada, null, null, null, null, null, 0,$this->ref_cod_instituicao);
        $habilitacao = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("habilitacao", $this->pessoa_logada, $this->cod_habilitacao);
            $auditoria->exclusao($habilitacao);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";

            throw new HttpResponseException(
                new RedirectResponse('educar_habilitacao_lst.php')
            );
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPmieducarHabilitacao\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_habilitacao ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
