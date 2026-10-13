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
    $this->login = function ($user) {
        $this->actingAs($user);
        app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($user->getKey()), false));
    };
    $this->user = LegacyUserFactory::new()->admin()->create();
    ($this->login)($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);

    // Sem a configuração geral ativa, quem não é administrador cai na tela de
    // sistema suspenso.
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);
    $this->alfa = $this->scenario->schoolClass(['nm_turma' => 'Alfa', 'tipo_boletim' => 1, 'tipo_boletim_diferenciado' => null]);
    $this->beta = $this->scenario->schoolClass(['nm_turma' => 'Beta', 'tipo_boletim' => 2, 'tipo_boletim_diferenciado' => null]);

    $this->update = fn (array $data) => $this->post(route('update-school-class-report-card.update-date'), $data + [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
    ]);
});

test('a tela de atualização em lote abre para administradores', function () {
    $this->get(route('update-school-class-report-card.index'))
        ->assertOk()
        ->assertSee('Atualização de boletins em lote');
});

test('usuários de escola não acessam a tela', function () {
    ($this->login)(LegacyUserFactory::new()->school()->create());

    $this->from(route('update-school-class-report-card.index'))
        ->get(route('update-school-class-report-card.index'))
        ->assertRedirect(route('update-school-class-report-card.index'))
        ->assertSessionHasErrors(['Error' => 'Você não tem permissão para acessar este recurso']);
});

test('a atualização exige ano, instituição e um novo tipo de boletim', function () {
    $this->post(route('update-school-class-report-card.update-date'), ['ano' => 'abc'])
        ->assertSessionHasErrors([
            'ano' => 'O campo Ano deve ser um ano válido.',
            'ref_cod_instituicao' => 'A instituição é obrigatória.',
            'new_report_card' => 'O novo tipo de boletim é obrigatório.',
            'new_alternative_report_card' => 'O novo tipo de boletim diferenciado é obrigatório.',
        ]);
});

test('avisa quando os filtros não encontram turmas', function () {
    ($this->update)(['new_report_card' => 3, 'ref_cod_escola' => $this->scenario->school->getKey(), 'ref_cod_curso' => $this->scenario->course->getKey(), 'ref_cod_serie' => [$this->scenario->grade->getKey()], 'old_report_card' => 9])
        ->assertRedirect(route('update-school-class-report-card.index'))
        ->assertSessionHas('error', 'Nenhuma turma encontrada com os filtros selecionados');
});

test('sem confirmação, pede para confirmar informando quantas turmas serão alteradas', function () {
    ($this->update)(['new_report_card' => 3])
        ->assertRedirect(route('update-school-class-report-card.index'))
        ->assertSessionHas('show-confirmation', ['count' => 2])
        ->assertSessionHasInput('new_report_card', '3');

    expect($this->alfa->refresh()->tipo_boletim)->toBe(1);
});

test('com confirmação, atualiza os boletins das turmas filtradas e lista o que mudou', function () {
    ($this->update)(['new_report_card' => 3, 'new_alternative_report_card' => 4, 'old_report_card' => 1, 'confirmation' => 1])
        ->assertRedirect(route('update-school-class-report-card.index'))
        ->assertSessionHas('success', '1 turmas atualizadas com sucesso.')
        ->assertSessionHas('classrooms', [[
            'id' => $this->alfa->getKey(),
            'name' => 'Alfa (2030)',
            'old_report' => 1,
            'new_report' => '3',
            'old_alternative_report' => null,
            'new_alternative_report' => '4',
        ]]);

    expect($this->alfa->refresh()->tipo_boletim)->toBe(3)
        ->and($this->alfa->tipo_boletim_diferenciado)->toBe(4)
        ->and($this->beta->refresh()->tipo_boletim)->toBe(2);
});
