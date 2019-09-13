<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Pre Requisito" );
        $this->processoAp = "601";
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

    var $cod_pre_requisito;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $schema_;
    var $tabela;
    var $nome;
    var $sql;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Pre Requisito - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Nome",
            "Schema ",
            "Tabela",
            "Sql"
        ) );

        // Filtros de Foreign Keys


        // outros Filtros
        $this->campoTexto( "nome", "Nome", $this->nome, 30, 255, false );
        $this->campoTexto( "schema_", "Schema ", $this->schema_, 30, 255, false );
        $this->campoTexto( "tabela", "Tabela", $this->tabela, 30, 255, false );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_pre_requisito = new clsPmieducarPreRequisito();
        $obj_pre_requisito->setOrderby( "nome ASC" );
        $obj_pre_requisito->setLimite( $this->limite, $this->offset );

        $lista = $obj_pre_requisito->lista(
            $this->cod_pre_requisito,
            null,
            null,
            $this->schema_,
            $this->tabela,
            $this->nome,
            $this->sql,
            null,
            null,
            1
        );

        $total = $obj_pre_requisito->_total;

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

                    $obj_ref_usuario_exc = new clsPmieducarUsuario( $registro["ref_usuario_exc"] );
                    $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
                    $registro["ref_usuario_exc"] = $det_ref_usuario_exc["data_cadastro"];

                    $obj_ref_usuario_cad = new clsPmieducarUsuario( $registro["ref_usuario_cad"] );
                    $det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
                    $registro["ref_usuario_cad"] = $det_ref_usuario_cad["data_cadastro"];

                $this->addLinhas( array(
                    "<a href=\"educar_pre_requisito_det.php?cod_pre_requisito={$registro["cod_pre_requisito"]}\">{$registro["nome"]}</a>",
                    "<a href=\"educar_pre_requisito_det.php?cod_pre_requisito={$registro["cod_pre_requisito"]}\">{$registro["schema_"]}</a>",
                    "<a href=\"educar_pre_requisito_det.php?cod_pre_requisito={$registro["cod_pre_requisito"]}\">{$registro["tabela"]}</a>",
                    "<a href=\"educar_pre_requisito_det.php?cod_pre_requisito={$registro["cod_pre_requisito"]}\">{$registro["sql"]}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_pre_requisito_lst.php", $total, $_GET, $this->nome, $this->limite );
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 601, $this->pessoa_logada, 3, null, true ) )
        {
            $this->acao = "go(\"educar_pre_requisito_cad.php\")";
            $this->nome_acao = "Novo";
        }

        $this->largura = "100%";
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
