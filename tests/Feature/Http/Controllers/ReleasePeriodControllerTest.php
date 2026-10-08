<?php

use App\Listeners\LoginLegacySession;
use App\Models\ReleasePeriod;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\ReleasePeriodDateFactory;
use Database\Factories\ReleasePeriodFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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

    $this->form = fn (array $overrides = []) => $overrides + [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
        'escola' => [[$this->scenario->school->getKey()]],
        'stage_type' => $this->stageType,
        'stage' => 1,
        'start_date' => ['01/02/2030', '01/03/2030'],
        'end_date' => ['10/02/2030', '10/03/2030'],
    ];

    $this->period = fn (array $attributes = []) => tap(ReleasePeriodFactory::new()->create($attributes + ['year' => 2030, 'stage' => 1, 'stage_type_id' => $this->stageType]), function ($period) {
        $period->schools()->attach($this->scenario->school->getKey());
        ReleasePeriodDateFactory::new()->create(['release_period_id' => $period, 'start_date' => '2030-02-01', 'end_date' => '2030-02-10']);
    });
});

test('a listagem filtra por ano, escola e etapa e informa as permissões', function () {
    $mine = ($this->period)();
    $other = ReleasePeriodFactory::new()->create(['year' => 2029, 'stage' => 2]);

    $this->get(route('release-period.index', ['ano' => 2030, 'ref_cod_escola' => $this->scenario->school->getKey(), 'stage' => 1]))
        ->assertOk()
        ->assertViewHas('data', fn ($data) => $data->pluck('id')->all() === [$mine->getKey()])
        ->assertViewHas('canModify', true)
        ->assertSessionHas('notice');

    $this->get(route('release-period.index'))
        ->assertOk()
        ->assertViewHas('data', fn ($data) => $data->pluck('id')->contains($other->getKey()));
});

test('o formulário abre vazio e preenchido com o período', function () {
    $this->get(route('release-period.form'))
        ->assertOk()
        ->assertViewHas('releasePeriod', fn ($period) => !$period->exists);

    $period = ($this->period)();

    $this->get(route('release-period.form', $period))
        ->assertOk()
        ->assertViewHas('releasePeriod', fn ($given) => $given->is($period));

    $this->get(route('release-period.show', $period))
        ->assertOk()
        ->assertViewHas('releasePeriod', fn ($given) => $given->is($period));
});

test('o cadastro valida os campos e as datas', function () {
    $this->post(route('release-period.create'), ['ano' => 'x', 'start_date' => ['01/02/2030', '01/02/2030'], 'end_date' => ['2030-02-10', '']])
        ->assertSessionHasErrors([
            'ano' => 'O campo Ano deve ser um ano válido.',
            'ref_cod_instituicao' => 'A instituição é obrigatória.',
            'escola' => 'A escola é obrigatória.',
            'stage_type' => 'O tipo de etapa é obrigatório.',
            'stage' => 'A etapa é obrigatória.',
            'start_date.0' => 'A data 01/02/2030 já foi usada',
            'end_date.0' => 'A data 2030-02-10 é inválida',
            'end_date.1' => 'Informe pelo menos uma data fim',
        ]);
});

test('cadastra o período com as escolas e as datas', function () {
    $this->post(route('release-period.create'), ($this->form)())
        ->assertRedirect(route('release-period.index'))
        ->assertSessionHas('success', 'Período cadastrado com sucesso.');

    $period = ReleasePeriod::query()->latest('id')->first();

    expect($period->year)->toBe(2030)
        ->and($period->stage)->toBe(1)
        ->and($period->schools->pluck('cod_escola')->all())->toBe([$this->scenario->school->getKey()])
        ->and($period->getDatesArray())->toBe(['01/02/2030 a 10/02/2030', '01/03/2030 a 10/03/2030']);
});

test('atualiza o período trocando as escolas e as datas', function () {
    $period = ($this->period)();
    $other = $this->scenario->grade('Segundo Ano');

    $this->post(route('release-period.update', $period), ($this->form)(['stage' => 2, 'start_date' => ['05/05/2030'], 'end_date' => ['15/05/2030']]))
        ->assertRedirect(route('release-period.index'))
        ->assertSessionHas('success', 'Período atualizado com sucesso.');

    $period->refresh();

    expect($period->stage)->toBe(2)
        ->and($period->getDatesArray())->toBe(['05/05/2030 a 15/05/2030'])
        ->and($period->schools()->count())->toBe(1);
});

test('exclui os períodos selecionados', function () {
    $first = ($this->period)();
    $second = ($this->period)();

    $this->get(route('release-period.delete', ['periods' => [$first->getKey(), $second->getKey()]]))
        ->assertRedirect(route('release-period.index'))
        ->assertSessionHas('success', 'Período(s) excluído(s) com sucesso.');

    expect(ReleasePeriod::query()->whereKey([$first->getKey(), $second->getKey()])->count())->toBe(0);
});

test('quando a confirmação da transação falha, desfaz e avisa no cadastro, na edição e na exclusão', function () {
    $period = ($this->period)();
    $database = Mockery::mock(app('db'))->makePartial();
    $database->shouldReceive('commit')->andThrow(new RuntimeException('falha ao confirmar'));
    DB::swap($database);

    $this->post(route('release-period.create'), ($this->form)(['stage' => 2]))
        ->assertRedirect(route('release-period.index'))
        ->assertSessionHas('error', 'Não foi possível cadastrar o período.');

    $this->post(route('release-period.update', $period), ($this->form)(['stage' => 3]))
        ->assertRedirect(route('release-period.index'))
        ->assertSessionHas('error', 'Não foi possível atualizar o período.');

    $this->get(route('release-period.delete', ['periods' => [$period->getKey()]]))
        ->assertRedirect(route('release-period.index'))
        ->assertSessionHas('error', 'Não foi possível excluir o período.');

    expect($period->refresh()->stage)->toBe(1)
        ->and(ReleasePeriod::query()->where('stage', 2)->exists())->toBeFalse();
});
