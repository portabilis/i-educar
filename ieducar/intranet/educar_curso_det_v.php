<?php

use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;
use App\Models\LegacyRegimeType;

return new class extends clsDetalhe {
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

        $this->cod_curso = $_GET['cod_curso'];

        $tmp_obj = new clsPmieducarCurso(cod_curso: $this->cod_curso);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect(url: 'educar_curso_lst.php');
        }

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        $nm_tipo = LegacyRegimeType::query()
            ->select(columns: 'nm_tipo')
            ->where(column: 'cod_tipo_regime', operator: $registro['ref_cod_tipo_regime'])
            ->first()?->nm_tipo;
        $registro['ref_cod_tipo_regime'] = $nm_tipo;

        $nm_tipo = LegacyEducationType::query()
            ->select(columns: 'nm_tipo')
            ->where(column: 'cod_tipo_ensino', operator: $registro['ref_cod_tipo_ensino'])
            ->first()?->nm_tipo;
        $registro['ref_cod_tipo_ensino'] = $nm_tipo;

        $obj_ref_cod_tipo_avaliacao = new clsPmieducarTipoAvaliacao(cod_tipo_avaliacao: $registro['ref_cod_tipo_avaliacao']);
        $det_ref_cod_tipo_avaliacao = $obj_ref_cod_tipo_avaliacao->detalhe();
        $registro['ref_cod_tipo_avaliacao'] = $det_ref_cod_tipo_avaliacao['nm_tipo'];

        $nm_nivel = LegacyEducationLevel::query()
            ->select(columns: 'nm_nivel')
            ->where(column: 'cod_nivel_ensino', operator: $registro['ref_cod_nivel_ensino'])
            ->first()?->nm_nivel;
        $registro['ref_cod_nivel_ensino'] = $nm_nivel;

        $obj_ref_usuario_cad = new clsPmieducarUsuario(cod_usuario: $registro['ref_usuario_cad']);
        $det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
        $registro['ref_usuario_cad'] = $det_ref_usuario_cad['data_cadastro'];

        $obj_ref_usuario_exc = new clsPmieducarUsuario(cod_usuario: $registro['ref_usuario_exc']);
        $det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
        $registro['ref_usuario_exc'] = $det_ref_usuario_exc['data_cadastro'];

        if ($registro['ref_cod_nivel_ensino']) {
            $this->addDetalhe(detalhe: ['Nivel Ensino', "{$registro['ref_cod_nivel_ensino']}"]);
        }
        if ($registro['ref_cod_tipo_ensino']) {
            $this->addDetalhe(detalhe: ['Tipo Ensino', "{$registro['ref_cod_tipo_ensino']}"]);
        }
        if ($registro['ref_cod_tipo_avaliacao']) {
            $this->addDetalhe(detalhe: ['Tipo Avaliacão', "{$registro['ref_cod_tipo_avaliacao']}"]);
        }
        if ($registro['nm_curso']) {
            $this->addDetalhe(detalhe: ['Nome Curso', "{$registro['nm_curso']}"]);
        }
        if ($registro['sgl_curso']) {
            $this->addDetalhe(detalhe: ['Sgl Curso', "{$registro['sgl_curso']}"]);
        }
        if ($registro['qtd_etapas']) {
            $this->addDetalhe(detalhe: ['Qtd Etapas', "{$registro['qtd_etapas']}"]);
        }
        if ($registro['frequencia_minima']) {
            $this->addDetalhe(detalhe: ['Frequencia Minima', number_format(num: $registro['frequencia_minima'], decimals: 2, decimal_separator: ',', thousands_separator: '.')]);
        }
        if ($registro['media']) {
            $this->addDetalhe(detalhe: ['Media', number_format(num: $registro['media'], decimals: 2, decimal_separator: ',', thousands_separator: '.')]);
        }
        if ($registro['falta_ch_globalizada']) {
            $this->addDetalhe(detalhe: ['Falta Ch Globalizada', ($registro['falta_ch_globalizada'] == 1) ? 'sim' : 'não']);
        }
        if ($registro['carga_horaria']) {
            $this->addDetalhe(detalhe: ['Carga Horaria', number_format(num: $registro['carga_horaria'], decimals: 2, decimal_separator: ',', thousands_separator: '.')]);
        }
        if ($registro['ato_poder_publico']) {
            $this->addDetalhe(detalhe: ['Ato Poder Publico', "{$registro['ato_poder_publico']}"]);
        }
        if ($registro['edicao_final']) {
            $this->addDetalhe(detalhe: ['Edicão Final', ($registro['edicao_final'] == 1) ? 'sim' : 'não']);
        }
        if ($registro['objetivo_curso']) {
            $this->addDetalhe(detalhe: ['Objetivo Curso', "{$registro['objetivo_curso']}"]);
        }
        if ($registro['publico_alvo']) {
            $this->addDetalhe(detalhe: ['Publico Alvo', "{$registro['publico_alvo']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 0, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 0)) {
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
