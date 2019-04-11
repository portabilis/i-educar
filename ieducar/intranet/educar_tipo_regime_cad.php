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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Regime" );
        $this->processoAp = "568";
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

    var $cod_tipo_regime;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";




        $this->cod_tipo_regime=$_GET["cod_tipo_regime"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 568, $this->pessoa_logada,3, "educar_tipo_regime_lst.php" );

        if( is_numeric( $this->cod_tipo_regime ) )
        {

            $obj = new clsPmieducarTipoRegime( $this->cod_tipo_regime );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;


                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissoes->permissao_excluir(568,$this->pessoa_logada,3);
                //**
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_tipo_regime_det.php?cod_tipo_regime={$registro["cod_tipo_regime"]}" : "educar_tipo_regime_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""        => "{$nomeMenu} tipo de regime"             
        ));
        $this->enviaLocalizacao($localizacao->montar());

        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_tipo_regime", $this->cod_tipo_regime );

        // foreign keys
        // foreign keys
        $get_escola = false;
        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");
        // text
        $this->campoTexto( "nm_tipo", "Nome Tipo", $this->nm_tipo, 30, 255, true );

        // data

    }

    function Novo()
    {


        $obj = new clsPmieducarTipoRegime( $this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $tipoRegime = new clsPmieducarTipoRegime($cadastrou);
            $tipoRegime = $tipoRegime->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("tipo_regime", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($tipoRegime);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_regime_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar clsPmieducarTipoRegime\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_string( $this->nm_tipo )\n-->";
        return false;
    }

    function Editar()
    {


        $tipoRegimeDetalhe = new clsPmieducarTipoRegime($this->cod_tipo_regime);
        $tipoRegimeDetalheAntes = $tipoRegimeDetalhe->detalhe();

        $obj = new clsPmieducarTipoRegime($this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_instituicao);
        $editou = $obj->edita();
        if( $editou )
        {
            $tipoRegimeDetalheDepois = $tipoRegimeDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("tipo_regime", $this->pessoa_logada, $this->cod_tipo_regime);
            $auditoria->alteracao($tipoRegimeDetalheAntes, $tipoRegimeDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_regime_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao editar clsPmieducarTipoRegime\nvalores obrigatorios\nif( is_numeric( $this->cod_tipo_regime ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarTipoRegime($this->cod_tipo_regime, $this->pessoa_logada, $this->pessoa_logada, $this->nm_tipo, $this->data_cadastro, $this->data_exclusao, 0, $this->ref_cod_instituicao);
        $tipoRegime = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("tipo_regime", $this->pessoa_logada, $this->cod_tipo_regime);
            $auditoria->exclusao($tipoRegime);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_regime_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPmieducarTipoRegime\nvalores obrigatorios\nif( is_numeric( $this->cod_tipo_regime ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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
