<?php

use App\Services\SchoolGradeDisciplineService;

return new class extends clsDetalhe {
    public $ref_cod_escola;
    public $ref_cod_serie;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $hora_inicial;
    public $hora_final;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $hora_inicio_intervalo;
    public $hora_fim_intervalo;

    public function Gerar()
    {
        $this->titulo = 'Escola Série - Detalhe';

        $this->ref_cod_serie = $_GET['ref_cod_serie'];
        $this->ref_cod_escola = $_GET['ref_cod_escola'];

        $tmp_obj = new clsPmieducarEscolaSerie();
        $lst_obj = $tmp_obj->lista($this->ref_cod_escola, $this->ref_cod_serie);
        $registro = array_shift($lst_obj);

        if (!$registro) {
            $this->simpleRedirect('educar_escola_serie_lst.php');
        }

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $nm_escola = $det_ref_cod_escola['nome'];

        $obj_ref_cod_serie = new clsPmieducarSerie($registro['ref_cod_serie']);
        $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
        $nm_serie = $det_ref_cod_serie['nm_serie'];

        $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
        $det_curso = $obj_curso->detalhe();
        $registro['ref_cod_curso'] = $det_curso['nm_curso'];

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

        if ($registro['ref_cod_instituicao']) {
            $this->addDetalhe(['Instituição', $registro['ref_cod_instituicao']]);
        }

        if ($nm_escola) {
            $this->addDetalhe(['Escola', $nm_escola]);
        }

        if ($registro['ref_cod_curso']) {
            $this->addDetalhe(['Curso', $registro['ref_cod_curso']]);
        }

        if ($nm_serie) {
            $this->addDetalhe(['S&eacute;rie', $nm_serie]);
        }

        if ($registro['hora_inicial']) {
            $registro['hora_inicial'] = date('H:i', strtotime($registro['hora_inicial']));
            $this->addDetalhe(['Hora Inicial', $registro['hora_inicial']]);
        }

        if ($registro['hora_final']) {
            $registro['hora_final'] = date('H:i', strtotime($registro['hora_final']));
            $this->addDetalhe(['Hora Final', $registro['hora_final']]);
        }

        if ($registro['hora_inicio_intervalo']) {
            $registro['hora_inicio_intervalo'] = date('H:i', strtotime($registro['hora_inicio_intervalo']));
            $this->addDetalhe(['Hora In&iacute;cio Intervalo', $registro['hora_inicio_intervalo']]);
        }

        if ($registro['hora_fim_intervalo']) {
            $registro['hora_fim_intervalo'] = date('H:i', strtotime($registro['hora_fim_intervalo']));
            $this->addDetalhe(['Hora Fim Intervalo', $registro['hora_fim_intervalo']]);
        }

        // Componentes da escola-série
        $componentes = [];
        try {
            $componentes = App_Model_IedFinder::getEscolaSerieDisciplina($this->ref_cod_serie, $this->ref_cod_escola);
        } catch (Exception $e) {
        }

        /** @var SchoolGradeDisciplineService $service */
        $service = app(SchoolGradeDisciplineService::class);

        $disciplines = $service->getAllDisciplines($this->ref_cod_escola, $this->ref_cod_serie)
            ->pluck('carga_horaria', 'ref_cod_disciplina');

        if (0 < count($componentes)) {
            $tabela = '
<table>
  <tr align="center">
    <td bgcolor="#ccdce6"><b>Nome</b></td>
    <td bgcolor="#ccdce6"><b>Carga horária</b></td>
  </tr>';

            $cont = 0;

            foreach ($componentes as $componente) {
                if (($cont % 2) == 0) {
                    $color = ' bgcolor="#f5f9fd" ';
                } else {
                    $color = ' bgcolor="#FFFFFF" ';
                }

                $tabela .= sprintf(
                    '
          <tr>
            <td %s align="left">%s</td>
            <td %s align="center">%.0f h</td>
          </tr>',
                    $color,
                    $componente,
                    $color,
                    $disciplines[intval($componente->id)] ?? $componente->cargaHoraria
                );

                $cont++;
            }

            $tabela .= '</table>';
        }

        if (isset($tabela)) {
            $this->addDetalhe(['Componentes curriculares', $tabela]);
        }

        if ($obj_permissao->permissao_cadastra(585, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_escola_serie_cad.php';
            $this->url_editar = "educar_escola_serie_cad.php?ref_cod_escola={$registro['ref_cod_escola']}&ref_cod_serie={$registro['ref_cod_serie']}";
        }

        $this->url_cancelar = 'educar_escola_serie_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe dos vínculos entre escola e série', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Escola S&eacute;rie';
        $this->processoAp = '585';
    }
};
