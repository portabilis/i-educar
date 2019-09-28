<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Ra&ccedil;a" );
        $this->processoAp = "678";
    }
}

class indice extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $__pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $__titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $__limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $__offset;

    var $cod_raca;
    var $idpes_exc;
    var $idpes_cad;
    var $nm_raca;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->__pessoa_logada = $this->pessoa_logada;

        $this->__titulo = "Ra&ccedil;a - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Ra&ccedil;a"
        ) );

        // outros Filtros
        $this->campoTexto( "nm_raca", "Ra&ccedil;a", $this->nm_raca, 30, 255, false );


        // Paginador
        $this->__limite = 20;
        $this->__offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->__limite-$this->__limite: 0;

        $obj_raca = new clsCadastroRaca();
        $obj_raca->setOrderby( "nm_raca ASC" );
        $obj_raca->setLimite( $this->__limite, $this->__offset );

        $lista = $obj_raca->lista(
            null,
            null,
            $this->nm_raca,
            null,
            null,
            null,
            null,
            't'
        );

        $total = $obj_raca->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                // muda os campos data
                $registro["data_cadastro_time"] = strtotime( substr( $registro["data_cadastro"], 0, 16 ) );
                $registro["data_cadastro_br"] = date( "d/m/Y H:i", $registro["data_cadastro_time"] );

                $registro["data_exclusao_time"] = strtotime( substr( $registro["data_exclusao"], 0, 16 ) );
                $registro["data_exclusao_br"] = date( "d/m/Y H:i", $registro["data_exclusao_time"] );

                $this->addLinhas( array(
                    "<a href=\"educar_raca_det.php?cod_raca={$registro["cod_raca"]}\">{$registro["nm_raca"]}</a>"
                ) );

            }
        }
        $this->addPaginador2( "educar_raca_lst.php", $total, $_GET, $this->nome, $this->__limite );

        $obj_permissao = new clsPermissoes();
        if( $obj_permissao->permissao_cadastra(678, $this->__pessoa_logada, 7) )
        {
            $this->acao = "go(\"educar_raca_cad.php\")";
            $this->nome_acao = "Novo";
        }
        $this->largura = "100%";

        $this->breadcrumb('Listagem de raças', [
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
