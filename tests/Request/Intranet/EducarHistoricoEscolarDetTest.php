<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolHistoryDisciplineFactory;
use Database\Factories\LegacySchoolHistoryFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');

    $this->history = fn (array $attributes = []) => LegacySchoolHistoryFactory::new()->create($attributes + [
        'ref_cod_aluno' => $this->student,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => null,
        'sequencial' => 1,
        'ano' => 2028,
        'escola' => 'Escola Antiga',
        'escola_cidade' => 'Içara',
        'escola_uf' => 'SC',
        'nm_curso' => 'Curso Antigo',
        'nm_serie' => 'Série Antiga',
        'carga_horaria' => 800.5,
        'dias_letivos' => 200,
        'frequencia' => 97,
        'faltas_globalizadas' => 12,
        'extra_curricular' => 0,
        'aceleracao' => 1,
        'origem' => 1,
        'observacao' => 'Veio de outra rede',
        'aprovado' => 1,
        'registro' => 'R-1',
        'livro' => 'L-2',
        'folha' => 'F-3',
    ]);

    $this->discipline = fn ($history, int $sequential, string $name, array $attributes = []) => LegacySchoolHistoryDisciplineFactory::new()->create($attributes + [
        'historico_escolar_id' => $history->id,
        'ref_ref_cod_aluno' => $history->ref_cod_aluno,
        'ref_sequencial' => $history->sequencial,
        'sequencial' => $sequential,
        'nm_disciplina' => $name,
        'nota' => '8.5',
        'faltas' => 3,
        'carga_horaria_disciplina' => 80,
    ]);

    $this->keys = fn ($history) => "ref_cod_aluno={$this->student->getKey()}&sequencial={$history->sequencial}";
    $this->url = fn ($history) => '/intranet/educar_historico_escolar_det.php?' . ($this->keys)($history);
    $this->editLink = fn ($history) => 'educar_historico_escolar_cad.php?' . ($this->keys)($history) . '"';
    $this->copyLink = fn ($history) => 'educar_historico_escolar_cad.php?' . ($this->keys)($history) . '&copia=true';

    // Usuário de nível escola numa instituição que restringe o histórico; o
    // aluno tem a última matrícula na escola do cenário.
    $this->schoolUser = function (bool $linked) {
        LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

        $this->scenario->enroll($this->scenario->registration(['ref_cod_aluno' => $this->student]), $this->scenario->schoolClass());

        $school = $linked ? $this->scenario->school : LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution]);

        return LegacyUserFactory::new()->school()->withSchool($school)->withAccess(578)->create(['ref_cod_instituicao' => $this->scenario->institution]);
    };

    $this->restrict = fn () => LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['restringir_historico_escolar' => true]);
});

test('mostra os dados do histórico, as disciplinas com as faltas globalizadas e os botões', function () {
    $history = ($this->history)();
    ($this->discipline)($history, 1, 'Matemática');
    ($this->discipline)($history, 2, 'Artes', ['nota' => '9.0']);

    $this->get(($this->url)($history))
        ->assertOk()
        ->assertSeeInOrder([
            'Aluno', 'Ana Alves',
            'Escola', 'ESCOLA ANTIGA',
            'Cidade da Escola', 'IÇARA',
            'Estado da Escola', 'SC',
            'Série', 'Série Antiga',
            'Curso', 'Curso Antigo',
            'Ano', '2028',
            'Carga Horária', '800,5',
            'Faltas globalizadas', 'Sim',
            'Dias Letivos', '200',
            'Frequência', '97',
            'Extra-Curricular', 'Não',
            'Aceleração', 'Sim',
            'Origem', 'Externo',
            'Observação', 'Veio de outra rede',
            'Situação', 'Aprovado',
            'Registro (arquivo)', 'R-1',
            'Livro', 'L-2',
            'Folha', 'F-3',
            'Disciplina', 'Artes', '9.0', '80', 'Matemática', '8.5', '80',
        ])
        ->assertSee("<td rowspan='2'  bgcolor='#f5f9fd'  align='center'>12</td>", false)
        ->assertSee(($this->editLink)($history), false)
        ->assertSee(($this->copyLink)($history), false)
        ->assertSee('educar_historico_escolar_lst.php?ref_cod_aluno=' . $this->student->getKey(), false);
});

