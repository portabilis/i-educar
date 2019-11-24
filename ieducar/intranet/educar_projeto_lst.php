<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Projeto" );
        $this->processoAp = "21250";
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

    var $cod_projeto;
    var $nome;
    var $observacao;

    function Gerar()
    {
        $this->titulo = "Projetos - Listagem";

        foreach( $_GET AS $var => $val )
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Nome do projeto",
            'Observação'
        ) );

        $this->campoTexto( "nome", "Nome do projeto", $this->nome, 30, 255, false );


        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_projeto = new clsPmieducarProjeto();
        $obj_projeto->setOrderby( "nome ASC" );
        $obj_projeto->setLimite( $this->limite, $this->offset );

        $lista = $obj_projeto->lista(
            null,
            $this->nome
        );

        $total = $obj_projeto->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {

                $this->addLinhas( array(
                    "<a href=\"educar_projeto_det.php?cod_projeto={$registro["cod_projeto"]}\">{$registro["nome"]}</a>",
                    "<a href=\"educar_projeto_det.php?cod_projeto={$registro["cod_projeto"]}\">{$registro["observacao"]}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_projeto_lst.php", $total, $_GET, $this->nome, $this->limite );


        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(21250, $this->pessoa_logada,3))
        {
            $this->acao = "go(\"educar_projeto_cad.php\")";
            $this->nome_acao = "Novo";
        }
        //**

        $this->largura = "100%";

        $this->breadcrumb('Listagem de projetos', [
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
