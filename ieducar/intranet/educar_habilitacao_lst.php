<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Habilita&ccedil;&atilde;o" );
        $this->processoAp = "573";
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

    var $cod_habilitacao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Habilita&ccedil;&atilde;o - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $get_escola = false;
        include("include/pmieducar/educar_campo_lista.php");

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

        switch ($nivel_usuario) {
            case 1:
                $this->addCabecalhos( array(
                    "Institui&ccedil;&atilde;o",
                    "Habilitac&atilde;o"
                ) );
                break;

            default:
                $this->addCabecalhos( array(
                    "Habilitac&atilde;o"
                ) );
                break;
        }


        // outros Filtros
        $this->campoTexto( "nm_tipo", "Habilita&ccedil;&atilde;o", $this->nm_tipo, 30, 255, false );


        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_habilitacao = new clsPmieducarHabilitacao();
        $obj_habilitacao->setOrderby( "nm_tipo ASC" );
        $obj_habilitacao->setLimite( $this->limite, $this->offset );

        $lista = $obj_habilitacao->lista(
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

        $total = $obj_habilitacao->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
                $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
                $registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];

                switch ($nivel_usuario) {
                    case 1:
                        $this->addLinhas( array(
                            "<a href=\"educar_habilitacao_det.php?cod_habilitacao={$registro["cod_habilitacao"]}\">{$registro["nm_tipo"]}</a>",
                            "<a href=\"educar_habilitacao_det.php?cod_habilitacao={$registro["cod_habilitacao"]}\">{$registro["ref_cod_instituicao"]}</a>"
                        ) );
                        break;

                    default:
                        $this->addLinhas( array(
                            "<a href=\"educar_habilitacao_det.php?cod_habilitacao={$registro["cod_habilitacao"]}\">{$registro["nm_tipo"]}</a>"
                        ) );
                        break;
                }

            }
        }
        $this->addPaginador2( "educar_habilitacao_lst.php", $total, $_GET, $this->nome, $this->limite );


        if( $obj_permissao->permissao_cadastra( 573, $this->pessoa_logada, 3 ) ) {
            $this->acao = "go(\"educar_habilitacao_cad.php\")";
            $this->nome_acao = "Novo";
        }
        $this->largura = "100%";

        $this->breadcrumb('Lista de habilitações', [
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
