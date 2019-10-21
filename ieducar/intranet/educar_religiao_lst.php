<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Religiao" );
        $this->processoAp = "579";
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

    var $cod_religiao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_religiao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Religiao - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Nome Religi&atilde;o"
        ) );

        // Filtros de Foreign Keys


        // outros Filtros
        $this->campoTexto( "nm_religiao", "Nome Religi&atilde;o", $this->nm_religiao, 30, 255, false );


        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_religiao = new clsPmieducarReligiao();
        $obj_religiao->setOrderby( "nm_religiao ASC" );
        $obj_religiao->setLimite( $this->limite, $this->offset );

        $lista = $obj_religiao->lista(
            null,
            null,
            null,
            $this->nm_religiao,
            null,
            null,
            1
        );

        $total = $obj_religiao->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {

                $this->addLinhas( array(
                    "<a href=\"educar_religiao_det.php?cod_religiao={$registro["cod_religiao"]}\">{$registro["nm_religiao"]}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_religiao_lst.php", $total, $_GET, $this->nome, $this->limite );


        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(579, $this->pessoa_logada,3))
        {
            $this->acao = "go(\"educar_religiao_cad.php\")";
            $this->nome_acao = "Novo";
        }
        //**
        $this->largura = "100%";

        $this->breadcrumb('Listagem de religiões', [
            url('intranet/educar_pessoas_index.php') => 'Pessoas',
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
