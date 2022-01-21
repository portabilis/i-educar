<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_curso;
    public $ref_usuario_cad;
    public $ref_cod_tipo_regime;
    public $ref_cod_nivel_ensino;
    public $ref_cod_tipo_ensino;
    public $ref_cod_tipo_avaliacao;
    public $nm_curso;
    public $sgl_curso;
    public $qtd_etapas;
    public $frequencia_minima;
    public $media;
    public $media_exame;
    public $falta_ch_globalizada;
    public $carga_horaria;
    public $ato_poder_publico;
    public $edicao_final;
    public $objetivo_curso;
    public $publico_alvo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_usuario_exc;
    public $ref_cod_instituicao;
    public $padrao_ano_escolar;
    public $hora_falta;

    public function Gerar()
    {
        $this->titulo = 'Curso - Detalhe';

        $this->cod_curso=$_GET['cod_curso'];

        $tmp_obj = new clsPmieducarCurso($this->cod_curso);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_curso_lst.php');
        }

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        $obj_ref_cod_tipo_regime = new clsPmieducarTipoRegime($registro['ref_cod_tipo_regime']);
        $det_ref_cod_tipo_regime = $obj_ref_cod_tipo_regime->detalhe();
        $registro['ref_cod_tipo_regime'] = $det_ref_cod_tipo_regime['nm_tipo'];

        $obj_ref_cod_tipo_ensino = new clsPmieducarTipoEnsino($registro['ref_cod_tipo_ensino']);
        $det_ref_cod_tipo_ensino = $obj_ref_cod_tipo_ensino->detalhe();
        $registro['ref_cod_tipo_ensino'] = $det_ref_cod_tipo_ensino['nm_tipo'];

        $obj_ref_cod_tipo_avaliacao = new clsPmieducarTipoAvaliacao($registro['ref_cod_tipo_avaliacao']);
        $det_ref_cod_tipo_avaliacao = $obj_ref_cod_tipo_avaliacao->detalhe();
        $registro['ref_cod_tipo_avaliacao'] = $det_ref_cod_tipo_avaliacao['nm_tipo'];

        $obj_ref_cod_nivel_ensino = new clsPmieducarNivelEnsino($registro['ref_cod_nivel_ensino']);
        $det_ref_cod_nivel_ensino = $obj_ref_cod_nivel_ensino->detalhe();
        $registro['ref_cod_nivel_ensino'] = $det_ref_cod_nivel_ensino['nm_nivel'];

        $obj_ref_usuario_cad = new clsPmieducarUsuario($registro['ref_usuario_cad']);
        $det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
        $registro['ref_usuario_cad'] = $det_ref_usuario_cad['data_cadastro'];

        $obj_ref_usuario_exc = new clsPmieducarUsuario($registro['ref_usuario_exc']);
        $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
        $registro['ref_usuario_exc'] = $det_ref_usuario_exc['data_cadastro'];

        if ($registro['ref_cod_nivel_ensino']) {
            $this->addDetalhe([ 'Nivel Ensino', "{$registro['ref_cod_nivel_ensino']}"]);
        }
        if ($registro['ref_cod_tipo_ensino']) {
            $this->addDetalhe([ 'Tipo Ensino', "{$registro['ref_cod_tipo_ensino']}"]);
        }
        if ($registro['ref_cod_tipo_avaliacao']) {
            $this->addDetalhe([ 'Tipo Avaliacão', "{$registro['ref_cod_tipo_avaliacao']}"]);
        }
        if ($registro['nm_curso']) {
            $this->addDetalhe([ 'Nome Curso', "{$registro['nm_curso']}"]);
        }
        if ($registro['sgl_curso']) {
            $this->addDetalhe([ 'Sgl Curso', "{$registro['sgl_curso']}"]);
        }
        if ($registro['qtd_etapas']) {
            $this->addDetalhe([ 'Qtd Etapas', "{$registro['qtd_etapas']}"]);
        }
        if ($registro['frequencia_minima']) {
            $this->addDetalhe([ 'Frequencia Minima', number_format($registro['frequencia_minima'], 2, ',', '.')]);
        }
        if ($registro['media']) {
            $this->addDetalhe([ 'Media', number_format($registro['media'], 2, ',', '.')]);
        }
        if ($registro['falta_ch_globalizada']) {
            $this->addDetalhe([ 'Falta Ch Globalizada', ($registro['falta_ch_globalizada'] == 1) ? 'sim': 'não']);
        }
        if ($registro['carga_horaria']) {
            $this->addDetalhe([ 'Carga Horaria', number_format($registro['carga_horaria'], 2, ',', '.')]);
        }
        if ($registro['ato_poder_publico']) {
            $this->addDetalhe([ 'Ato Poder Publico', "{$registro['ato_poder_publico']}"]);
        }
        if ($registro['edicao_final']) {
            $this->addDetalhe([ 'Edicão Final', ($registro['edicao_final'] == 1) ? 'sim' : 'não']);
        }
        if ($registro['objetivo_curso']) {
            $this->addDetalhe([ 'Objetivo Curso', "{$registro['objetivo_curso']}"]);
        }
        if ($registro['publico_alvo']) {
            $this->addDetalhe([ 'Publico Alvo', "{$registro['publico_alvo']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(0, $this->pessoa_logada, 0)) {
            $this->url_novo = 'educar_curso_cad.php';
            $this->url_editar = "educar_curso_cad.php?cod_curso={$registro['cod_curso']}";
        }

        $this->url_cancelar = 'educar_curso_lst.php';
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Curso';
        $this->processoAp = '0';
    }
};
