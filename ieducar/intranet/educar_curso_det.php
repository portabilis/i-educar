<?php

use App\Models\LegacyCourse;
use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;
use App\Models\LegacyRegimeType;

return new class extends clsDetalhe
{
    public $titulo;

    public $cod_curso;

    public $ref_usuario_cad;

    public $ref_cod_tipo_regime;

    public $ref_cod_nivel_ensino;

    public $ref_cod_tipo_ensino;

    public $nm_curso;

    public $sgl_curso;

    public $qtd_etapas;

    public $carga_horaria;

    public $ato_poder_publico;

    public $habilitacao;

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

        $tmp_obj = new clsPmieducarCurso($this->cod_curso);
        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            $this->simpleRedirect('educar_curso_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

        $nm_tipo = LegacyRegimeType::query()
            ->select('nm_tipo')
            ->where(column: 'cod_tipo_regime', operator: $registro['ref_cod_tipo_regime'])
            ->first()?->nm_tipo;
        $registro['ref_cod_tipo_regime'] = $nm_tipo;

        $nm_nivel = LegacyEducationLevel::query()
            ->select('nm_nivel')
            ->where(column: 'cod_nivel_ensino', operator: $registro['ref_cod_nivel_ensino'])
            ->first()?->nm_nivel;
        $registro['ref_cod_nivel_ensino'] = $nm_nivel;

        $nm_tipo = LegacyEducationType::query()
            ->select('nm_tipo')
            ->where(column: 'cod_tipo_ensino', operator: $registro['ref_cod_tipo_ensino'])
            ->first()?->nm_tipo;
        $registro['ref_cod_tipo_ensino'] = $nm_tipo;

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe(['Instituição', $registro['ref_cod_instituicao']]);
            }
        }

        if ($registro['ref_cod_tipo_regime']) {
            $this->addDetalhe(['Tipo Regime', $registro['ref_cod_tipo_regime']]);
        }

        if ($registro['ref_cod_nivel_ensino']) {
            $this->addDetalhe(['Nível Ensino', $registro['ref_cod_nivel_ensino']]);
        }

        if ($registro['ref_cod_tipo_ensino']) {
            $this->addDetalhe(['Tipo Ensino', $registro['ref_cod_tipo_ensino']]);
        }

        if ($registro['nm_curso']) {
            $this->addDetalhe(['Curso', $registro['nm_curso']]);
        }

        if ($registro['sgl_curso']) {
            $this->addDetalhe(['Sigla Curso', $registro['sgl_curso']]);
        }

        if ($registro['qtd_etapas']) {
            $this->addDetalhe(['Quantidade Etapas', $registro['qtd_etapas']]);
        }

        if ($registro['hora_falta']) {
            $registro['hora_falta'] = number_format(num: $registro['hora_falta'], decimals: 2, decimal_separator: ',', thousands_separator: '.');
            $this->addDetalhe(['Hora/Falta', $registro['hora_falta']]);
        }

        if ($registro['carga_horaria']) {
            $registro['carga_horaria'] = number_format(num: $registro['carga_horaria'], decimals: 2, decimal_separator: ',', thousands_separator: '.');
            $this->addDetalhe(['Carga Horária', $registro['carga_horaria']]);
        }

        if ($registro['ato_poder_publico']) {
            $this->addDetalhe(['Ato Poder P&uacute;blico', $registro['ato_poder_publico']]);
        }

        $curso = LegacyCourse::find($this->cod_curso);
        $lst = $curso->qualifications?->toArray();

        if (!empty($lst)) {
            $tabela = '<TABLE>
                 <TR align=center>
                     <TD bgcolor=#ccdce6><B>Nome</B></TD>
                 </TR>';
            $cont = 0;

            foreach ($lst as $valor) {
                if (($cont % 2) == 0) {
                    $color = ' bgcolor=#f5f9fd ';
                } else {
                    $color = ' bgcolor=#FFFFFF ';
                }

                $habilitacao = $valor['nm_tipo'];

                $tabela .= "<TR>
                  <TD {$color} align=left>{$habilitacao}</TD>
              </TR>";

                $cont++;
            }
            $tabela .= '</TABLE>';
        }

        if ($habilitacao) {
            $this->addDetalhe(['Habilitação', $tabela]);
        }

        if ($registro['padrao_ano_escolar']) {
            if ($registro['padrao_ano_escolar'] == 0) {
                $registro['padrao_ano_escolar'] = 'não';
            } elseif ($registro['padrao_ano_escolar'] == 1) {
                $registro['padrao_ano_escolar'] = 'sim';
            }

            $this->addDetalhe(['Padrão Ano Escolar', $registro['padrao_ano_escolar']]);
        }

        if ($registro['objetivo_curso']) {
            $this->addDetalhe(['Objetivo Curso', $registro['objetivo_curso']]);
        }

        if ($registro['publico_alvo']) {
            $this->addDetalhe(['P&uacute;blico Alvo', $registro['publico_alvo']]);
        }

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 566, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->url_novo = 'educar_curso_cad.php';
            $this->url_editar = "educar_curso_cad.php?cod_curso={$registro['cod_curso']}";
        }

        $this->url_cancelar = 'educar_curso_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe do curso', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Curso';
        $this->processoAp = '566';
    }
};
