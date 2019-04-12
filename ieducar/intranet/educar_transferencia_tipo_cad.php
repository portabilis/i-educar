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
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once ("include/modules/clsModulesAuditoriaGeral.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Motivo Transfer&ecirc;ncia" );
        $this->processoAp = "575";
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

    var $cod_transferencia_tipo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $desc_tipo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";
        

        $this->cod_transferencia_tipo=$_GET["cod_transferencia_tipo"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 575, $this->pessoa_logada, 7, "educar_transferencia_tipo_lst.php" );

        if( is_numeric( $this->cod_transferencia_tipo ) )
        {
            $obj = new clsPmieducarTransferenciaTipo();
            $lst  = $obj->lista( $this->cod_transferencia_tipo );
            $registro  = array_shift($lst);
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->fexcluir = $obj_permissoes->permissao_excluir( 575, $this->pessoa_logada,7 );
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_transferencia_tipo_det.php?cod_transferencia_tipo={$registro["cod_transferencia_tipo"]}" : "educar_transferencia_tipo_lst.php";
        
        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""        => "{$nomeMenu} tipo de transfer&ecirc;ncia"             
        ));
        $this->enviaLocalizacao($localizacao->montar());

        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_transferencia_tipo", $this->cod_transferencia_tipo );

        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoTexto( "nm_tipo", "Motivo Transfer&ecirc;ncia", $this->nm_tipo, 30, 255, true );
        $this->campoMemo( "desc_tipo", "Descri&ccedil;&atilde;o", $this->desc_tipo, 60, 5, false );
    }

    function Novo()
    {
        

        $obj = new clsPmieducarTransferenciaTipo( null,null,$this->pessoa_logada,$this->nm_tipo,$this->desc_tipo,null,null,1,$this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $transferenciaTipo = new clsPmieducarTransferenciaTipo($cadastrou);
            $transferenciaTipo = $transferenciaTipo->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("transferencia_tipo", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($transferenciaTipo);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_transferencia_tipo_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar clsPmieducarTransferenciaTipo\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_instituicao ) && is_string( $this->nm_tipo )\n-->";
        return false;
    }

    function Editar()
    {
        

        $transferenciaTipoDetalhe = new clsPmieducarTransferenciaTipo($this->cod_transferencia_tipo);
        $transferenciaTipoDetalheAntes = $transferenciaTipoDetalhe->detalhe();

        $obj = new clsPmieducarTransferenciaTipo( $this->cod_transferencia_tipo,$this->pessoa_logada,null,$this->nm_tipo,$this->desc_tipo,null,null,1,$this->ref_cod_instituicao );
        $editou = $obj->edita();
        if( $editou )
        {
            $transferenciaTipoDetalheDepois = $transferenciaTipoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("transferencia_tipo", $this->pessoa_logada, $this->cod_transferencia_tipo);
            $auditoria->alteracao($transferenciaTipoDetalheAntes, $transferenciaTipoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_transferencia_tipo_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao editar clsPmieducarTransferenciaTipo\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_transferencia_tipo ) && is_numeric( $this->pessoa_logada ) )\n-->";
        return false;
    }

    function Excluir()
    {
        

        $obj = new clsPmieducarTransferenciaTipo( $this->cod_transferencia_tipo, $this->pessoa_logada, null, null, null, null, null, 0);
        $transferenciaTipo = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("transferencia_tipo", $this->pessoa_logada, $this->cod_transferencia_tipo);
            $auditoria->exclusao($transferenciaTipo);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_transferencia_tipo_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPmieducarTransferenciaTipo\nvalores obrigat&oacuterios\nif( is_numeric( $this->cod_transferencia_tipo ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
