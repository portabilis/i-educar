<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Calendario Dia" );
        $this->processoAp = "621";
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

    var $ref_cod_calendario_ano_letivo;
    var $mes;
    var $dia;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_cod_calendario_dia_motivo;
    var $ref_cod_calendario_atividade;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_escola;
    var $ref_cod_instituicao;

    function Gerar()
    {
        $this->titulo = "Calendario Dia - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;




        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if(!$this->ref_cod_escola)
            $this->ref_cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);
        if(!$this->ref_cod_instituicao)
            $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);


        $this->addCabecalhos( array(
            "Calendario Ano Letivo",
            "Dia",
            "Mes",

            "Calendario Dia Motivo"
        ) );

        $get_escola     = 1;
        $obrigatorio    = true;
        include("include/pmieducar/educar_campo_lista.php");

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_calendario_dia = new clsPmieducarCalendarioDia();
        $obj_calendario_dia->setOrderby( "descricao ASC" );
        $obj_calendario_dia->setLimite( $this->limite, $this->offset );

        $lista = $obj_calendario_dia->lista(
            $this->ref_cod_calendario_ano_letivo,
            $this->mes,
            $this->dia,
            null,
            null,
            $this->ref_cod_calendario_dia_motivo,
            $this->ref_cod_calendario_atividade,
            $this->descricao_ini,
            $this->descricao_fim,
            null,
            null,
            1,
            $this->ref_cod_escola
        );

        $total = $obj_calendario_dia->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $obj_ref_cod_calendario_dia_motivo = new clsPmieducarCalendarioDiaMotivo( $registro["ref_cod_calendario_dia_motivo"] );
                $det_ref_cod_calendario_dia_motivo = $obj_ref_cod_calendario_dia_motivo->detalhe();
                $registro["ref_cod_calendario_dia_motivo"] = $det_ref_cod_calendario_dia_motivo["nm_motivo"];

                $obj_ref_cod_calendario_ano_letivo = new clsPmieducarCalendarioAnoLetivo( $registro["ref_cod_calendario_ano_letivo"] );
                $det_ref_cod_calendario_ano_letivo = $obj_ref_cod_calendario_ano_letivo->detalhe();
                $registro["ano"] = $det_ref_cod_calendario_ano_letivo["ano"];

                $this->addLinhas( array(
                    "<a href=\"educar_calendario_dia_cad.php?ref_cod_calendario_ano_letivo={$registro["ref_cod_calendario_ano_letivo"]}&ano={$registro["ano"]}&mes={$registro["mes"]}&dia={$registro["dia"]}\">{$registro["ano"]}</a>",
                    "<a href=\"educar_calendario_dia_cad.php?ref_cod_calendario_ano_letivo={$registro["ref_cod_calendario_ano_letivo"]}&ano={$registro["ano"]}&mes={$registro["mes"]}&dia={$registro["dia"]}\">{$registro["dia"]}</a>",
                    "<a href=\"educar_calendario_dia_cad.php?ref_cod_calendario_ano_letivo={$registro["ref_cod_calendario_ano_letivo"]}&ano={$registro["ano"]}&mes={$registro["mes"]}&dia={$registro["dia"]}\">{$registro["mes"]}</a>",
                    "<a href=\"educar_calendario_dia_cad.php?ref_cod_calendario_ano_letivo={$registro["ref_cod_calendario_ano_letivo"]}&ano={$registro["ano"]}&mes={$registro["mes"]}&dia={$registro["dia"]}\">{$registro["ref_cod_calendario_dia_motivo"]}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_calendario_dia_lst.php", $total, $_GET, $this->nome, $this->limite );
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 0, $this->pessoa_logada, 0 ) )
        {
        $this->acao = "go(\"educar_calendario_dia_cad.php\")";
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


