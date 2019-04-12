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
require_once( "include/pmieducar/geral.inc.php" );
require_once "include/modules/clsModulesAuditoriaGeral.inc.php";

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Religiao" );
        $this->processoAp = "579";
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

    var $cod_religiao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_religiao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Inicializar()
    {
        $retorno = "Novo";
        

        $this->cod_religiao=$_GET["cod_religiao"];

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        $obj_permissao->permissao_cadastra(579, $this->pessoa_logada,3,"educar_religiao_lst.php");
        //**

        if( is_numeric( $this->cod_religiao ) )
        {

            $obj = new clsPmieducarReligiao( $this->cod_religiao );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                //** verificao de permissao para exclusao
                $this->fexcluir = $obj_permissao->permissao_excluir(579,$this->pessoa_logada,3);
                //**

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_religiao_det.php?cod_religiao={$registro["cod_religiao"]}" : "educar_religiao_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_pessoas_index.php"          => "Pessoas",
             ""        => "{$nomeMenu} religi&atilde;o"
        ));
        $this->enviaLocalizacao($localizacao->montar());

        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_religiao", $this->cod_religiao );

        // foreign keys

        // text
        $this->campoTexto( "nm_religiao", "Religi&atilde;o", $this->nm_religiao, 30, 255, true );

        // data

    }

    function Novo()
    {
        

        $obj = new clsPmieducarReligiao( $this->cod_religiao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_religiao, $this->data_cadastro, $this->data_exclusao, $this->ativo );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $religiao = new clsPmieducarReligiao($cadastrou);
            $religiao = $religiao->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("religiao", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($religiao);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_religiao_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar clsPmieducarReligiao\nvalores obrigatorios\nis_numeric( $this->ref_usuario_exc ) && is_numeric( $this->ref_usuario_cad ) && is_string( $this->nm_religiao )\n-->";
        return false;
    }

    function Editar()
    {
        

        $religiaoDetalhe = new clsPmieducarReligiao($this->cod_religiao);
        $religiaoDetalheAntes = $religiaoDetalhe->detalhe();

        $obj = new clsPmieducarReligiao($this->cod_religiao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_religiao, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $editou = $obj->edita();
        if( $editou )
        {
            $religiaoDetalheDepois = $religiaoDetalhe->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("religiao", $this->pessoa_logada, $this->cod_religiao);
            $auditoria->alteracao($religiaoDetalheAntes, $religiaoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_religiao_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao editar clsPmieducarReligiao\nvalores obrigatorios\nif( is_numeric( $this->cod_religiao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
        return false;
    }

    function Excluir()
    {
        

        $obj = new clsPmieducarReligiao($this->cod_religiao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_religiao, $this->data_cadastro, $this->data_exclusao, 0);
        $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("religiao", $this->pessoa_logada, $this->cod_religiao);
            $auditoria->exclusao($detalhe);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_religiao_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPmieducarReligiao\nvalores obrigatorios\nif( is_numeric( $this->cod_religiao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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
