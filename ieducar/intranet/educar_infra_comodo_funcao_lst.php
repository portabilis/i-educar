<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo de ambiente" );
        $this->processoAp = "572";
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

    var $cod_infra_comodo_funcao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_funcao;
    var $desc_funcao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;
    var $ref_cod_escola;

    function Gerar()
    {
        $this->titulo = "Tipo de ambiente - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $lista_busca = array(
            "Tipo de ambiente",
            "Escola",
            "Institui&ccedil;&atilde;o"
        );

        $this->addCabecalhos($lista_busca);

        $this->inputsHelper()->dynamic(array('instituicao', 'escola'));

        // outros Filtros
        $this->campoTexto( "nm_funcao", "Tipo", $this->nm_funcao, 30, 255, false );


        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_infra_comodo_funcao = new clsPmieducarInfraComodoFuncao();

        if (App_Model_IedFinder::usuarioNivelBibliotecaEscolar($this->pessoa_logada)) {
            $obj_infra_comodo_funcao->codUsuario = $this->pessoa_logada;
        }

        $obj_infra_comodo_funcao->setOrderby( "nm_funcao ASC" );
        $obj_infra_comodo_funcao->setLimite( $this->limite, $this->offset );

        $lista = $obj_infra_comodo_funcao->lista(
            $this->cod_infra_comodo_funcao,
            null,
            null,
            $this->nm_funcao,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_escola,
            $this->ref_cod_instituicao
        );

        $total = $obj_infra_comodo_funcao->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
                $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
                $nm_escola = $det_ref_cod_escola["nome"];

                $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];

                $lista_busca = array(
                    "<a href=\"educar_infra_comodo_funcao_det.php?cod_infra_comodo_funcao={$registro["cod_infra_comodo_funcao"]}\">{$registro["nm_funcao"]}</a>"
                );

                $lista_busca[] = "<a href=\"educar_infra_comodo_funcao_det.php?cod_infra_comodo_funcao={$registro["cod_infra_comodo_funcao"]}\">{$nm_escola}</a>";
                $lista_busca[] = "<a href=\"educar_infra_comodo_funcao_det.php?cod_infra_comodo_funcao={$registro["cod_infra_comodo_funcao"]}\">{$registro["ref_cod_instituicao"]}</a>";

                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2( "educar_infra_comodo_funcao_lst.php", $total, $_GET, $this->nome, $this->limite );


        $obj_permissao = new clsPermissoes();
        if($obj_permissao->permissao_cadastra(567, $this->pessoa_logada,7))
        {
            $this->acao = "go(\"educar_infra_comodo_funcao_cad.php\")";
            $this->nome_acao = "Novo";;
        }

        $this->largura = "100%";

        $this->breadcrumb('Listagem de tipos de ambiente', [
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
