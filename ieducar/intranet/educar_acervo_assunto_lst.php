<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Acervo Assunto" );
        $this->processoAp = "592";
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

    var $cod_acervo_assunto;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_assunto;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    #var $ref_cod_biblioteca;

    function Gerar()
    {
        $this->titulo = "Acervo Assunto - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Assunto",
            "Descrição"
        ) );

        // Filtros de Foreign Keys
        #$get_escola = true;
        #$get_biblioteca = true;
        #$get_cabecalho = "lista_busca";
        #include("include/pmieducar/educar_campo_lista.php");

        // outros Filtros
        $this->campoTexto( "nm_assunto", "Assunto", $this->nm_assunto, 30, 255, false );
        $this->campoTexto( "descricao", "Descrição", $this->descricao, 30, 255, false );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        if(!is_numeric($this->ref_cod_biblioteca))
        {
            $obj_bib_user = new clsPmieducarBibliotecaUsuario();
            $this->ref_cod_biblioteca = $obj_bib_user->listaBibliotecas($this->pessoa_logada);
        }

        $obj_acervo_assunto = new clsPmieducarAcervoAssunto();
        $obj_acervo_assunto->setOrderby( "nm_assunto ASC" );
        $obj_acervo_assunto->setLimite( $this->limite, $this->offset );

        $lista = $obj_acervo_assunto->lista(
            null,
            null,
            null,
            $this->nm_assunto,
            $this->descricao,
            null,
            null,
            null,
            null,
            1
        );

        $total = $obj_acervo_assunto->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $this->addLinhas( array(
                    "<a href=\"educar_acervo_assunto_det.php?cod_acervo_assunto={$registro["cod_acervo_assunto"]}\">{$registro["nm_assunto"]}</a>",
                    "<a href=\"educar_acervo_assunto_det.php?cod_acervo_assunto={$registro["cod_acervo_assunto"]}\">{$registro['descricao']}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_acervo_assunto_lst.php", $total, $_GET, $this->nome, $this->limite );
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 592, $this->pessoa_logada, 11 ) )
        {
            $this->acao = "go(\"educar_acervo_assunto_cad.php\")";
            $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

        $this->breadcrumb('Listagem de assuntos', [
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
