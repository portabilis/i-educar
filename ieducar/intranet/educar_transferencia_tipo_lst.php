<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Motivo Transfer&ecirc;ncia" );
        $this->processoAp = "575";
    }
}

class indice extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $offset;

    var $cod_transferencia_tipo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $desc_tipo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Motivo Transfer&ecirc;ncia - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $lista_busca = array(
            "Transfer&ecirc;ncia"
        );

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
        {
            $lista_busca[] = "Institui&ccedil;&atilde;o";
        }

        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        include("include/pmieducar/educar_campo_lista.php");

        // outros Filtros
        $this->campoTexto( "nm_tipo", "Transfer&ecirc;ncia", $this->nm_tipo, 30, 255, false );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_transferencia_tipo = new clsPmieducarTransferenciaTipo();
        $obj_transferencia_tipo->setOrderby( "nm_tipo ASC" );
        $obj_transferencia_tipo->setLimite( $this->limite, $this->offset );

        $lista = $obj_transferencia_tipo->lista(
            null,
            null,
            null,
            $this->nm_tipo,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );

        $total = $obj_transferencia_tipo->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];

                $lista_busca = array(
                    "<a href=\"educar_transferencia_tipo_det.php?cod_transferencia_tipo={$registro["cod_transferencia_tipo"]}\">{$registro["nm_tipo"]}</a>"
                );

                if ($nivel_usuario == 1)
                {
                    $lista_busca[] = "<a href=\"educar_transferencia_tipo_det.php?cod_transferencia_tipo={$registro["cod_transferencia_tipo"]}\">{$registro["ref_cod_instituicao"]}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2( "educar_transferencia_tipo_lst.php", $total, $_GET, $this->nome, $this->limite );

        if( $obj_permissoes->permissao_cadastra( 575, $this->pessoa_logada, 7 ) )
        {
            $this->acao = "go(\"educar_transferencia_tipo_cad.php\")";
            $this->nome_acao = "Novo";
        }
        $this->largura = "100%";

        $this->breadcrumb('Listagem de tipos de transferência', [
            url('intranet/educar_index.php') => 'Escola',
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
