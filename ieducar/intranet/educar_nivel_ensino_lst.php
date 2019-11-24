<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - N&iacute;vel Ensino" );
        $this->processoAp = "571";
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

    var $cod_nivel_ensino;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_nivel;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "N&iacute;vel Ensino - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Nivel Ensino"
        ) );

        $lista_busca = array(
            "N&iacute;vel Ensino"
        );

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($nivel_usuario == 1)
            $lista_busca[] = "Institui&ccedil;&atilde;o";
        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        include("include/pmieducar/educar_campo_lista.php");

        // outros Filtros
        $this->campoTexto( "nm_nivel", "N&iacute;vel Ensino", $this->nm_nivel, 30, 255, false );


        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_nivel_ensino = new clsPmieducarNivelEnsino();
        $obj_nivel_ensino->setOrderby( "nm_nivel ASC" );
        $obj_nivel_ensino->setLimite( $this->limite, $this->offset );

        $lista = $obj_nivel_ensino->lista(
            null,
            null,
            null,
            $this->nm_nivel,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );

        $total = $obj_nivel_ensino->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];

                $lista_busca = array(
                    "<a href=\"educar_nivel_ensino_det.php?cod_nivel_ensino={$registro["cod_nivel_ensino"]}\">{$registro["nm_nivel"]}</a>"
                );

                if ($nivel_usuario == 1)
                    $lista_busca[] = "<a href=\"educar_nivel_ensino_det.php?cod_nivel_ensino={$registro["cod_nivel_ensino"]}\">{$registro["ref_cod_instituicao"]}</a>";
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2( "educar_nivel_ensino_lst.php", $total, $_GET, $this->nome, $this->limite );

        if( $obj_permissoes->permissao_cadastra( 571, $this->pessoa_logada,3 ) )
        {
            $this->acao = "go(\"educar_nivel_ensino_cad.php\")";
            $this->nome_acao = "Novo";
        }
        $this->largura = "100%";

        $this->breadcrumb('Listagem de níveis de ensino', [
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
