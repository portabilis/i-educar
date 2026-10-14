<?php

use App\Models\LegacyCourse;
use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionType;
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

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $ref_cod_tipo_dispensa;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $observacao;

    public $ref_sequencial;

    public function Gerar()
    {
        $this->titulo = 'Dispensa Componente Curricular - Detalhe';

        $this->ref_cod_disciplina = $_GET['ref_cod_disciplina'];
        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];
        $this->ref_cod_serie = $_GET['ref_cod_serie'];
        $this->ref_cod_disciplina = $_GET['ref_cod_disciplina'];
        $this->ref_cod_escola = $_GET['ref_cod_escola'];
        $urlListagem = 'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->ref_cod_matricula;

        if (!is_numeric($this->ref_cod_matricula) || !is_numeric($this->ref_cod_serie)
            || !is_numeric($this->ref_cod_escola) || !is_numeric($this->ref_cod_disciplina)) {
            $this->redirectNotFound(
                url: $urlListagem,
                registro: 'dispensa de componente curricular',
                feminino: true,
                dica: 'Confira os registros listados abaixo.',
            );
        }

        $dispensa = LegacyDisciplineExemption::query()
            ->withTrashed()
            ->whereExemptionKey(
                $this->ref_cod_matricula,
                $this->ref_cod_serie,
                $this->ref_cod_escola,
                $this->ref_cod_disciplina
            )
            ->first();

        if (!$dispensa) {
            $this->redirectNotFound(
                url: $urlListagem,
                registro: 'dispensa de componente curricular',
                feminino: true,
                dica: 'Confira os registros listados abaixo.',
            );
        }

        $registro = $dispensa->getAttributes();

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

        // Tipo de dispensa
        $det_ref_cod_tipo_dispensa = LegacyExemptionType::find($registro['ref_cod_tipo_dispensa'])?->getAttributes();
        $registro['ref_cod_tipo_dispensa'] = $det_ref_cod_tipo_dispensa['nm_tipo'];

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

        if ($registro['ref_cod_tipo_dispensa']) {
            $this->addDetalhe(['Tipo Dispensa', $registro['ref_cod_tipo_dispensa']]);
        }

        if ($registro['observacao']) {
            $this->addDetalhe(['Observação', $registro['observacao']]);
        }

        $obj_permissoes = new clsPermissoes;

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 628, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7) && $detalhe_aluno['aprovado'] == App_Model_MatriculaSituacao::EM_ANDAMENTO) {
            $this->url_novo = sprintf(
                'educar_dispensa_disciplina_cad.php?ref_cod_matricula=%d',
                $this->ref_cod_matricula
            );
            $this->url_editar = sprintf(
                'educar_dispensa_disciplina_cad.php?ref_cod_matricula=%d&ref_cod_disciplina=%d',
                $registro['ref_cod_matricula'],
                $registro['ref_cod_disciplina']
            );
        }

        $this->url_cancelar = $urlListagem;
        $this->largura = '100%';

        // Início > Escola > Alunos > Nome > Matrícula > Dispensa de componentes curriculares > Dispensa
        $registration = LegacyRegistration::find($this->ref_cod_matricula);
        $this->breadcrumb(currentPage: 'Dispensa', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
            url('intranet/educar_aluno_lst.php') => 'Alunos',
            url("intranet/educar_aluno_det.php?cod_aluno={$registration?->ref_cod_aluno}") => $registration?->student?->real_name,
            url("intranet/educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}") => 'Matrícula',
            url("intranet/educar_dispensa_disciplina_lst.php?ref_cod_matricula={$this->ref_cod_matricula}") => 'Dispensa de componentes curriculares',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Dispensa Componente Curricular';
        $this->processoAp = 628;
    }
};
