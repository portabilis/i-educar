<?php

use App\Models\LegacyDisciplineDependence;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->institution = ['ref_cod_instituicao' => $this->scenario->institution];
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Ana Alves'), 'dependencia' => true]);
    $this->scenario->enroll($this->registration, $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']));

    $this->arts = $this->scenario->discipline('Artes Visuais');
    $this->botany = $this->scenario->discipline('Botânica');

    $this->dependence = fn ($discipline, ?string $note = 'Reprovou por nota') => LegacyDisciplineDependence::query()->create([
        'cod_disciplina_dependencia' => (LegacyDisciplineDependence::query()->max('cod_disciplina_dependencia') ?? 0) + 1,
        'ref_cod_matricula' => $this->registration->getKey(),
        'ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_disciplina' => $discipline->getKey(),
        'observacao' => $note,
    ]);

    $this->keys = fn ($discipline) => http_build_query([
        'ref_cod_matricula' => $this->registration->getKey(),
        'ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_disciplina' => $discipline->getKey(),
    ]);

    // Um usuário institucional com os acessos dados.
    $this->as = function (bool $modify, int ...$processes) {
        LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

        $factory = LegacyUserFactory::new()->institutional()->withAccess(578, modify: $modify, remove: $modify);

        foreach ($processes as $process) {
            $factory = $factory->withAccess($process);
        }

        $this->actingAs($factory->create($this->institution));
    };

    $this->detail = fn ($discipline) => $this->get('/intranet/educar_disciplina_dependencia_det.php?' . ($this->keys)($discipline));
    $this->list = 'educar_disciplina_dependencia_lst.php?ref_cod_matricula=' . $this->registration->getKey();
});

test('mostra o aluno, o curso, a série, o componente e a observação', function () {
    ($this->dependence)($this->arts);

    ($this->detail)($this->arts)
        ->assertOk()
        ->assertSeeInOrder([
            'Aluno', 'Ana Alves',
            'Curso', 'Ensino Fundamental',
            'Série', 'Primeiro Ano',
            'Componente Curricular', 'Artes Visuais',
            'Observação', 'Reprovou por nota',
        ])
        ->assertSee("educar_disciplina_dependencia_cad.php?ref_cod_matricula={$this->registration->getKey()}&ref_cod_disciplina={$this->arts->getKey()}", false);
});

test('sem observação não mostra a linha', function () {
    ($this->dependence)($this->arts, null);

    ($this->detail)($this->arts)->assertOk()->assertSee('Artes Visuais')->assertDontSee('Observação');
});

test('não oferece editar a quem só pode visualizar matrículas', function () {
    ($this->dependence)($this->arts);

    ($this->as)(false, 682);

    ($this->detail)($this->arts)->assertOk()->assertSee('Artes Visuais')->assertDontSee('educar_disciplina_dependencia_cad.php', false);
});

test('volta para a listagem de dependências quando a disciplina não é dependência da matrícula', function () {
    ($this->detail)($this->botany)->assertRedirect($this->list);
});

test('volta para a listagem de dependências quando a matrícula não existe', function () {
    $this->get('/intranet/educar_disciplina_dependencia_det.php?' . http_build_query([
        'ref_cod_matricula' => 999999999,
        'ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_disciplina' => $this->arts->getKey(),
    ]))->assertRedirect('educar_disciplina_dependencia_lst.php?ref_cod_matricula=999999999');
});