test('sem faltas globalizadas mostra as faltas de cada disciplina', function () {
    $history = ($this->history)(['faltas_globalizadas' => null, 'aceleracao' => 0, 'observacao' => null]);
    ($this->discipline)($history, 1, 'Matemática', ['faltas' => 7]);

    $this->get(($this->url)($history))
        ->assertOk()
        ->assertSeeInOrder(['Faltas globalizadas', 'Não', 'Aceleração', 'Não', 'Disciplina', 'Matemática', '8.5', '7', '80'])
        ->assertDontSee('rowspan', false)
        ->assertDontSee('Observação');
});

test('no histórico extra-curricular os rótulos falam em instituição', function () {
    $history = ($this->history)(['extra_curricular' => 1, 'escola' => 'Instituto de Música']);

    $this->get(($this->url)($history))
        ->assertOk()
        ->assertSeeInOrder([
            'Instituição', 'INSTITUTO DE MÚSICA',
            'Cidade da Instituição', 'IÇARA',
            'Estado da Instituição', 'SC',
            'Série', 'Série Antiga',
            'Extra-Curricular', 'Sim',
        ])
        ->assertDontSee('Cidade da Escola');
});

test('o histórico interno pode ser copiado mas não editado', function () {
    $history = ($this->history)(['origem' => 0]);

    $this->get(($this->url)($history))
        ->assertOk()
        ->assertSeeInOrder(['Origem', 'Interno'])
        ->assertSee(($this->copyLink)($history), false)
        ->assertDontSee(($this->editLink)($history), false);
});

test('com o histórico restrito, o administrador edita e copia', function (bool $withSchool) {
    ($this->restrict)();

    $history = ($this->history)(['ref_cod_escola' => $withSchool ? $this->scenario->school : null]);

    $this->get(($this->url)($history))
        ->assertOk()
        ->assertSee(($this->editLink)($history), false)
        ->assertSee(($this->copyLink)($history), false);
})->with([
    'histórico sem escola' => [false],
    'histórico com escola' => [true],
]);

test('com o histórico restrito e sem escola no histórico, vale a escola da última matrícula do aluno', function (bool $linked) {
    ($this->restrict)();

    $history = ($this->history)();

    $this->actingAs(($this->schoolUser)($linked));

    $response = $this->get(($this->url)($history))->assertOk()->assertDontSee(($this->copyLink)($history), false);

    $linked
        ? $response->assertSee(($this->editLink)($history), false)
        : $response->assertDontSee(($this->editLink)($history), false);
})->with([
    'usuário da escola do aluno' => [true],
    'usuário de outra escola' => [false],
]);

test('com o histórico restrito e com escola no histórico, vale o nome da escola do usuário', function (string $school, bool $allowed) {
    ($this->restrict)();

    $history = ($this->history)(['ref_cod_escola' => $this->scenario->school, 'escola' => $school]);

    $this->actingAs(($this->schoolUser)(true));

    $response = $this->get(($this->url)($history))->assertOk();

    if ($allowed) {
        $response->assertSee(($this->editLink)($history), false)->assertSee(($this->copyLink)($history), false);
    } else {
        $response->assertDontSee(($this->editLink)($history), false)->assertDontSee(($this->copyLink)($history), false);
    }
})->with([
    'histórico da escola do usuário' => ['Escola Aurora', true],
    'histórico de outra escola' => ['Escola Antiga', false],
]);

test('volta para a listagem do aluno quando o histórico não existe', function () {
    $this->get('/intranet/educar_historico_escolar_det.php?ref_cod_aluno=' . $this->student->getKey() . '&sequencial=99')
        ->assertRedirect('educar_historico_escolar_lst.php?ref_cod_aluno=' . $this->student->getKey());
});
