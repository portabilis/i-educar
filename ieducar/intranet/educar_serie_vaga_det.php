<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsDetalhe {
    public $titulo;

    public $cod_serie_vaga;

    public function Gerar()
    {
        $this->titulo = 'Vagas por série - Detalhe';

        $this->cod_serie_vaga = $_GET['cod_serie_vaga'];

        $tmp_obj = new clsPmieducarSerieVaga($this->cod_serie_vaga);

        $registro = $tmp_obj->detalhe();

        if (!$registro) {
            throw new HttpResponseException(
                new RedirectResponse('educar_serie_vaga_lst.php')
            );
        }

        $obj_serie = new clsPmieducarSerie($registro['ref_cod_serie']);
        $det_serie = $obj_serie->detalhe();
        $registro['ref_ref_cod_serie'] = $det_serie['nm_serie'];

        // Dados do curso
        $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
        $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
        $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];

        // Dados da escola
        $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $registro['ref_cod_escola'] = $det_ref_cod_escola['nome'];

        if ($registro['ano']) {
            $this->addDetalhe(['Ano', $registro['ano']]);
        }

        if ($registro['ref_cod_escola']) {
            $this->addDetalhe(['Escola', $registro['ref_cod_escola']]);
        }

        if ($registro['ref_cod_curso']) {
            $this->addDetalhe(['Curso', $registro['ref_cod_curso']]);
        }

        if ($registro['ref_ref_cod_serie']) {
            $this->addDetalhe(['Série', $registro['ref_ref_cod_serie']]);
        }

        if ($registro['turno']) {
            $turnos = [
        0 => 'Selecione',
        1 => 'Matutino',
        2 => 'Vespertino',
        3 => 'Noturno',
        4 => 'Integral'
      ];
            $this->addDetalhe(['Turno', $turnos[$registro['turno']]]);
        }

        if ($registro['vagas']) {
            $this->addDetalhe(['Vagas', $registro['vagas']]);
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(21253, $this->pessoa_logada, 7)) {
            $this->url_novo   = 'educar_serie_vaga_cad.php';
            $this->url_editar = sprintf('educar_serie_vaga_cad.php?cod_serie_vaga=%d', $this->cod_serie_vaga);
        }

        $this->url_cancelar = 'educar_serie_vaga_lst.php';
        $this->largura      = '100%';

        $this->breadcrumb('Detalhe de vagas da série', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
    }

    public function Formular()
    {
        $this->title = 'Vagas por série';
        $this->processoAp = 21253;
    }
};
