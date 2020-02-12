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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Pagamento Multa" );
        $this->processoAp = "622";
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

    var $cod_pagamento_multa;
    var $ref_usuario_cad;
    var $ref_idpes;
    var $ref_cod_cliente;
    var $ref_cod_cliente_tipo;
    var $ref_cod_biblioteca;
    var $ref_cod_escola;
    var $ref_cod_instituicao;
    var $valor_pago;
    var $data_cadastro;

    function Gerar()
    {
        $this->titulo = "Pagamento Multa - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $lista_busca = array(
            "Cliente",
            "Valor Multa (Biblioteca)",
            "Valor Multa (Total)",
            "Valor Pago"
        );

        $obrigatorio              = false;
        $get_instituicao          = true;
        $get_escola               = true;
        $get_biblioteca           = true;
        $get_cliente_tipo         = true;
        $get_cabecalho            = "lista_busca";
        include("include/pmieducar/educar_campo_lista.php");

        $this->addCabecalhos($lista_busca);

        $parametros = new clsParametrosPesquisas();
        $parametros->setSubmit( 0 );
        $parametros->adicionaCampoSelect( "ref_idpes", "idpes", "nome" );
        $parametros->setPessoa( 'F' );
        $parametros->setPessoaCPF( 'N' );
        $parametros->setCodSistema( 1 );

    $dados = array(
      'nome' => 'Cliente',
      'campo' => '', // Como acao
      'valor' => array(null => 'Para procurar, clique na lupa ao lado.'),
      'default' => null,
      'acao' => "",
      'descricao' => "",
      'caminho' => 'pesquisa_pessoa_lst.php',
      'descricao2' => "",
      'flag' => null,
      'pag_cadastro' => null,
      'disabled' => "",
      'div' => false,
      'serializedcampos' => $parametros->serializaCampos(),
      'duplo' => false,
      'obrigatorio' => true
    );
    $this->setOptionsListaPesquisa("ref_idpes", $dados);

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;
        $obj_exemplar_emprestimo = new clsPmieducarExemplarEmprestimo();
        $lst_exemplar_emprestimo = $obj_exemplar_emprestimo->listaDividaPagamentoCliente( $this->ref_cod_cliente, $this->ref_idpes, $this->ref_cod_cliente_tipo, $this->pessoa_logada, $this->ref_cod_biblioteca, $this->ref_cod_escola, $this->ref_cod_instituicao );

        // monta a lista
        if( is_array( $lst_exemplar_emprestimo ) && count( $lst_exemplar_emprestimo ) )
        {
            foreach ( $lst_exemplar_emprestimo AS $registro )
            {
                $obj_cliente = new clsPmieducarCliente( $registro["ref_cod_cliente"] );
                $det_cliente = $obj_cliente->detalhe();
                if ( $det_cliente ) {
                    $obj_pessoa = new clsPessoa_( $det_cliente["ref_idpes"] );
                    $det_pessoa = $obj_pessoa->detalhe();
                    if ( $det_tipo )
                        $nm_tipo = $det_tipo["nm_tipo"];
                }
                if ( !is_numeric( $registro["valor_pago"] ) )
                    $registro["valor_pago"] = 0;

                $obj_ex_em = new clsPmieducarExemplarEmprestimo();
                $lst_ex_em = $obj_ex_em->listaTotalMulta( $registro["ref_cod_cliente"] );

                $multa_total = 0;
                $obj_total_divida = new clsPmieducarExemplarEmprestimo();
                $total_obj_divida = $obj_total_divida->totalMultaPorBiblioteca( $registro["ref_cod_cliente"], $registro["ref_cod_biblioteca"], true );
                $multa_total = $total_obj_divida[0]['sum'];

                $obj_bib = new clsPmieducarBiblioteca( $registro["ref_cod_biblioteca"] );
                $det_bib = $obj_bib->detalhe();
                if ( $det_bib ) {
                    $obj_inst = new clsPmieducarInstituicao( $det_bib["ref_cod_instituicao"] );
                    $det_inst = $obj_inst->detalhe();
                    $obj_escola = new clsPmieducarEscola( $det_bib["ref_cod_escola"] );
                    $det_escola = $obj_escola->detalhe();
                    if ( $det_escola ) {
                        $obj_pes = new clsPessoa_( $det_escola["ref_idpes"] );
                        $det_pes = $obj_pes->detalhe();
                        if ( $det_pes ) {
                            $nome_escola = $det_pes["nome"];
                        }
                    }
                }

                $obj_tipo = new clsPmieducarCliente();
                $det_tipo = $obj_tipo->retornaTipoCliente( $registro["ref_cod_cliente"], $registro["ref_cod_biblioteca"] );
                $lista_busca = array(
                    $lista_busca[] = "<a href=\"educar_pagamento_multa_det.php?cod_cliente={$registro["ref_cod_cliente"]}&cod_cliente_tipo={$det_tipo["cod_cliente_tipo"]}\">{$det_pessoa["nome"]}</a>",
                    $lista_busca[] = "<a href=\"educar_pagamento_multa_det.php?cod_cliente={$registro["ref_cod_cliente"]}&cod_cliente_tipo={$det_tipo["cod_cliente_tipo"]}\">"."R$".number_format( $registro["valor_multa"], 2, ",", "." )."</a>",
                    $lista_busca[] = "<a href=\"educar_pagamento_multa_det.php?cod_cliente={$registro["ref_cod_cliente"]}&cod_cliente_tipo={$det_tipo["cod_cliente_tipo"]}\">"."R$".number_format( $multa_total, 2, ",", "." )."</a>",
                    $lista_busca[] = "<a href=\"educar_pagamento_multa_det.php?cod_cliente={$registro["ref_cod_cliente"]}&cod_cliente_tipo={$det_tipo["cod_cliente_tipo"]}\">"."R$".number_format( $registro["valor_pago"], 2, ",", "." )."</a>"
                );

                if ($qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8)) {
                    $lista_busca[] = "<a href=\"educar_pagamento_multa_det.php?cod_cliente={$registro["ref_cod_cliente"]}&cod_cliente_tipo={$det_tipo["cod_cliente_tipo"]}\">{$det_bib["nm_biblioteca"]}</a>";
                }
                else if ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4) {
                    $lista_busca[] = "<a href=\"educar_pagamento_multa_det.php?cod_cliente={$registro["ref_cod_cliente"]}&cod_cliente_tipo={$det_tipo["cod_cliente_tipo"]}\">{$det_bib["nm_biblioteca"]}</a>";
                }
                if ($nivel_usuario == 1 || $nivel_usuario == 2) {
                    $lista_busca[] = "<a href=\"educar_pagamento_multa_det.php?cod_cliente={$registro["ref_cod_cliente"]}&cod_cliente_tipo={$det_tipo["cod_cliente_tipo"]}\">{$nome_escola}</a>";
                }
                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_pagamento_multa_det.php?cod_cliente={$registro["ref_cod_cliente"]}&cod_cliente_tipo={$det_tipo["cod_cliente_tipo"]}\">{$det_inst["nm_instituicao"]}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2( "educar_pagamento_multa_lst.php", $total, $_GET, $this->nome, $this->limite );
        $obj_permissoes = new clsPermissoes();
//      if( $obj_permissoes->permissao_cadastra( 622, $this->pessoa_logada, 11 ) )
//      {
//      $this->acao = "go(\"educar_pagamento_multa_cad.php\")";
//      $this->nome_acao = "Novo";
//      }

        $this->largura = "100%";

        $this->breadcrumb('Listagem de dívidas', [
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
