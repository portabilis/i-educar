<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Benefício do aluno");
        $this->processoAp = "581";
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

    var $cod_aluno_beneficio;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_beneficio;
    var $desc_beneficio;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Benef&iacute;cio Aluno - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Beneficio"
        ) );

        // Filtros de Foreign Keys

            //$obrigatorio = true;
            //include("include/pmieducar/educar_pesquisa_instituicao_escola.php");

        // outros Filtros
        $this->campoTexto( "nm_beneficio", "Benef&iacute;cio", $this->nm_beneficio, 30, 255, false );


        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_aluno_beneficio = new clsPmieducarAlunoBeneficio();
        $obj_aluno_beneficio->setOrderby( "nm_beneficio ASC" );
        $obj_aluno_beneficio->setLimite( $this->limite, $this->offset );

        $lista = $obj_aluno_beneficio->lista(
            null,
            null,
            null,
            $this->nm_beneficio,
            null,
            null,
            null,
            1
        );

        $total = $obj_aluno_beneficio->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {

                $this->addLinhas( array(
                    "<a href=\"educar_aluno_beneficio_det.php?cod_aluno_beneficio={$registro["cod_aluno_beneficio"]}\">{$registro["nm_beneficio"]}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_aluno_beneficio_lst.php", $total, $_GET, $this->nome, $this->limite );


        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(581, $this->pessoa_logada,3))
        {
            $this->acao = "go(\"educar_aluno_beneficio_cad.php\")";
            $this->nome_acao = "Novo";
        }
        //**

        $this->largura = "100%";

        $this->breadcrumb('Tipos de benefício do aluno', [
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
