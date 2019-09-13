<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
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

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

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
        $this->titulo = "Curso - Detalhe";


        $this->cod_curso=$_GET["cod_curso"];

        $tmp_obj = new clsPmieducarCurso( $this->cod_curso );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_curso_lst.php');
        }

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];

        $obj_ref_cod_tipo_regime = new clsPmieducarTipoRegime( $registro["ref_cod_tipo_regime"] );
        $det_ref_cod_tipo_regime = $obj_ref_cod_tipo_regime->detalhe();
        $registro["ref_cod_tipo_regime"] = $det_ref_cod_tipo_regime["nm_tipo"];

        $obj_ref_cod_tipo_ensino = new clsPmieducarTipoEnsino( $registro["ref_cod_tipo_ensino"] );
        $det_ref_cod_tipo_ensino = $obj_ref_cod_tipo_ensino->detalhe();
        $registro["ref_cod_tipo_ensino"] = $det_ref_cod_tipo_ensino["nm_tipo"];

        $obj_ref_cod_tipo_avaliacao = new clsPmieducarTipoAvaliacao( $registro["ref_cod_tipo_avaliacao"] );
        $det_ref_cod_tipo_avaliacao = $obj_ref_cod_tipo_avaliacao->detalhe();
        $registro["ref_cod_tipo_avaliacao"] = $det_ref_cod_tipo_avaliacao["nm_tipo"];

        $obj_ref_cod_nivel_ensino = new clsPmieducarNivelEnsino( $registro["ref_cod_nivel_ensino"] );
        $det_ref_cod_nivel_ensino = $obj_ref_cod_nivel_ensino->detalhe();
        $registro["ref_cod_nivel_ensino"] = $det_ref_cod_nivel_ensino["nm_nivel"];

        $obj_ref_usuario_cad = new clsPmieducarUsuario( $registro["ref_usuario_cad"] );
        $det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
        $registro["ref_usuario_cad"] = $det_ref_usuario_cad["data_cadastro"];

        $obj_ref_usuario_exc = new clsPmieducarUsuario( $registro["ref_usuario_exc"] );
        $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
        $registro["ref_usuario_exc"] = $det_ref_usuario_exc["data_cadastro"];

        if( $registro["ref_cod_nivel_ensino"] )
        {
            $this->addDetalhe( array( "Nivel Ensino", "{$registro["ref_cod_nivel_ensino"]}") );
        }
        if( $registro["ref_cod_tipo_ensino"] )
        {
            $this->addDetalhe( array( "Tipo Ensino", "{$registro["ref_cod_tipo_ensino"]}") );
        }
        if( $registro["ref_cod_tipo_avaliacao"] )
        {
            $this->addDetalhe( array( "Tipo Avaliac&atilde;o", "{$registro["ref_cod_tipo_avaliacao"]}") );
        }
        if( $registro["nm_curso"] )
        {
            $this->addDetalhe( array( "Nome Curso", "{$registro["nm_curso"]}") );
        }
        if( $registro["sgl_curso"] )
        {
            $this->addDetalhe( array( "Sgl Curso", "{$registro["sgl_curso"]}") );
        }
        if( $registro["qtd_etapas"] )
        {
            $this->addDetalhe( array( "Qtd Etapas", "{$registro["qtd_etapas"]}") );
        }
        if( $registro["frequencia_minima"] )
        {
            $this->addDetalhe( array( "Frequencia Minima", number_format($registro["frequencia_minima"], 2, ",", ".")) );
        }
        if( $registro["media"] )
        {
            $this->addDetalhe( array( "Media", number_format($registro["media"], 2, ",", ".")) );
        }
        if( $registro["falta_ch_globalizada"] )
        {
            $this->addDetalhe( array( "Falta Ch Globalizada", ($registro["falta_ch_globalizada"] == 1) ? "sim": "n&atilde;o") );
        }
        if( $registro["carga_horaria"] )
        {
            $this->addDetalhe( array( "Carga Horaria", number_format($registro["carga_horaria"], 2, ",", ".")) );
        }
        if( $registro["ato_poder_publico"] )
        {
            $this->addDetalhe( array( "Ato Poder Publico", "{$registro["ato_poder_publico"]}") );
        }
        if( $registro["edicao_final"] )
        {
            $this->addDetalhe( array( "Edic&atilde;o Final", ($registro["edicao_final"] == 1) ? "sim" : "n&atilde;o") );
        }
        if( $registro["objetivo_curso"] )
        {
            $this->addDetalhe( array( "Objetivo Curso", "{$registro["objetivo_curso"]}") );
        }
        if( $registro["publico_alvo"] )
        {
            $this->addDetalhe( array( "Publico Alvo", "{$registro["publico_alvo"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 0, $this->pessoa_logada, 0 ) )
        {
        $this->url_novo = "educar_curso_cad.php";
        $this->url_editar = "educar_curso_cad.php?cod_curso={$registro["cod_curso"]}";
        }

        $this->url_cancelar = "educar_curso_lst.php";
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
