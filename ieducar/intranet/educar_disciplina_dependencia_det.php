<?php

use App\Models\LegacyCourse;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyDisciplineDependence;
use App\Models\LegacyGrade;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudent;

return new class extends clsDetalhe
{
    public $titulo;

    public $ref_cod_matricula;

    public $ref_cod_turma;

    public $ref_cod_serie;

    public $ref_cod_escola;

    public $ref_cod_disciplina;

    public $observacao;

    public function Gerar()
    {
        $this->titulo = 'Disciplina de dependência - Detalhe';

        $this->ref_cod_disciplina = $_GET['ref_cod_disciplina'];
        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];
        $this->ref_cod_serie = $_GET['ref_cod_serie'];
        $this->ref_cod_disciplina = $_GET['ref_cod_disciplina'];
        $this->ref_cod_escola = $_GET['ref_cod_escola'];

        $registro = null;
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_serie) &&
            is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_disciplina) &&
            $this->dependenciaValida()
        ) {
            $registro = LegacyDisciplineDependence::query()
                ->whereRegistration($this->ref_cod_matricula)
                ->whereGrade($this->ref_cod_serie)
                ->whereSchool($this->ref_cod_escola)
                ->whereDiscipline($this->ref_cod_disciplina)
                ->first(['cod_disciplina_dependencia', 'ref_cod_matricula', 'ref_cod_serie', 'ref_cod_escola', 'ref_cod_disciplina', 'observacao']);
        }

        if (!$registro) {
            $this->redirectNotFound(
                url: 'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula,
                registro: 'disciplina de dependência',
                feminino: true,
                dica: 'Confira os registros listados abaixo.',
            );
        }

        $registro['ref_ref_cod_serie'] = LegacyGrade::query()->whereKey($this->ref_cod_serie)->value('nm_serie');

        // Dados da matrícula
        $obj_ref_cod_matricula = new clsPmieducarMatricula;
        $matricula = $obj_ref_cod_matricula->lista($this->ref_cod_matricula);
        $detalhe_aluno = array_shift($matricula);

        $obj_escola = new clsPmieducarEscola(
            cod_escola: $this->ref_cod_escola,
            bloquear_lancamento_diario_anos_letivos_encerrados: 1
        );
        $obj_escola->detalhe();

        $nm_aluno = LegacyStudent::query()
            ->active()
            ->whereStudent($detalhe_aluno['ref_cod_aluno'])
            ->joinPerson()
            ->value('pessoa.nome');

        // Dados do curso
        $registro['ref_cod_curso'] = LegacyCourse::query()
            ->whereKey($detalhe_aluno['ref_cod_curso'])
            ->value('nm_curso');

        if ($registro['ref_cod_matricula']) {
            $this->addDetalhe(['Matricula', $registro['ref_cod_matricula']]);
        }

        if ($nm_aluno) {
            $this->addDetalhe(['Aluno', $nm_aluno]);
        }

        if ($registro['ref_cod_curso']) {
            $this->addDetalhe(['Curso', $registro['ref_cod_curso']]);
        }

        if ($registro['ref_ref_cod_serie']) {
            $this->addDetalhe(['Série', $registro['ref_ref_cod_serie']]);
        }

        if ($registro['ref_cod_disciplina']) {
            $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper;
            $componente = $componenteMapper->find($registro['ref_cod_disciplina']);
            $this->addDetalhe(['Componente Curricular', $componente->nome]);
        }

        if ($registro['observacao']) {
            $this->addDetalhe(['Observação', $registro['observacao']]);
        }

        $obj_permissoes = new clsPermissoes;

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = sprintf(
                'educar_disciplina_dependencia_cad.php?ref_cod_matricula=%d',
                $this->ref_cod_matricula
            );
            $this->url_editar = sprintf(
                'educar_disciplina_dependencia_cad.php?ref_cod_matricula=%d&ref_cod_disciplina=%d',
                $registro['ref_cod_matricula'],
                $registro['ref_cod_disciplina']
            );
        }

        $this->url_cancelar = 'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;
        $this->largura = '100%';

        // Início > Escola > Alunos > Nome > Matrícula > Disciplinas de dependência > Disciplina de dependência
        $registration = LegacyRegistration::find($this->ref_cod_matricula);
        $this->breadcrumb(currentPage: 'Disciplina de dependência', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
            url('intranet/educar_aluno_lst.php') => 'Alunos',
            url("intranet/educar_aluno_det.php?cod_aluno={$registration?->ref_cod_aluno}") => $registration?->student?->real_name,
            url("intranet/educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}") => 'Matrícula',
            url("intranet/educar_disciplina_dependencia_lst.php?ref_cod_matricula={$this->ref_cod_matricula}") => 'Disciplinas de dependência',
        ]);
    }

    /**
     * Uma dependência só é válida quando a matrícula existe e o componente
     * curricular está vinculado ao ano/série.
     */
    private function dependenciaValida(): bool
    {
        if (!LegacyRegistration::query()->whereKey($this->ref_cod_matricula)->exists()) {
            return false;
        }

        return LegacyDisciplineAcademicYear::query()
            ->whereGrade($this->ref_cod_serie)
            ->whereDiscipline($this->ref_cod_disciplina)
            ->count() === 1;
    }

    public function Formular()
    {
        $this->title = 'Disciplina dependência';
        $this->processoAp = 578;
    }
};
