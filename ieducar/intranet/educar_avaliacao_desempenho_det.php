<?php


require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
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

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

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

    function Gerar()
    {
        $this->titulo = "Avalia&ccedil;&atilde;o Desempenho - Detalhe";


        $this->ref_cod_servidor=$_GET["ref_cod_servidor"];
        $this->ref_ref_cod_instituicao=$_GET["ref_ref_cod_instituicao"];
        $this->sequencial=$_GET["sequencial"];

        $tmp_obj = new clsPmieducarAvaliacaoDesempenho( $this->sequencial, $this->ref_cod_servidor, $this->ref_ref_cod_instituicao );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_avaliacao_desempenho_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao( $registro["ref_ref_cod_instituicao"] );
        $det_instituicao = $obj_instituicao->detalhe();
        $nm_instituicao = $det_instituicao["nm_instituicao"];

        $obj_cod_servidor = new clsPessoa_( $this->ref_cod_servidor );
        $det_cod_servidor = $obj_cod_servidor->detalhe();
        $nm_servidor = $det_cod_servidor["nome"];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
        {
            if( $nm_instituicao )
            {
                $this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$nm_instituicao}") );
            }
        }
        if( $registro["ref_cod_servidor"] )
        {
            $this->addDetalhe( array( "Servidor", "{$nm_servidor}") );
        }
        if( $registro["titulo_avaliacao"] )
        {
            $this->addDetalhe( array( "Avalia&ccedil;&atilde;o", "{$registro["titulo_avaliacao"]}") );
        }
        if( $registro["descricao"] )
        {
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7 ) )
        {
            $this->url_novo = "educar_avaliacao_desempenho_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
            $this->url_editar = "educar_avaliacao_desempenho_cad.php?sequencial={$registro["sequencial"]}&ref_cod_servidor={$registro["ref_cod_servidor"]}&ref_ref_cod_instituicao={$registro["ref_ref_cod_instituicao"]}";
        }

        $this->url_cancelar = "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
        $this->largura = "100%";

        $this->breadcrumb('Detalhe da avaliação de desempenho', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
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
