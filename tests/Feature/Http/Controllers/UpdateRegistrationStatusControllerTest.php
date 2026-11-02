<?php

use App\Listeners\LoginLegacySession;
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
    $this->schoolClass = $this->scenario->schoolClass();
    $this->registration = $this->scenario->enrolled('Bia Borges', $this->schoolClass)->registration;
    $this->other = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Caio Costa')]);
});

test('a tela de atualização de situação abre', function () {
    $this->get(route('update-registration-status.index'))
        ->assertOk()
        ->assertViewHas('user');
});

test('a atualização exige ano, instituição, situações e, na transferência, motivo e data', function () {
    $this->post(route('update-registration-status.update-status'), ['ano' => 'x', 'nova_situacao' => App_Model_MatriculaSituacao::TRANSFERIDO])
        ->assertSessionHasErrors([
            'ano' => 'O campo Ano deve ser um ano válido.',
            'ref_cod_instituicao' => 'A instituição é obrigatória.',
            'situacao' => 'A situação é obrigatória.',
            'transferencia_tipo' => 'O motivo da transferência é obrigatório.',
            'transferencia_data' => 'A data da transferência é obrigatória.',
        ]);

    $this->post(route('update-registration-status.update-status'), [])
        ->assertSessionHasErrors(['ano' => 'O ano é obrigatório.', 'nova_situacao' => 'A nova situação é obrigatória.']);
});

test('atualiza a situação só das matrículas enturmadas que casam com os filtros', function () {
    $this->post(route('update-registration-status.update-status'), [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_curso' => $this->scenario->course->getKey(),
        'ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_cod_turma' => $this->schoolClass->getKey(),
        'situacao' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'nova_situacao' => App_Model_MatriculaSituacao::APROVADO,
    ])
        ->assertRedirect(route('update-registration-status.index'))
        ->assertSessionHas('success', '1 matrículas atualizadas com sucesso.');

    expect($this->registration->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::APROVADO)
        ->and($this->other->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO);
});
