<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);

    $this->detail = fn ($schoolClass) => 'educar_turma_det.php?cod_turma=' . $schoolClass->getKey();

    $this->list = fn (array $query = []) => $this->get('/intranet/educar_turma_lst.php?' . http_build_query($query + [
        'busca' => 'S',
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
    ]))->assertOk();
});

test('lista as turmas do ano com turno, série, curso, escola e situação', function () {
    $schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa', 'turma_turno_id' => LegacyPeriodFactory::new()->morning()]);

    ($this->list)()
        ->assertSee(($this->detail)($schoolClass), false)
        ->assertSeeInOrder(['2030', 'Turma Alfa', 'Matutino', 'Primeiro Ano', 'Ensino Fundamental', 'Escola Aurora', 'Ativo'])
        ->assertSee('go("educar_turma_cad.php")', false);
});

test('sem busca, lista as turmas do ano corrente', function () {
    $other = $this->scenario->schoolClass(['nm_turma' => 'Turma de 2030']);
    $current = $this->scenario->schoolClass(['nm_turma' => 'Turma do ano', 'ano' => now()->year]);

    $this->get('/intranet/educar_turma_lst.php')
        ->assertOk()
        ->assertSee(($this->detail)($current), false)
        ->assertDontSee(($this->detail)($other), false);
});

test('mostra turno em branco e situação inativa quando a turma não tem turno e não está visível', function () {
    $schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa', 'turma_turno_id' => null, 'visivel' => false]);

    ($this->list)()
        ->assertSee('<a href="' . ($this->detail)($schoolClass) . '"></a>', false)
        ->assertSee('<a href="' . ($this->detail)($schoolClass) . '">Inativo</a>', false);
});

test('mostra um traço quando a série ou a escola não têm nome', function () {
    $schoolClass = $this->scenario->schoolClass([
        'nm_turma' => 'Turma Alfa',
        'ref_ref_cod_serie' => $this->scenario->grade('', ['descricao' => null]),
    ]);

    $this->scenario->school->person->update(['nome' => '']);

    $link = '<a href="' . ($this->detail)($schoolClass) . '">-</a>';

    expect(substr_count(($this->list)()->getContent(), $link))->toBe(2);
});

test('na turma multisseriada mostra todas as séries e cursos', function () {
    $second = $this->scenario->grade('Segundo Ano');

    $schoolClass = LegacySchoolClassFactory::new()->inGrades([$this->scenario->grade, $second])->create([
        'nm_turma' => 'Turma Multi',
        'ref_ref_cod_escola' => $this->scenario->school,
        'ref_ref_cod_serie' => $this->scenario->grade,
        'ref_cod_curso' => $this->scenario->course,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ano' => 2030,
    ]);

    ($this->list)()
        ->assertSee(($this->detail)($schoolClass), false)
        ->assertSee('Primeiro Ano<br>Segundo Ano', false);
});

test('filtra as turmas', function (Closure $query) {
    $wanted = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa', 'turma_turno_id' => LegacyPeriodFactory::new()->morning()]);

    $otherGrade = $this->scenario->grade('Segundo Ano');
    $other = $this->scenario->schoolClass([
        'nm_turma' => 'Turma Beta',
        'ref_ref_cod_serie' => $otherGrade,
        'turma_turno_id' => LegacyPeriodFactory::new()->afternoon(),
        'visivel' => false,
    ]);

    ($this->list)($query->call($this, $wanted))
        ->assertSee(($this->detail)($wanted), false)
        ->assertDontSee(($this->detail)($other), false);
})->with([
    'nome' => [fn () => ['nm_turma' => 'alfa']],
    'turno' => [fn ($schoolClass) => ['turma_turno_id' => $schoolClass->turma_turno_id]],
    'situação ativa' => [fn () => ['visivel' => 1]],
    'série' => [fn ($schoolClass) => ['ref_cod_curso' => $schoolClass->ref_cod_curso, 'ref_cod_serie' => $schoolClass->ref_ref_cod_serie]],
    // Os campos ocultos da tela de edição têm precedência sobre os filtros.
    'escola e série dos campos ocultos' => [fn ($schoolClass) => ['ref_cod_escola_' => $schoolClass->ref_ref_cod_escola, 'ref_cod_serie_' => $schoolClass->ref_ref_cod_serie]],
]);

test('filtra as turmas inativas', function () {
    $active = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);
    $inactive = $this->scenario->schoolClass(['nm_turma' => 'Turma Beta', 'visivel' => false]);

    ($this->list)(['visivel' => 2])
        ->assertSee(($this->detail)($inactive), false)
        ->assertDontSee(($this->detail)($active), false);
});

test('filtra pela escola', function () {
    $mine = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);
    $other = EnrollmentScenario::for($this->user, 2030)->schoolClass(['nm_turma' => 'Turma Beta']);

    ($this->list)(['ref_cod_escola' => $this->scenario->school->getKey()])
        ->assertSee(($this->detail)($mine), false)
        ->assertDontSee(($this->detail)($other), false);
});

test('com o curso escolhido, oferece as séries ativas dele no filtro', function () {
    LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->scenario->course, 'nm_serie' => 'Série Extinta', 'ativo' => 0]);

    $html = ($this->list)(['ref_cod_curso' => $this->scenario->course->getKey()])->getContent();

    expect($html)->toContain("value=\"{$this->scenario->grade->getKey()}\">Primeiro Ano")
        ->and($html)->not->toContain('Série Extinta');
});

test('não lista turma excluída', function () {
    $deleted = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa', 'ativo' => 0]);

    ($this->list)()->assertDontSee(($this->detail)($deleted), false);
});

test('usuário de nível escola só vê as turmas das escolas a que está vinculado', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $mine = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);
    $other = EnrollmentScenario::for($this->user, 2030)->schoolClass(['nm_turma' => 'Turma Beta']);

    $this->actingAs(
        LegacyUserFactory::new()->school()->withSchool($this->scenario->school)->withAccess(586, modify: false, remove: false)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    ($this->list)()
        ->assertSee(($this->detail)($mine), false)
        ->assertDontSee(($this->detail)($other), false)
        ->assertDontSee('go("educar_turma_cad.php")', false);
});
