<?php

use App\Listeners\LoginLegacySession;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacySchoolClassStage;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($this->user->getKey()), false));

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);
    $this->stageType = DB::table('pmieducar.modulo')->where('ref_cod_instituicao', $this->scenario->institution)->value('cod_modulo');

    // Uma turma com calendário próprio, em duas etapas do mesmo tipo da escola.
    $this->scenario->course->update(['padrao_ano_escolar' => 0]);
    $this->schoolClass = $this->scenario->schoolClass();
    foreach ([1, 2] as $sequence) {
        LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $this->schoolClass, 'sequencial' => $sequence, 'ref_cod_modulo' => $this->stageType, 'data_inicio' => '2030-01-01', 'data_fim' => '2030-01-02', 'dias_letivos' => 1]);
    }

    $this->form = fn (array $overrides = []) => $overrides + [
        'tipo' => 'schoolclass',
        'ref_cod_instituicao' => $this->scenario->institution,
        'ano' => 2030,
        'ref_cod_modulo' => $this->stageType,
        'escola' => [[$this->scenario->school->getKey()]],
        'curso' => [[$this->scenario->course->getKey()]],
        'etapas' => [
            1 => ['data_inicio' => '01/02/2030', 'data_fim' => '30/06/2030', 'dias_letivos' => 100],
            2 => ['data_inicio' => '01/08/2030', 'data_fim' => '', 'dias_letivos' => ''],
        ],
    ];
    $this->update = fn (array $overrides = []) => $this->from(route('stage.edit'))->post(route('stage.update'), ($this->form)($overrides));
});

test('a tela de atualização de etapas abre', function () {
    $this->get(route('stage.edit'))
        ->assertOk()
        ->assertViewIs('stage.edit');
});

test('a atualização valida o tipo, as datas e o número de etapas', function () {
    ($this->update)(['tipo' => 'x', 'ano' => 30])
        ->assertSessionHasErrors(['tipo', 'ano']);

    ($this->update)(['etapas' => [1 => ['data_inicio' => '', 'data_fim' => '', 'dias_letivos' => '']]])
        ->assertSessionHasErrors(['etapas' => 'Nenhuma informação a ser atualizada nas etapas']);

    ($this->update)(['etapas' => [1 => ['data_inicio' => '01/02/2031', 'data_fim' => '30/06/2029', 'dias_letivos' => 400]]])
        ->assertSessionHasErrors(['etapas.1.data_inicio' => 'O ano da data de início deve ser 2030.', 'etapas.1.data_fim' => 'O ano da data de término deve ser 2030.', 'etapas.1.dias_letivos' => 'O campo Dias letivos da etapa deve ser no máximo 366.']);

    ($this->update)(['etapas' => [
        0 => ['data_inicio' => '01/02/2030', 'data_fim' => '30/06/2030', 'dias_letivos' => ''],
        1 => ['data_inicio' => '30/06/2030', 'data_fim' => '', 'dias_letivos' => ''],
    ]])
        ->assertSessionHasErrors(['etapas.0.data_fim' => 'A data inicial da etapa 1 deve ser maior que a data final da etapa 0']);

    ($this->update)(['etapas' => [
        1 => ['data_inicio' => '01/02/2030', 'data_fim' => '', 'dias_letivos' => ''],
        2 => ['data_inicio' => '01/05/2030', 'data_fim' => '', 'dias_letivos' => ''],
        3 => ['data_inicio' => '01/09/2030', 'data_fim' => '', 'dias_letivos' => ''],
    ]])
        ->assertSessionHasErrors(['etapas' => 'O número de etapas preenchidas não pode exceder 2 conforme o filtro.']);
});

test('atualiza as etapas das turmas do filtro, mantendo o que veio vazio', function () {
    ($this->update)()
        ->assertRedirect(route('stage.edit'))
        ->assertSessionHas('success', 'Atualização em lote efetuada com sucesso em 1 turmas.');

    $stages = LegacySchoolClassStage::query()->where('ref_cod_turma', $this->schoolClass->getKey())->orderBy('sequencial')->get();

    expect($stages[0]->data_inicio->format('Y-m-d'))->toBe('2030-02-01')
        ->and($stages[0]->data_fim->format('Y-m-d'))->toBe('2030-06-30')
        ->and($stages[0]->dias_letivos)->toBe(100)
        ->and($stages[1]->data_inicio->format('Y-m-d'))->toBe('2030-08-01')
        ->and($stages[1]->data_fim->format('Y-m-d'))->toBe('2030-01-02')
        ->and($stages[1]->dias_letivos)->toBe(1);
});

test('avisa quando nenhuma turma ou escola casa com o filtro', function () {
    ($this->update)(['escola' => [[999999999]]])
        ->assertRedirect(route('stage.edit'))
        ->assertSessionHas('error', 'Nenhuma turma encontrada com os filtros selecionados');

    ($this->update)(['tipo' => 'school', 'escola' => [[999999999]]])
        ->assertRedirect(route('stage.edit'))
        ->assertSessionHas('error', 'Nenhuma escola encontrada com os filtros selecionados');
});

test('atualiza as etapas do ano letivo das escolas do filtro', function () {
    ($this->update)(['tipo' => 'school'])
        ->assertRedirect(route('stage.edit'))
        ->assertSessionHas('success', 'Atualização em lote efetuada com sucesso em 1 escolas.');

    $stages = LegacyAcademicYearStage::query()->where('ref_ref_cod_escola', $this->scenario->school->getKey())->where('ref_ano', 2030)->orderBy('sequencial')->get();

    expect($stages[0]->data_fim->format('Y-m-d'))->toBe('2030-06-30')
        ->and($stages[0]->dias_letivos)->toBe(100)
        ->and($stages[1]->data_inicio->format('Y-m-d'))->toBe('2030-08-01')
        ->and($stages[1]->data_fim->format('Y-m-d'))->toBe('2030-12-15');
});

test('avisa quando a atualização das etapas falha no banco', function () {
    $database = Mockery::mock(app('db'))->makePartial();
    $database->shouldReceive('commit')->andThrow(new RuntimeException('falha ao confirmar'));
    DB::swap($database);

    ($this->update)()
        ->assertRedirect(route('stage.edit'))
        ->assertSessionHas('error', 'Atualização em lote não realizada.');
});
