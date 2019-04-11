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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Dispensa" );
        $this->processoAp = "577";
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

    var $cod_tipo_dispensa;
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
        

        $this->cod_tipo_dispensa=$_GET["cod_tipo_dispensa"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 577, $this->pessoa_logada, 7, "educar_tipo_dispensa_lst.php" );

        if( is_numeric( $this->cod_tipo_dispensa ) )
        {

            $obj = new clsPmieducarTipoDispensa( $this->cod_tipo_dispensa );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->ref_cod_instituicao = $det_ref_cod_escola["ref_cod_instituicao"];

                $this->fexcluir = $obj_permissoes->permissao_excluir( 577, $this->pessoa_logada,7 );
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_tipo_dispensa_det.php?cod_tipo_dispensa={$registro["cod_tipo_dispensa"]}" : "educar_tipo_dispensa_lst.php";
        
        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""        => "{$nomeMenu} tipo de dispensa"             
        ));
        $this->enviaLocalizacao($localizacao->montar());        

        $this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_tipo_dispensa", $this->cod_tipo_dispensa );

        // foreign keys
        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoTexto( "nm_tipo", "Tipo Dispensa", $this->nm_tipo, 30, 255, true );
        $this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );
    }

    function Novo()
    {
        

//      echo "null, null, {$this->pessoa_logada}, {$this->nm_tipo}, {$this->descricao}, null, null, 1, {$this->ref_cod_escola}, {$this->ref_cod_instituicao}<br>";
        $obj = new clsPmieducarTipoDispensa( null, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, null, null, 1, $this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $tipoDispensa = new clsPmieducarTipoDispensa($cadastrou);
            $tipoDispensa = $tipoDispensa->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("tipo_dispensa", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($tipoDispensa);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        echo "<!--\nErro ao cadastrar clsPmieducarTipoDispensa\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_escola ) && is_string( $this->nm_tipo )\n-->";
        return false;
    }

    function Editar()
    {
        

        $tipoDispensaDetalhe = new clsPmieducarTipoDispensa($this->cod_tipo_dispensa);
        $tipoDispensaDetalheAntes = $tipoDispensaDetalhe->detalhe();

        $obj = new clsPmieducarTipoDispensa( $this->cod_tipo_dispensa, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, null, null, 1, $this->ref_cod_instituicao );
        $editou = $obj->edita();
        if( $editou )
        {
            $tipoDispensaDetalheDepois = $tipoDispensaDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("tipo_dispensa", $this->pessoa_logada, $this->cod_tipo_dispensa);
            $auditoria->alteracao($tipoDispensaDetalheAntes, $tipoDispensaDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao editar clsPmieducarTipoDispensa\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_tipo_dispensa ) && is_numeric( $this->pessoa_logada ) )\n-->";
        return false;
    }

    function Excluir()
    {
        

        $obj = new clsPmieducarTipoDispensa( $this->cod_tipo_dispensa, $this->pessoa_logada, null, null, null, null, null, 0 );
        $tipoDispensa = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("tipo_dispensa", $this->pessoa_logada, $this->cod_tipo_dispensa);
            $auditoria->exclusao($tipoDispensa);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_tipo_dispensa_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
        echo "<!--\nErro ao excluir clsPmieducarTipoDispensa\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_tipo_dispensa ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
