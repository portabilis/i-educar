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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Ensino" );
        $this->processoAp = "558";
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

    var $cod_tipo_ensino;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $atividade_complementar;

    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";
        

        //** Verificacao de permissao para exclusao
        $obj_permissao = new clsPermissoes();

        $obj_permissao->permissao_cadastra(558, $this->pessoa_logada,7,"educar_tipo_ensino_lst.php");
        //**

        $this->cod_tipo_ensino=$_GET["cod_tipo_ensino"];

        if( is_numeric( $this->cod_tipo_ensino ) )
        {
            
            $obj = new clsPmieducarTipoEnsino($this->cod_tipo_ensino,null,null,null,null,null,1);
            if(!$registro = $obj->detalhe()){
                $this->simpleRedirect('educar_tipo_ensino_lst.php');
            }

            if(!$registro["ativo"] )
                $this->simpleRedirect('educar_tipo_ensino_lst.php');

            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissao->permissao_excluir(558,$this->pessoa_logada,7);
                //**

                $retorno = "Editar";
            }
            $this->atividade_complementar = dbBool($this->atividade_complementar);
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_tipo_ensino_det.php?cod_tipo_ensino={$registro["cod_tipo_ensino"]}" : "educar_tipo_ensino_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""        => "{$nomeMenu} tipo de ensino"             
        ));
        $this->enviaLocalizacao($localizacao->montar());

        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_tipo_ensino", $this->cod_tipo_ensino );

        // foreign keys
        $get_escola = false;
        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");
        // text
        $this->campoTexto( "nm_tipo", "Tipo de Ensino", $this->nm_tipo, 30, 255, true );

        $this->campoCheck("atividade_complementar", "Atividade complementar", $this->atividade_complementar );

        // data

    }

    function Novo()
    {
        

        $this->atividade_complementar = is_null($this->atividade_complementar) ? FALSE : TRUE;

        $obj = new clsPmieducarTipoEnsino();
        $obj->ref_usuario_cad        = $this->pessoa_logada;
        $obj->nm_tipo                = $this->nm_tipo;
        $obj->ativo                  = 1;
        $obj->ref_cod_instituicao    = $this->ref_cod_instituicao;
        $obj->atividade_complementar = $this->atividade_complementar;
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $tipoEnsino = new clsPmieducarTipoEnsino($cadastrou);
            $tipoEnsino = $tipoEnsino->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("tipo_ensino", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($tipoEnsino);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_ensino_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar clsPmieducarTipoEnsino\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_string( $this->nm_tipo ) && is_string( $this->data_cadastro ) && is_numeric( $this->ativo )\n-->";
        return false;
    }

    function Editar()
    {
        

        $this->atividade_complementar = is_null($this->atividade_complementar) ? FALSE : TRUE;

        $tipoEnsinoDetalhe = new clsPmieducarTipoEnsino($this->cod_tipo_ensino);
        $tipoEnsinoDetalheAntes = $tipoEnsinoDetalhe->detalhe();

        $obj = new clsPmieducarTipoEnsino();
        $obj->cod_tipo_ensino        = $this->cod_tipo_ensino;
        $obj->ref_usuario_exc        = $this->pessoa_logada;
        $obj->nm_tipo                = $this->nm_tipo;
        $obj->ativo                  = 1;
        $obj->ref_cod_instituicao    = $this->ref_cod_instituicao;
        $obj->atividade_complementar = $this->atividade_complementar;

        $editou = $obj->edita();
        if( $editou )
        {
            $tipoEnsinoDetalheDepois = $tipoEnsinoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("tipo_ensino", $this->pessoa_logada, $this->cod_tipo_ensino);
            $auditoria->alteracao($tipoEnsinoDetalheAntes, $tipoEnsinoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_ensino_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao editar clsPmieducarTipoEnsino\nvalores obrigatorios\nif( is_numeric( $this->cod_tipo_ensino ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
        return false;
    }

    function Excluir()
    {
        

        $obj = new clsPmieducarTipoEnsino($this->cod_tipo_ensino, $this->pessoa_logada, null, $this->nm_tipo, null, null, 0);
        $tipoEnsino = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("tipo_ensino", $this->pessoa_logada, $this->cod_tipo_ensino);
            $auditoria->exclusao($tipoEnsino);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_ensino_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPmieducarTipoEnsino\nvalores obrigatorios\nif( is_numeric( $this->cod_tipo_ensino ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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
