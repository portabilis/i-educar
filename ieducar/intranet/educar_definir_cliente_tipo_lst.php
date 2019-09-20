<?php

/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Cliente" );
        $this->processoAp = "623";
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

    var $cod_cliente;
    var $ref_cod_cliente_tipo;
    var $ref_cod_biblioteca;
    var $ref_cod_escola;
    var $ref_cod_instituicao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_idpes;
    var $login;
    var $senha;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $status;

    function Gerar()
    {
        $this->titulo = "Cliente - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $lista_busca = array(
            "Cliente",
            "Tipo do Cliente"
        );

        // Filtros de Foreign Keys
        $get_instituicao          = true;
        $get_escola               = true;
        $get_biblioteca           = true;
        $get_cliente_tipo         = true;
        $get_cabecalho = "lista_busca";
        include("include/pmieducar/educar_campo_lista.php");

        $this->addCabecalhos($lista_busca);

        $opcoes = array( "" => "Pesquise a pessoa clicando na lupa ao lado" );

        $parametros = new clsParametrosPesquisas();
        $parametros->setSubmit( 0 );
        $parametros->adicionaCampoSelect( "ref_idpes", "idpes", "nome" );
        $parametros->setCodSistema( 1 );
        $parametros->setPessoa( 'F' );
        $parametros->setPessoaEditar( 'N' );
        $parametros->setPessoaNovo( 'N' );
        $this->campoListaPesq( "ref_idpes", "Cliente", $opcoes, $this->ref_idpes, "pesquisa_pessoa_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_cliente = new clsPmieducarCliente();
        $obj_cliente->setOrderby( "nome ASC" );
        $obj_cliente->setLimite( $this->limite, $this->offset );

        if ( $this->status != 'S' )
            $this->status = null;
            $lista = $obj_cliente->listaCompleta( null,
                                                  null,
                                                  null,
                                                  $this->ref_idpes,
                                                  null,
                                                  null,
                                                  null,
                                                  null,
                                                  null,
                                                  null,
                                                  1,
                                                  null,
                                                  null,
                                                  $this->ref_cod_cliente_tipo,
                                                  $this->ref_cod_escola,
                                                  $this->ref_cod_biblioteca,
                                                  $this->ref_cod_instituicao );

        $total = $obj_cliente->_total;
        $obj_banco = new clsBanco();
        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                    $obj_ref_cod_biblioteca = new clsPmieducarBiblioteca( $registro["cod_biblioteca"] );
                    $det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
                    $registro["cod_biblioteca"] = $det_ref_cod_biblioteca["nm_biblioteca"];
                if( $registro["cod_instituicao"] )
                {
                    $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["cod_instituicao"] );
                    $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                    $registro["cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
                }
                if( $registro["cod_escola"] )
                {
                    $obj_ref_cod_escola = new clsPmieducarEscola();
                    $det_ref_cod_escola = array_shift($obj_ref_cod_escola->lista($registro["cod_escola"]));
                    $registro["cod_escola"] = $det_ref_cod_escola["nome"];
                }


                $lista_busca = array(
                    "<a href=\"educar_definir_cliente_tipo_det.php?cod_cliente={$registro["cod_cliente"]}&cod_cliente_tipo={$registro["cod_cliente_tipo"]}\">{$registro["nome"]}</a>",
                    "<a href=\"educar_definir_cliente_tipo_det.php?cod_cliente={$registro["cod_cliente"]}&cod_cliente_tipo={$registro["cod_cliente_tipo"]}\">{$registro["nm_tipo"]}</a>"
                );

                if ($qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8))
                    $lista_busca[] = "<a href=\"educar_definir_cliente_tipo_det.php?cod_cliente={$registro["cod_cliente"]}&cod_cliente_tipo={$registro["cod_cliente_tipo"]}\">{$registro["cod_biblioteca"]}</a>";
                else if ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4)
                    $lista_busca[] = "<a href=\"educar_definir_cliente_tipo_det.php?cod_cliente={$registro["cod_cliente"]}&cod_cliente_tipo={$registro["cod_cliente_tipo"]}\">{$registro["cod_biblioteca"]}</a>";
                if ($nivel_usuario == 1 || $nivel_usuario == 2)
                    $lista_busca[] = "<a href=\"educar_definir_cliente_tipo_det.php?cod_cliente={$registro["cod_cliente"]}&cod_cliente_tipo={$registro["cod_cliente_tipo"]}\">{$registro["cod_escola"]}</a>";
                if ($nivel_usuario == 1)
                    $lista_busca[] = "<a href=\"educar_definir_cliente_tipo_det.php?cod_cliente={$registro["cod_cliente"]}&cod_cliente_tipo={$registro["cod_cliente_tipo"]}\">{$registro["cod_instituicao"]}</a>";

                $this->addLinhas($lista_busca);

            }
        }
        $this->addPaginador2( "educar_definir_cliente_tipo_lst.php", $total, $_GET, $this->nome, $this->limite );
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 623, $this->pessoa_logada, 11 ) )
        {
            $this->acao = "go(\"educar_definir_cliente_tipo_cad.php\")";
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
<script>
if ( document.getElementById( 'ref_cod_instituicao' ) ) {
    var ref_cod_instituicao = document.getElementById( 'ref_cod_instituicao' );
    ref_cod_instituicao.onchange = function() { getEscola(); getBiblioteca(1); getClienteTipo(); }
}
if ( document.getElementById( 'ref_cod_escola' ) ) {
    var ref_cod_escola = document.getElementById( 'ref_cod_escola' );
    ref_cod_escola.onchange = function() { getBiblioteca(2); getClienteTipo(); }
}
if ( document.getElementById( 'ref_cod_biblioteca' ) ) {
    var ref_cod_biblioteca = document.getElementById( 'ref_cod_biblioteca' );
    ref_cod_biblioteca.onchange = function() { getClienteTipo(); }
}
</script>
