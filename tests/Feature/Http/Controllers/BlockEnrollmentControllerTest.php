<?php

use App\Listeners\LoginLegacySession;
use App\Models\LegacySchoolGrade;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Support\Exceptions\Exception;
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

    $this->update = fn (array $data = []) => $this->post(route('block-enrollment.update'), $data + [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_curso' => $this->scenario->course->getKey(),
    ]);
    $this->blocked = fn () => (bool) LegacySchoolGrade::query()->where('ref_cod_escola', $this->scenario->school->getKey())->where('ref_cod_serie', $this->scenario->grade->getKey())->value('bloquear_enturmacao_sem_vagas');
});

test('a tela de bloqueio em lote abre', function () {
    $this->get(route('block-enrollment.edit'))
        ->assertOk()
        ->assertViewHas('user');
});

test('a atualização exige ano e instituição', function () {
    $this->post(route('block-enrollment.update'), [])
        ->assertSessionHasErrors(['ano', 'ref_cod_instituicao']);
});

test('avisa quando não há séries no filtro, pede confirmação e então bloqueia ou libera', function () {
    ($this->update)(['ano' => 2029])
        ->assertRedirect(route('block-enrollment.edit'))
        ->assertSessionHas('error', 'Nenhuma série da escola encontrada com os filtros selecionados');

    ($this->update)(['bloquear_enturmacao_sem_vagas' => 1])
        ->assertRedirect(route('block-enrollment.edit'))
        ->assertSessionHas('show-confirmation', 1);

    expect(($this->blocked)())->toBeFalse();

    ($this->update)(['bloquear_enturmacao_sem_vagas' => 1, 'confirmation' => 1])
        ->assertRedirect(route('block-enrollment.edit'))
        ->assertSessionHas('success', 'Atualização em lote efetuada com sucesso.');

    expect(($this->blocked)())->toBeTrue();

    ($this->update)(['confirmation' => 1]);

    expect(($this->blocked)())->toBeFalse();
});

test('avisa quando a atualização em lote não é confirmada no banco', function () {
    // O controller só captura as exceções do i-Educar.
    $database = Mockery::mock(app('db'))->makePartial();
    $database->shouldReceive('commit')->andThrow(new Exception('falha ao confirmar'));
    DB::swap($database);

    ($this->update)(['bloquear_enturmacao_sem_vagas' => 1, 'confirmation' => 1])
        ->assertRedirect(route('block-enrollment.edit'))
        ->assertSessionHas('error', 'Atualização em lote não realizada.');

    expect(($this->blocked)())->toBeFalse();
});
