<?php


require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Servidores - Avalia&ccedil;&atilde;o Desempenho" );
        $this->processoAp = "635";
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

    var $sequencial;
    var $ref_cod_servidor;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $titulo_avaliacao;
    var $ref_ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->ref_cod_servidor=$_GET["ref_cod_servidor"];
        $this->ref_ref_cod_instituicao=$_GET["ref_ref_cod_instituicao"];
        $this->sequencial=$_GET["sequencial"];
        //echo $this->ref_cod_servidor. "e ".$this->ref_ref_cod_instituicao."<br>";
        //die();
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7,  "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}" );

        if( is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_servidor ) )
        {

            $obj = new clsPmieducarAvaliacaoDesempenho( $this->sequencial, $this->ref_cod_servidor, $this->ref_ref_cod_instituicao );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                if( $obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 7 ) )
                {
                    $this->fexcluir = true;
                }
                $retorno = "Editar";
            }
        }
        /*echo*/ $this->url_cancelar = ($retorno == "Editar") ? "educar_avaliacao_desempenho_det.php?sequencial={$this->sequencial}&ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}" : "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' avaliação de desempenho', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "sequencial", $this->sequencial );
        $this->campoOculto( "ref_cod_servidor", $this->ref_cod_servidor );
        $this->campoOculto( "ref_ref_cod_instituicao", $this->ref_ref_cod_instituicao );

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
        {
            $obj_instituicao = new clsPmieducarInstituicao( $this->ref_ref_cod_instituicao );
            $det_instituicao = $obj_instituicao->detalhe();
            $nm_instituicao = $det_instituicao["nm_instituicao"];
            $this->campoTexto( "nm_instituicao", "Institui&ccedil;&atilde;o", $nm_instituicao, 30, 255, false, false, false, "", "", "", "", true);
        }

        $obj_cod_servidor = new clsPessoa_( $this->ref_cod_servidor );
        $det_cod_servidor = $obj_cod_servidor->detalhe();
        $nm_servidor = $det_cod_servidor["nome"];

        $this->campoTexto( "nm_servidor", "Servidor", $nm_servidor, 30, 255, false, false, false, "", "", "", "", true);

        // text
        $this->campoTexto( "titulo_avaliacao", "Avalia&ccedil;&atilde;o", $this->titulo_avaliacao, 30, 255, true );
        $this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, true );
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7,  "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}" );

        $obj = new clsPmieducarAvaliacaoDesempenho( null, $this->ref_cod_servidor, $this->ref_ref_cod_instituicao, null, $this->pessoa_logada, $this->descricao, null, null, 1, $this->titulo_avaliacao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect("educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}");
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7,  "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}" );


        $obj = new clsPmieducarAvaliacaoDesempenho( $this->sequencial, $this->ref_cod_servidor, $this->ref_ref_cod_instituicao, $this->pessoa_logada, null, $this->descricao, null, null, 1, $this->titulo_avaliacao);
        $editou = $obj->edita();
        if( $editou )
        {
            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect("educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}");
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 7,  "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}" );


        $obj = new clsPmieducarAvaliacaoDesempenho( $this->sequencial, $this->ref_cod_servidor, $this->ref_ref_cod_instituicao, $this->pessoa_logada, null, null, null, null, 0 );
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect("educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}");
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";

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
