<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Motivo Baixa" );
        $this->processoAp = "600";
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

    var $cod_motivo_baixa;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_motivo_baixa;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_biblioteca;

    function Gerar()
    {
        $this->titulo = "Motivo Baixa - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Motivo Baixa",
            "Biblioteca"
        ) );


        $get_escola = true;
        $get_biblioteca = true;
        $get_cabecalho = "lista_busca";
        include("include/pmieducar/educar_campo_lista.php");

        // outros Filtros
        $this->campoTexto( "nm_motivo_baixa", "Motivo Baixa", $this->nm_motivo_baixa, 30, 255, false );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        if(!is_numeric($this->ref_cod_biblioteca))
        {
            $obj_bib_user = new clsPmieducarBibliotecaUsuario();
            $this->ref_cod_biblioteca = $obj_bib_user->listaBibliotecas($this->pessoa_logada);
        }

        $obj_motivo_baixa = new clsPmieducarMotivoBaixa();
        $obj_motivo_baixa->setOrderby( "nm_motivo_baixa ASC" );
        $obj_motivo_baixa->setLimite( $this->limite, $this->offset );

        $lista = $obj_motivo_baixa->lista(
            null,
            null,
            null,
            $this->nm_motivo_baixa,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_biblioteca
        );

        $total = $obj_motivo_baixa->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $obj_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
                $det_biblioteca = $obj_biblioteca->detalhe();
                $registro['ref_cod_biblioteca'] = $det_biblioteca['nm_biblioteca'];
                $this->addLinhas( array(
                    "<a href=\"educar_motivo_baixa_det.php?cod_motivo_baixa={$registro["cod_motivo_baixa"]}\">{$registro["nm_motivo_baixa"]}</a>",
                    "<a href=\"educar_motivo_baixa_det.php?cod_motivo_baixa={$registro["cod_motivo_baixa"]}\">{$registro["ref_cod_biblioteca"]}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_motivo_baixa_lst.php", $total, $_GET, $this->nome, $this->limite );
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 600, $this->pessoa_logada, 11 ) )
        {
            $this->acao = "go(\"educar_motivo_baixa_cad.php\")";
            $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

        $this->breadcrumb('Listagem de motivos de baixa', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
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
