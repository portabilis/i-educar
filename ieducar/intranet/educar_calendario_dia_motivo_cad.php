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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Calend&aacute;rio Dia Motivo" );
        $this->processoAp = "576";
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

    var $cod_calendario_dia_motivo;
    var $ref_cod_escola;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $sigla;
    var $descricao;
    var $tipo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $nm_motivo;

    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";
        

        $this->cod_calendario_dia_motivo=$_GET["cod_calendario_dia_motivo"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 576, $this->pessoa_logada, 7, "educar_calendario_dia_motivo_lst.php" );

        if( is_numeric( $this->cod_calendario_dia_motivo ) )
        {

            $obj = new clsPmieducarCalendarioDiaMotivo( $this->cod_calendario_dia_motivo );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;


                $this->fexcluir = $obj_permissoes->permissao_excluir( 576, $this->pessoa_logada, 7 );
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro["cod_calendario_dia_motivo"]}" : "educar_calendario_dia_motivo_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""        => "{$nomeMenu} motivo de dias do calend&aacute;rio"             
        ));
        $this->enviaLocalizacao($localizacao->montar());

        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_calendario_dia_motivo", $this->cod_calendario_dia_motivo );

        if( $this->cod_calendario_dia_motivo )
        {
            $obj_calendario_dia_motivo = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo);
            $obj_calendario_dia_motivo_det = $obj_calendario_dia_motivo->detalhe();
            $this->ref_cod_escola = $obj_calendario_dia_motivo_det['ref_cod_escola'];
            $obj_ref_cod_escola = new clsPmieducarEscola( $this->ref_cod_escola );
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $this->ref_cod_instituicao = $det_ref_cod_escola['ref_cod_instituicao'];
        }

        // foreign keys
        $obrigatorio = true;
        $get_escola = true;
        // foreign keys

        // text
        $this->inputsHelper()->dynamic(array('instituicao','escola'));
        $this->campoTexto( "nm_motivo", "Motivo", $this->nm_motivo, 30, 255, true );
        $this->campoTexto( "sigla", "Sigla", $this->sigla, 15, 15, true );
        $this->campoMemo( "descricao", "Descric&atilde;o", $this->descricao, 60, 5, false );

        $opcoes = array( "" => "Selecione", "e" => "extra", "n" => "n&atilde;o-letivo" );
        $this->campoLista( "tipo", "Tipo", $opcoes, $this->tipo );

    }

    function Novo()
    {
        

        $obj = new clsPmieducarCalendarioDiaMotivo( null, $this->ref_cod_escola, null, $this->pessoa_logada, $this->sigla, $this->descricao, $this->tipo, null, null, 1, $this->nm_motivo );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $calendarioDiaMotivo = new clsPmieducarCalendarioDiaMotivo($cadastrou);
            $calendarioDiaMotivo = $calendarioDiaMotivo->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("calendario_dia_motivo", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($calendarioDiaMotivo);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_dia_motivo_lst')
            );
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar clsPmieducarCalendarioDiaMotivo\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_escola ) && is_numeric( $this->pessoa_logada ) && is_string( $this->sigla ) && is_string( $this->tipo )\n-->";
        return false;
    }

    function Editar()
    {
        

        $calendarioDiaMotivoDetalhe = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo);
        $calendarioDiaMotivoDetalheAntes = $calendarioDiaMotivoDetalhe->detalhe();

        $obj = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo, $this->ref_cod_escola, $this->pessoa_logada, null, $this->sigla, $this->descricao, $this->tipo, null, null, 1, $this->nm_motivo );
        $editou = $obj->edita();
        if( $editou )
        {
            $calendarioDiaMotivoDetalheDepois = $calendarioDiaMotivoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("calendario_dia_motivo", $this->pessoa_logada, $this->cod_calendario_dia_motivo);
            $auditoria->alteracao($calendarioDiaMotivoDetalheAntes, $calendarioDiaMotivoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_dia_motivo_lst')
            );
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao editar clsPmieducarCalendarioDiaMotivo\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_calendario_dia_motivo ) && is_numeric( $this->pessoa_logada ) )\n-->";
        return false;
    }

    function Excluir()
    {
        

        $obj = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo, null, $this->pessoa_logada, null, null, null, null, null, null, 0);
        $calendarioDiaMotivo = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("calendario_dia_motivo", $this->pessoa_logada, $this->cod_calendario_dia_motivo);
            $auditoria->exclusao($calendarioDiaMotivo);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_dia_motivo_lst')
            );
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPmieducarCalendarioDiaMotivo\nvalores obrigatorios\nif( is_numeric( $this->cod_calendario_dia_motivo ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
