<?php

use App\Models\LegacyDisciplineDependence;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
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

    $this->list = fn (array $query = []) => $this->get('/intranet/educar_disciplina_dependencia_lst.php?' . http_build_query($query + [
        'ref_cod_matricula' => $this->registration->getKey(),
    ]));
});

test('lista as disciplinas de dependência da matrícula', function () {
    ($this->dependence)($this->arts);

    ($this->list)()
        ->assertOk()
        ->assertSee('educar_disciplina_dependencia_det.php?' . ($this->keys)($this->arts), false)
        ->assertSee('Artes Visuais')
        ->assertDontSee('educar_disciplina_dependencia_det.php?' . ($this->keys)($this->botany), false)
        ->assertSee('educar_disciplina_dependencia_cad.php?ref_cod_matricula=' . $this->registration->getKey(), false);
});

test('filtra pelo componente', function () {
    ($this->dependence)($this->arts);
    ($this->dependence)($this->botany);

    ($this->list)(['ref_cod_disciplina' => $this->botany->getKey()])
        ->assertOk()
        ->assertSee('educar_disciplina_dependencia_det.php?' . ($this->keys)($this->botany), false)
        ->assertDontSee('educar_disciplina_dependencia_det.php?' . ($this->keys)($this->arts), false);
});

test('não oferece o botão Novo a quem só pode visualizar matrículas', function () {
    ($this->dependence)($this->arts);

    ($this->as)(false, 682);

    ($this->list)()->assertOk()->assertSee('Artes Visuais')->assertDontSee('educar_disciplina_dependencia_cad.php', false);
});

test('volta para a listagem de matrículas quando a matrícula não é informada', function () {
    $this->get('/intranet/educar_disciplina_dependencia_lst.php')->assertRedirect('educar_matricula_lst.php');
});

test('manda para o início quem não pode ver as dependências ou é de biblioteca', function (Closure $user) {
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs($user->call($this));

    ($this->list)()->assertRedirect('/intranet/index.php');
})->with([
    'sem acesso ao processo 682' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)->create($this->institution)],
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(578)->withAccess(682)->create($this->institution + [
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
