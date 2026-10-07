<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolHistoryFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');
    LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['nm_instituicao' => 'Rede Aurora']);

    $this->history = fn (int $sequential, array $attributes = []) => LegacySchoolHistoryFactory::new()->create($attributes + [
        'ref_cod_aluno' => $this->student,
        'ref_cod_instituicao' => $this->scenario->institution,
        'sequencial' => $sequential,
        'ano' => 2028,
        'escola' => 'Escola Antiga',
        'nm_curso' => 'Curso Antigo',
        'nm_serie' => 'Série Antiga',
        'registro' => 'R-1',
        'livro' => 'L-2',
        'folha' => 'F-3',
        'extra_curricular' => 0,
    ]);

    $this->detail = fn ($history) => "educar_historico_escolar_det.php?ref_cod_aluno={$this->student->getKey()}&sequencial={$history->sequencial}";
    $this->new = 'go("educar_historico_escolar_cad.php?ref_cod_aluno=' . $this->student->getKey() . '")';
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_historico_escolar_lst.php?' . http_build_query($query + ['ref_cod_aluno' => $this->student->getKey()]))->assertOk();

    // Usuário de nível escola numa instituição que restringe o histórico.
    $this->schoolUser = function (bool $linked) {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);
        LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['restringir_historico_escolar' => true]);

        $this->scenario->enroll($this->scenario->registration(['ref_cod_aluno' => $this->student]), $this->scenario->schoolClass());

        $school = $linked ? $this->scenario->school : LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution]);

        return LegacyUserFactory::new()->school()->withSchool($school)->withAccess(578)->create(['ref_cod_instituicao' => $this->scenario->institution]);
    };
});

test('lista os históricos do aluno por ano e sequencial, com as colunas e os botões', function () {
    $third = ($this->history)(3, ['ano' => 2029, 'extra_curricular' => 1, 'escola' => 'Instituto de Música']);
    $second = ($this->history)(2);
    $first = ($this->history)(1, ['ano' => 2027]);

    ($this->list)()
        ->assertSeeInOrder([
            '2027', 'Não', 'ESCOLA ANTIGA', 'Rede Aurora', 'Curso Antigo', 'Série Antiga', 'R-1', 'L-2', 'F-3',
            '2028', 'Não',
            '2029', 'Sim', 'INSTITUTO DE MÚSICA',
        ])
        ->assertSee(($this->detail)($first), false)
        ->assertSee(($this->detail)($second), false)
        ->assertSee(($this->detail)($third), false)
        ->assertSee($this->new, false)
        ->assertSee('educar_aluno_det.php?cod_aluno=' . $this->student->getKey(), false);
});

test('filtra pelo ano, pela instituição e por ser ou não extra-curricular', function (array $query, int $shown) {
    $histories = [
        1 => ($this->history)(1),
        2 => ($this->history)(2, ['ano' => 2029, 'extra_curricular' => 1]),
        3 => ($this->history)(3, ['ano' => 2030, 'ref_cod_instituicao' => LegacyInstitutionFactory::new()->create()]),
    ];

    if (array_key_exists('ref_cod_instituicao', $query)) {
        $query['ref_cod_instituicao'] = $histories[3]->ref_cod_instituicao;
    }

    $response = ($this->list)($query);

    foreach ($histories as $number => $history) {
        $number === $shown
            ? $response->assertSee(($this->detail)($history), false)
            : $response->assertDontSee(($this->detail)($history), false);
    }
})->with([
    'ano' => [['ano' => 2028], 1],
    'extra-curricular' => [['extra_curricular' => 1], 2],
    'não extra-curricular, no ano' => [['extra_curricular' => 2, 'ano' => 2028], 1],
    'instituição' => [['ref_cod_instituicao' => null], 3],
]);

test('não lista histórico inativo nem o de outro aluno', function () {
    $inactive = ($this->history)(1, ['ativo' => 0]);
    $other = ($this->history)(2, ['ref_cod_aluno' => $this->scenario->student('Bia Borges')]);

    ($this->list)()
        ->assertDontSee(($this->detail)($inactive), false)
        ->assertDontSee("ref_cod_aluno={$other->ref_cod_aluno}&sequencial=2", false);
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $sequential) {
        ($this->history)($sequential, ['registro' => sprintf('Registro %02d', $sequential)]);
    }

    ($this->list)()->assertSee('Registro 20')->assertDontSee('Registro 21');
    ($this->list)(['pagina_formulario' => 2])->assertSee('Registro 21')->assertDontSee('Registro 20');
});

test('com o histórico restrito, o usuário de escola só cadastra para aluno da sua escola', function (bool $linked) {
    $this->actingAs(($this->schoolUser)($linked));

    $response = ($this->list)();

    $linked ? $response->assertSee($this->new, false) : $response->assertDontSee($this->new, false);
})->with([
    'usuário da escola do aluno' => [true],
    'usuário de outra escola' => [false],
]);

test('sem a restrição de histórico, o usuário de outra escola cadastra', function () {
    $user = ($this->schoolUser)(false);
    LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['restringir_historico_escolar' => false]);

    $this->actingAs($user);

    ($this->list)()->assertSee($this->new, false);
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    ($this->list)()->assertDontSee($this->new, false);
});

test('volta para a listagem de alunos quando o aluno não é informado', function () {
    $this->get('/intranet/educar_historico_escolar_lst.php')->assertRedirect('educar_aluno_lst.php');
});
