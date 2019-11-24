<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Infra Predio" );
        $this->processoAp = "567";
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

    var $cod_infra_predio;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_cod_escola;
    var $nm_predio;
    var $desc_predio;
    var $endereco;
    var $data_cadastro;
    var $data_descricao;
    var $ativo;

    var $ref_cod_instituicao;

    function Gerar()
    {
        //** 2 - Escola 1 - institucional 0 - poli-institucional
        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

        $obj_infra_predio = new clsPmieducarInfraPredio();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_infra_predio->codUsuario = $this->pessoa_logada;
        }

        $obj_infra_predio->setOrderby( "nm_predio ASC" );
        $obj_infra_predio->setLimite( $this->limite, $this->offset );

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;

        $this->inputsHelper()->dynamic(array('instituicao', 'escola'), array("required" => FALSE));

        $this->addCabecalhos( array(
            "Institui&ccedil;&atilde;o",
            "Escola",
            "Nome Predio",
        ) );

        $obj_escola = new clsPmieducarEscola($this->ref_cod_escola,null,null,ref_cod_instituicao,null,null,null,null,null,null,1);
        $obj_escola->setCamposLista("cod_escola,nm_escola");

        if(!$obj_escola->detalhe() && !empty($this->ref_cod_escola) && !empty($this->ref_cod_instituicao))
            $this->ref_cod_instituicao = $this->ref_cod_escola = null;

        $lista = $obj_infra_predio->lista(
            $this->cod_infra_predio,
            null,
            null,
            $this->ref_cod_escola,
            $this->nm_predio,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $escola_in,
            $this->ref_cod_instituicao
        );

        $this->titulo = "Infra Predio - Listagem";

        // outros Filtros
        $this->campoTexto( "nm_predio", "Nome Pr&eacute;dio", $this->nm_predio, 30, 255, false );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $total = $obj_infra_predio->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $registro["ref_cod_instituicao"] = $det_ref_cod_escola["ref_cod_instituicao"];

                $obj_ref_cod_intituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
                $det_ref_cod_intituicao = $obj_ref_cod_intituicao->detalhe();
                $registro["ref_cod_instituicao"] = $det_ref_cod_intituicao["nm_instituicao"];

                $obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $registro["ref_cod_escola"] = $det_ref_cod_escola["nome"];

                $this->addLinhas( array(
                    "<a href=\"educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}\">{$registro["ref_cod_instituicao"]}</a>",
                    "<a href=\"educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}\">{$registro["ref_cod_escola"]}</a>",
                    "<a href=\"educar_infra_predio_det.php?cod_infra_predio={$registro["cod_infra_predio"]}\">{$registro["nm_predio"]}</a>"
                    ) );
            }
        }
        $this->addPaginador2( "educar_infra_predio_lst.php", $total, $_GET, $this->nome, $this->limite );


        //** Verificacao de permissao para cadastro

        if($obj_permissao->permissao_cadastra(567, $this->pessoa_logada,7))
        {
            $this->acao = "go(\"educar_infra_predio_cad.php\")";
            $this->nome_acao = "Novo";
        }
        //**
        $this->largura = "100%";

        $this->breadcrumb('Listagem de prédios', [
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
