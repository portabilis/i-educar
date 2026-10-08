<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');

    $this->registration = fn (array $attributes = []) => $this->scenario->registration($attributes + ['ref_cod_aluno' => $this->student]);
    $this->detail = fn ($registration) => 'educar_matricula_det.php?cod_matricula=' . $registration->getKey();

    $this->list = fn (array $query = []) => $this->get('/intranet/educar_matricula_lst.php?' . http_build_query($query + [
        'ref_cod_aluno' => $this->student->getKey(),
    ]))->assertOk();

    // Um usuário institucional (nível 2), que vê a coluna de escola mas não a de instituição.
    $this->institutional = function () {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

        $this->actingAs(
            LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
        );
    };
});

test('lista as matrículas do aluno com situação, turmas, série, curso, escola e instituição', function () {
    $registration = ($this->registration)();

    $this->scenario->enroll($registration, $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']));
    $this->scenario->enroll($registration, $this->scenario->schoolClass(['nm_turma' => 'Turma Beta']), ['sequencial' => 2]);

    ($this->list)()
        ->assertSee(($this->detail)($registration), false)
        ->assertSeeInOrder([
            '2030', (string) $registration->getKey(), 'Cursando', 'Turma Alfa<br />Turma Beta',
            'Primeiro Ano', 'Ensino Fundamental', 'Escola Aurora', $this->user->institution->nm_instituicao,
        ], false)
        ->assertSee('go("educar_matricula_cad.php?ref_cod_aluno=' . $this->student->getKey() . '")', false)
        ->assertSee('educar_aluno_det.php?cod_aluno=' . $this->student->getKey(), false);
});

test('ordena do ano mais recente para o mais antigo', function () {
    $old = ($this->registration)(['ano' => 2029]);
    $new = ($this->registration)(['ano' => 2030]);

    ($this->list)()->assertSeeInOrder([($this->detail)($new), ($this->detail)($old)], false);
});

test('mostra a matrícula sem enturmação com a coluna de turma vazia e traços para nomes em branco', function () {
    $registration = ($this->registration)(['ref_ref_cod_serie' => $this->scenario->grade('', ['descricao' => null])]);

    $this->scenario->school->organization->update(['fantasia' => '']);

    $html = ($this->list)()->getContent();

    expect(substr_count($html, '<a href="' . ($this->detail)($registration) . '">-</a>'))->toBe(2)
        ->and($html)->not->toContain('Turma Alfa');
});

test('não lista matrícula inativa nem a que não é a última', function () {
    $inactive = ($this->registration)(['ativo' => 0]);
    $notLast = ($this->registration)(['ultima_matricula' => 0]);

    ($this->list)()
        ->assertDontSee(($this->detail)($inactive), false)
        ->assertDontSee(($this->detail)($notLast), false)
        ->assertSee('Aluno sem matrículas em andamento na sua escola.');
});

test('filtra pela escola', function () {
    $mine = ($this->registration)();
    $other = EnrollmentScenario::for($this->user, 2030)->registration(['ref_cod_aluno' => $this->student]);

    ($this->list)(['ref_cod_instituicao' => $this->scenario->institution, 'ref_cod_escola' => $this->scenario->school->getKey()])
        ->assertSee(($this->detail)($mine), false)
        ->assertDontSee(($this->detail)($other), false);
});

test('ao usuário institucional mostra a escola e não a instituição', function () {
    $registration = ($this->registration)();

    ($this->institutional)();

    ($this->list)()
        ->assertSee('<a href="' . ($this->detail)($registration) . '">Escola Aurora</a>', false)
        ->assertDontSee('<a href="' . ($this->detail)($registration) . '">' . $this->user->institution->nm_instituicao . '</a>', false)
        // Sem permissão de cadastro não há botão de nova matrícula.
        ->assertDontSee('educar_matricula_cad.php', false);
});

test('ao usuário institucional mostra um traço quando a escola não tem nome', function () {
    $registration = ($this->registration)();

    $this->scenario->school->organization->update(['fantasia' => '']);

    ($this->institutional)();

    ($this->list)()->assertSee('<a href="' . ($this->detail)($registration) . '">-</a>', false);
});

test('volta para a listagem de alunos quando o aluno não é informado', function () {
    $this->get('/intranet/educar_matricula_lst.php')->assertRedirect('educar_aluno_lst.php');
});
