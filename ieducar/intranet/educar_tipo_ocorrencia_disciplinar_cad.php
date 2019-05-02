<?php
 // error_reporting(E_ERROR);
 // ini_set("display_errors", 1);
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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Ocorr&ecirc;ncia Disciplinar" );
        $this->processoAp = "580";
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

    var $cod_tipo_ocorrencia_disciplinar;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $descricao;
    var $max_ocorrencias;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_tipo_ocorrencia_disciplinar=$_GET["cod_tipo_ocorrencia_disciplinar"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 580, $this->pessoa_logada, 3, "educar_tipo_ocorrencia_disciplinar_lst.php" );

        if( is_numeric( $this->cod_tipo_ocorrencia_disciplinar ) )
        {
            $obj = new clsPmieducarTipoOcorrenciaDisciplinar( $this->cod_tipo_ocorrencia_disciplinar );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->fexcluir = $obj_permissoes->permissao_excluir( 580, $this->pessoa_logada,3 );
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_tipo_ocorrencia_disciplinar_det.php?cod_tipo_ocorrencia_disciplinar={$registro["cod_tipo_ocorrencia_disciplinar"]}" : "educar_tipo_ocorrencia_disciplinar_lst.php";
        
        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""        => "{$nomeMenu} tipo de ocorr&ecirc;ncia disciplinar"             
        ));
        $this->enviaLocalizacao($localizacao->montar());        

        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_tipo_ocorrencia_disciplinar", $this->cod_tipo_ocorrencia_disciplinar );

        // foreign keys
        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoTexto( "nm_tipo", "Tipo Ocorr&ecirc;ncia Disciplinar", $this->nm_tipo, 30, 255, true );
        $this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );
        $this->campoNumero( "max_ocorrencias", "M&aacute;ximo Ocorr&ecirc;ncias", $this->max_ocorrencias, 4, 4, false );
    }

    function Novo()
    {


        $obj = new clsPmieducarTipoOcorrenciaDisciplinar( null, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, $this->max_ocorrencias, null, null, 1, $this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $tipoOcorrenciaDisciplinar = new clsPmieducarTipoOcorrenciaDisciplinar($cadastrou);
            $tipoOcorrenciaDisciplinar = $tipoOcorrenciaDisciplinar->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("tipo_ocorrencia_disciplinar", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($tipoOcorrenciaDisciplinar);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_ocorrencia_disciplinar_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar clsPmieducarTipoOcorrenciaDisciplinar\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada )  && is_string( $this->nm_tipo ) && is_numeric( $this->max_ocorrencias )\n-->";
        return false;
    }

    function Editar()
    {


        $tipoOcorrenciaDisciplinarDetalhe = new clsPmieducarTipoOcorrenciaDisciplinar($this->cod_tipo_ocorrencia_disciplinar);
        $tipoOcorrenciaDisciplinarDetalheAntes = $tipoOcorrenciaDisciplinarDetalhe->detalhe();

        $obj = new clsPmieducarTipoOcorrenciaDisciplinar( $this->cod_tipo_ocorrencia_disciplinar, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, $this->max_ocorrencias, null, null, 1, $this->ref_cod_instituicao );
        $editou = $obj->edita();
        if( $editou )
        {
            $tipoOcorrenciaDisciplinarDetalheDepois = $tipoOcorrenciaDisciplinarDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("tipo_ocorrencia_disciplinar", $this->pessoa_logada, $this->cod_tipo_ocorrencia_disciplinar);
            $auditoria->alteracao($tipoOcorrenciaDisciplinarDetalheAntes, $tipoOcorrenciaDisciplinarDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_ocorrencia_disciplinar_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao editar clsPmieducarTipoOcorrenciaDisciplinar\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_tipo_ocorrencia_disciplinar ) && is_numeric( $this->pessoa_logada ) )\n-->";
        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarTipoOcorrenciaDisciplinar($this->cod_tipo_ocorrencia_disciplinar, $this->pessoa_logada, null, null, null, null, null, null, 0);
        $tipoOcorrenciaDisciplinar = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("tipo_ocorrencia_disciplinar", $this->pessoa_logada, $this->cod_tipo_ocorrencia_disciplinar);
            $auditoria->exclusao($tipoOcorrenciaDisciplinar);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_ocorrencia_disciplinar_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPmieducarTipoOcorrenciaDisciplinar\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_tipo_ocorrencia_disciplinar ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
