<?php

use App\Listeners\LoginLegacySession;
use App\Models\EnrollmentInep;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
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
    $this->schoolClass = $this->scenario->schoolClass(['etapa_educacenso' => 39]);
    $this->enrollment = $this->scenario->enrolled('Bia Borges', $this->schoolClass);
    $this->inep = fn () => EnrollmentInep::query()->where('matricula_turma_id', $this->enrollment->getKey())->value('matricula_inep');
});

test('a tela do Inep da enturmação diz se a carga horária integralizada pode ser informada', function () {
    $this->get(route('enrollments.enrollment-inep.edit', $this->enrollment))
        ->assertOk()
        ->assertViewHas('podePreencherCargaHoraria', true);

    $this->schoolClass->update(['etapa_educacenso' => 14]);

    $this->get(route('enrollments.enrollment-inep.edit', $this->enrollment))
        ->assertOk()
        ->assertViewHas('podePreencherCargaHoraria', false);
});

test('grava o código Inep, a exclusão do censo e a carga horária só quando a etapa permite', function () {
    $this->post(route('enrollments.enrollment-inep.update', $this->enrollment), ['matricula_inep' => '123456789012', 'desconsiderar_educacenso' => 1, 'carga_horaria_integralizada' => '800'])
        ->assertRedirect(route('enrollments.enrollment-history', $this->enrollment->ref_cod_matricula));

    $this->enrollment->refresh();
    expect((bool) $this->enrollment->desconsiderar_educacenso)->toBeTrue()
        ->and($this->enrollment->carga_horaria_integralizada)->toBe(800)
        ->and(($this->inep)())->toBe(123456789012);

    $this->schoolClass->update(['etapa_educacenso' => 14]);

    $this->post(route('enrollments.enrollment-inep.update', $this->enrollment), ['matricula_inep' => 'abc', 'carga_horaria_integralizada' => '800']);

    $this->enrollment->refresh();
    expect((bool) $this->enrollment->desconsiderar_educacenso)->toBeFalse()
        ->and($this->enrollment->carga_horaria_integralizada)->toBeNull()
        ->and(($this->inep)())->toBeNull();
});
