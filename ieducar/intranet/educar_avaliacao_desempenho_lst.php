<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Servidores - Avaliação Desempenho" );
        $this->processoAp = "635";
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
        $this->titulo = "Avaliação Desempenho - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;


        $this->ref_ref_cod_instituicao=($_GET['ref_cod_instituicao'] == "") ? $_GET['ref_ref_cod_instituicao'] : $_GET['ref_cod_instituicao'];



        $lista_busca = array(
            "Avalia&ccedil;&atilde;o",
            "Servidor"
        );

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
            $lista_busca[] = "Institui&ccedil;&atilde;o";

        $this->addCabecalhos($lista_busca);

        // outros Filtros
        $this->campoTexto( "titulo_avaliacao", "Avalia&ccedil;&atilde;o", $this->titulo_avaliacao, 30, 255, false );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_avaliacao_desempenho = new clsPmieducarAvaliacaoDesempenho();
        $obj_avaliacao_desempenho->setOrderby( "titulo_avaliacao ASC" );
        $obj_avaliacao_desempenho->setLimite( $this->limite, $this->offset );

        $lista = $obj_avaliacao_desempenho->lista(
            null,
            $this->ref_cod_servidor,
            $this->ref_ref_cod_instituicao,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->titulo_avaliacao
        );

        $total = $obj_avaliacao_desempenho->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $obj_cod_servidor = new clsPessoa_( $registro["ref_cod_servidor"] );
                $det_cod_servidor = $obj_cod_servidor->detalhe();
                $nm_servidor = $det_cod_servidor["nome"];

                $obj_instituicao = new clsPmieducarInstituicao( $registro["ref_ref_cod_instituicao"] );
                $det_instituicao = $obj_instituicao->detalhe();
                $nm_instituicao = $det_instituicao["nm_instituicao"];

                $lista_busca = array(
                    "<a href=\"educar_avaliacao_desempenho_det.php?sequencial={$registro["sequencial"]}&ref_cod_servidor={$registro["ref_cod_servidor"]}&ref_ref_cod_instituicao={$registro["ref_ref_cod_instituicao"]}\">{$registro["titulo_avaliacao"]}</a>",
                    "<a href=\"educar_avaliacao_desempenho_det.php?sequencial={$registro["sequencial"]}&ref_cod_servidor={$registro["ref_cod_servidor"]}&ref_ref_cod_instituicao={$registro["ref_ref_cod_instituicao"]}\">{$nm_servidor}</a>"
                );

                if ($nivel_usuario == 1)
                    $lista_busca[] = "<a href=\"educar_avaliacao_desempenho_det.php?sequencial={$registro["sequencial"]}&ref_cod_servidor={$registro["ref_cod_servidor"]}&ref_ref_cod_instituicao={$registro["ref_ref_cod_instituicao"]}\">{$nm_instituicao}</a>";
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2( "educar_avaliacao_desempenho_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}", $total, $_GET, $this->nome, $this->limite );
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7 ) )
        {
            //$this->array_botao_url[] = "educar_avaliacao_desempenho_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
            $this->array_botao_url[] = "educar_avaliacao_desempenho_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
            $this->array_botao[] = "Novo";
        }

        $this->array_botao_url[] = "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}";
        $this->array_botao[] = "Voltar";

        $this->largura = "100%";

        $this->breadcrumb('Registro da avaliação de desempenho do servidor', [
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
