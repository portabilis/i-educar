<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);

    $this->link = fn ($schoolClass) => route('enrollments.batch.enroll.index', ['schoolClass' => $schoolClass->getKey()]);

    $this->list = fn (array $query = []) => $this->get('/intranet/educar_matriculas_turma_lst.php?' . http_build_query($query + [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
    ]))->assertOk();
});

test('lista as turmas do ano com série, curso e escola, cada uma levando à enturmação em lote', function () {
    $schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);

    ($this->list)()
        ->assertSee('<a href="' . ($this->link)($schoolClass) . '">2030</a>', false)
        ->assertSeeInOrder(['Turma Alfa', 'Primeiro Ano', 'Ensino Fundamental', 'Escola Aurora']);
});

test('sem ano informado, lista as turmas do ano corrente', function () {
    $other = $this->scenario->schoolClass(['nm_turma' => 'Turma de 2030']);
    $current = $this->scenario->schoolClass(['nm_turma' => 'Turma do ano', 'ano' => now()->year]);

    $this->get('/intranet/educar_matriculas_turma_lst.php?ref_cod_instituicao=' . $this->scenario->institution)
        ->assertOk()
        ->assertSee(($this->link)($current), false)
        ->assertDontSee(($this->link)($other), false);
});

test('filtra pela escola, pelo curso, pela série e pela turma', function (Closure $query) {
    $wanted = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);
    $other = EnrollmentScenario::for($this->user, 2030)->schoolClass(['nm_turma' => 'Turma Beta']);

    ($this->list)($query->call($this, $wanted))
        ->assertSee(($this->link)($wanted), false)
        ->assertDontSee(($this->link)($other), false);
})->with([
    'escola' => [fn ($schoolClass) => ['ref_cod_escola' => $schoolClass->ref_ref_cod_escola]],
    'curso' => [fn ($schoolClass) => ['ref_cod_curso' => $schoolClass->ref_cod_curso]],
    'série' => [fn ($schoolClass) => ['ref_cod_serie' => $schoolClass->ref_ref_cod_serie]],
    'turma' => [fn ($schoolClass) => ['ref_cod_turma' => $schoolClass->getKey()]],
]);

test('mostra um traço para a turma sem série ou sem escola', function () {
    $schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Avulsa', 'ref_ref_cod_serie' => null, 'ref_ref_cod_escola' => null]);

    expect(substr_count(($this->list)()->getContent(), '<a href="' . ($this->link)($schoolClass) . '">-</a>'))->toBe(2);
});

test('pagina de vinte em vinte', function () {
    foreach (range(1, 21) as $number) {
        $this->scenario->schoolClass(['nm_turma' => sprintf('Turma %02d', $number)]);
    }

    ($this->list)()->assertSee('Turma 20')->assertDontSee('Turma 21');
    ($this->list)(['pagina_formulario' => 2])->assertSee('Turma 21')->assertDontSee('Turma 20');
});

test('usuário de nível escola só vê as turmas das escolas a que está vinculado', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $mine = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);
    $other = EnrollmentScenario::for($this->user, 2030)->schoolClass(['nm_turma' => 'Turma Beta']);

    $this->actingAs(
        LegacyUserFactory::new()->school()->withSchool($this->scenario->school)->withAccess(659)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    ($this->list)()
        ->assertSee(($this->link)($mine), false)
        ->assertDontSee(($this->link)($other), false);
});
