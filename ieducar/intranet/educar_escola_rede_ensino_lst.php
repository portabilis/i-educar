<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Escola Rede Ensino" );
        $this->processoAp = "647";
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

    var $cod_escola_rede_ensino;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_rede;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Escola Rede Ensino - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $lista_busca = array(
            "Rede Ensino"
        );

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1)
            $lista_busca[] = "Institui&ccedil;&atilde;o";

        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        include("include/pmieducar/educar_campo_lista.php");

        $this->campoTexto( "nm_rede", "Rede Ensino", $this->nm_rede, 30, 255, false );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_escola_rede_ensino = new clsPmieducarEscolaRedeEnsino();
        $obj_escola_rede_ensino->setOrderby( "nm_rede ASC" );
        $obj_escola_rede_ensino->setLimite( $this->limite, $this->offset );

        $lista = $obj_escola_rede_ensino->lista(
            null,
            null,
            null,
            $this->nm_rede,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );

        $total = $obj_escola_rede_ensino->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];

                $lista_busca = array(
                    "<a href=\"educar_escola_rede_ensino_det.php?cod_escola_rede_ensino={$registro["cod_escola_rede_ensino"]}\">{$registro["nm_rede"]}</a>"
                );

                if ($nivel_usuario == 1)
                    $lista_busca[] = "<a href=\"educar_escola_rede_ensino_det.php?cod_escola_rede_ensino={$registro["cod_escola_rede_ensino"]}\">{$registro["ref_cod_instituicao"]}</a>";
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2( "educar_escola_rede_ensino_lst.php", $total, $_GET, $this->nome, $this->limite );

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 647, $this->pessoa_logada, 3 ) )
        {
            $this->acao = "go(\"educar_escola_rede_ensino_cad.php\")";
            $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

        $this->breadcrumb('Listagem de redes de ensino', [
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
