<?php

use App\Jobs\UpdateRegistrationDateJob;
use App\Listeners\LoginLegacySession;
use App\Models\LegacyRegistration;
use App\Models\Notification;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Bus\Batch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
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
    $this->enrollment = $this->scenario->enrolled('Bia Borges', $this->schoolClass, ['data_enturmacao' => '2030-02-05'], ['data_matricula' => '2030-02-01']);
    $this->registration = $this->enrollment->registration;

    $this->update = fn (array $data = []) => $this->post(route('update-registration-date.update-date'), $data + [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
        'nova_data_entrada' => '10/02/2030',
    ]);
    $this->filters = fn () => [
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_curso' => $this->scenario->course->getKey(),
        'ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_cod_turma' => $this->schoolClass->getKey(),
        'data_entrada_antiga' => '01/02/2030',
        'data_enturmacao_antiga' => '05/02/2030',
        'situacao' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
    ];
});

test('a tela de atualização de datas abre', function () {
    $this->get(route('update-registration-date.index'))
        ->assertOk()
        ->assertViewHas('user');
});

test('a atualização exige ano, instituição e a nova data', function () {
    $this->post(route('update-registration-date.update-date'), ['ano' => 'abc', 'nova_data_entrada' => '2030-02-10'])
        ->assertSessionHasErrors([
            'ano' => 'O campo Ano deve ser um ano válido.',
            'ref_cod_instituicao' => 'A instituição é obrigatória.',
            'nova_data_entrada' => 'A nova data é inválida.',
        ]);
});

test('avisa quando os filtros não encontram matrículas e pede confirmação antes de atualizar', function () {
    ($this->update)(['data_entrada_antiga' => '15/05/2030'])
        ->assertRedirect(route('update-registration-date.index'))
        ->assertSessionHas('error', 'Nenhuma matrícula encontrada com os filtros selecionados');

    ($this->update)(($this->filters)())
        ->assertRedirect(route('update-registration-date.index'))
        ->assertSessionHas('show-confirmation', ['count' => 1]);

    expect($this->registration->refresh()->data_matricula->format('Y-m-d'))->toBe('2030-02-01');
});

test('atualiza a data de entrada e, se pedida, a de enturmação das matrículas filtradas', function () {
    ($this->update)(($this->filters)() + ['confirmation' => 1, 'nova_data_enturmacao' => '12/02/2030', 'remanejadas' => 1])
        ->assertRedirect(route('update-registration-date.index'))
        ->assertSessionHas('success', '1 matrículas atualizadas com sucesso.')
        ->assertSessionHas('registrations');

    expect($this->registration->refresh()->data_matricula->format('Y-m-d'))->toBe('2030-02-10')
        ->and($this->enrollment->refresh()->data_enturmacao->format('Y-m-d'))->toBe('2030-02-12');
});

test('acima de quinhentas matrículas, a atualização vai para a fila em lotes', function () {
    Bus::fake();
    $student = $this->registration->ref_cod_aluno;
    $rows = [];
    foreach (range(1, 500) as $i) {
        $rows[] = ['ref_cod_aluno' => $student, 'ref_ref_cod_escola' => $this->scenario->school->getKey(), 'ref_ref_cod_serie' => $this->scenario->grade->getKey(), 'ref_cod_curso' => $this->scenario->course->getKey(), 'ano' => 2030, 'ativo' => 1, 'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO, 'data_matricula' => '2030-02-01', 'ref_usuario_cad' => $this->user->getKey(), 'data_cadastro' => now()];
    }
    LegacyRegistration::query()->insert($rows);

    ($this->update)(['confirmation' => 1, 'data_entrada_antiga' => '01/02/2030'])
        ->assertRedirect(route('update-registration-date.index'))
        ->assertSessionHas('success', 'Serão atualizadas 501 matrículas. Você será notificado no final do processo');

    Bus::assertBatched(fn ($batch) => $batch->jobs->count() === 2 && $batch->jobs->every(fn ($job) => $job instanceof UpdateRegistrationDateJob));

    // Ao final do lote, quem pediu é avisado do total atualizado.
    Bus::assertBatched(function ($batch) {
        $batch->finallyCallbacks()[0](Mockery::mock(Batch::class));

        return true;
    });
    expect(Notification::query()->where('user_id', $this->user->getKey())->value('text'))
        ->toBe('Atualização de datas de matrícula concluída. 501 matrículas atualizadas.');
    expect($this->registration->refresh()->data_matricula->format('Y-m-d'))->toBe('2030-02-01');
});
