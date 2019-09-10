<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Curso" );
        $this->processoAp = "0";
    }
}

class indice extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $__pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $__titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $__limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $__offset;

    var $cod_curso;
    var $ref_usuario_cad;
    var $ref_cod_tipo_regime;
    var $ref_cod_nivel_ensino;
    var $ref_cod_tipo_ensino;
    var $ref_cod_tipo_avaliacao;
    var $nm_curso;
    var $sgl_curso;
    var $qtd_etapas;
    var $frequencia_minima;
    var $media;
    var $media_exame;
    var $falta_ch_globalizada;
    var $carga_horaria;
    var $ato_poder_publico;
    var $edicao_final;
    var $objetivo_curso;
    var $publico_alvo;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_usuario_exc;
    var $ref_cod_instituicao;
    var $padrao_ano_escolar;
    var $hora_falta;

    function Gerar()
    {
        $this->__pessoa_logada = $this->pessoa_logada;

        $this->__titulo = "Curso - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Curso",
            "Nivel Ensino",
            "Tipo Ensino",
            "Instituic&atilde;o"
        ) );

        $this->campoTexto( "nm_curso", "Curso", $this->nm_curso, 30, 255, false );

        $opcoes = array( "" => "Selecione" );

        $objTemp = new clsPmieducarNivelEnsino();
        $lista = $objTemp->lista();
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $opcoes["{$registro['cod_nivel_ensino']}"] = "{$registro['nm_nivel']}";
            }
        }

        $this->campoLista( "ref_cod_nivel_ensino", "Nivel Ensino", $opcoes, $this->ref_cod_nivel_ensino );

        $opcoes = array( "" => "Selecione" );

        $objTemp = new clsPmieducarTipoEnsino();
        $lista = $objTemp->lista();
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $opcoes["{$registro['cod_tipo_ensino']}"] = "{$registro['nm_ensino']}";
            }
        }

        $this->campoLista( "ref_cod_tipo_ensino", "Tipo Ensino", $opcoes, $this->ref_cod_tipo_ensino );


        // Paginador
        $this->__limite = 20;
        $this->__offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->__limite-$this->__limite: 0;

        $obj_curso = new clsPmieducarCurso();
        $obj_curso->setOrderby( "nm_curso ASC" );
        $obj_curso->setLimite( $this->__limite, $this->__offset );

        $lista = $obj_curso->lista(
            null,
            null,
            $this->ref_cod_nivel_ensino,
            $this->ref_cod_tipo_ensino,
            null,
            $this->nm_curso,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
            null
        );

        $total = $obj_curso->_total;

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

                $obj_ref_cod_nivel_ensino = new clsPmieducarNivelEnsino( $registro["ref_cod_nivel_ensino"] );
                $det_ref_cod_nivel_ensino = $obj_ref_cod_nivel_ensino->detalhe();
                $registro["ref_cod_nivel_ensino"] = $det_ref_cod_nivel_ensino["nm_nivel"];

                $obj_ref_cod_tipo_ensino = new clsPmieducarTipoEnsino( $registro["ref_cod_tipo_ensino"] );
                $det_ref_cod_tipo_ensino = $obj_ref_cod_tipo_ensino->detalhe();
                $registro["ref_cod_tipo_ensino"] = $det_ref_cod_tipo_ensino["nm_tipo"];

                $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
                $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                $registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];

                $this->addLinhas( array(
                    "<a href=\"educar_curso_det.php?cod_curso={$registro["cod_curso"]}\">{$registro["nm_curso"]}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro["cod_curso"]}\">{$registro["ref_cod_nivel_ensino"]}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro["cod_curso"]}\">{$registro["ref_cod_tipo_ensino"]}</a>",
                    "<a href=\"educar_curso_det.php?cod_curso={$registro["cod_curso"]}\">{$registro["ref_cod_instituicao"]}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_curso_lst.php", $total, $_GET, $this->nome, $this->__limite );
        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 0, $this->pessoa_logada, 0 ) )
        {
        $this->acao = "go(\"educar_curso_cad.php\")";
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
