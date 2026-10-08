<?php

use App\Exceptions\Registration\RegistrationException;
use App\Http\Controllers\BatchEnrollmentController;
use App\Listeners\LoginLegacySession;
use App\Models\LegacyEnrollment;
use App\Process;
use App\Services\EnrollmentService;
use App\Services\RegistrationService;
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
        $this->user = $user;
        $this->actingAs($user);
        app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($user->getKey()), false));
    };
    ($this->login)(LegacyUserFactory::new()->admin()->create());

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);
    $this->schoolClass = $this->scenario->schoolClass(['max_aluno' => 2]);
    $this->ana = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Ana Alves'), 'data_matricula' => '2030-02-01']);
    $this->bia = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Bia Borges'), 'data_matricula' => '2030-02-01']);

    $this->active = fn () => LegacyEnrollment::query()->where('ref_cod_turma', $this->schoolClass->getKey())->where('ativo', 1)->pluck('ref_cod_matricula')->sort()->values()->all();
});

test('a enturmação em lote lista as matrículas ainda não enturmadas, em ordem de nome', function () {
    $this->scenario->enroll($this->bia, $this->schoolClass, ['data_enturmacao' => '2030-02-05']);

    $this->get("/enturmacao-em-lote/{$this->schoolClass->getKey()}")
        ->assertOk()
        ->assertViewHas('registrations', fn ($registrations) => $registrations->pluck('cod_matricula')->all() === [$this->ana->getKey()])
        ->assertViewHas('canCancelRegistration', true);
});

test('a enturmação em lote exige a data e as matrículas, e respeita as vagas', function () {
    $this->post("/enturmacao-em-lote/{$this->schoolClass->getKey()}", [])
        ->assertSessionHasErrors(['date' => 'A data de enturmação é obrigatória.', 'registrations' => 'Ao menos uma matrícula deve ser selecionada.']);

    $this->schoolClass->schoolGrade->update(['bloquear_enturmacao_sem_vagas' => 1]);
    $this->schoolClass->update(['max_aluno' => 1]);

    $this->post("/enturmacao-em-lote/{$this->schoolClass->getKey()}", ['date' => '10/03/2030', 'registrations' => [$this->ana->getKey(), $this->bia->getKey()]])
        ->assertSessionHasErrors(['registrations' => 'Há somente 1 vaga na turma, você tentou enturmar 2 aluno(s).']);
});

test('enturma as matrículas selecionadas e mostra quem falhou', function () {
    // Bia já está na turma: a enturmação dela falha e ela volta à lista.
    $this->schoolClass->update(['max_aluno' => 10]);
    $this->scenario->enroll($this->bia, $this->schoolClass, ['data_enturmacao' => '2030-02-05']);

    $this->post("/enturmacao-em-lote/{$this->schoolClass->getKey()}", ['date' => '10/03/2030', 'registrations' => [$this->ana->getKey(), $this->bia->getKey()]])
        ->assertOk()
        ->assertViewHas('success', fn ($success) => $success->has((string) $this->ana->getKey()))
        ->assertViewHas('fails', fn ($fails) => $fails->has((string) $this->bia->getKey()))
        ->assertSee('Foi enturmado 1 aluno.')
        ->assertSee('Não foi possível enturmar 1 aluno.')
        ->assertViewHas('registrations', fn ($registrations) => $registrations->pluck('cod_matricula')->contains($this->bia->getKey()));

    expect(($this->active)())->toBe(collect([$this->ana->getKey(), $this->bia->getKey()])->sort()->values()->all());
});

test('a desenturmação em lote lista as enturmações ativas e desenturma as selecionadas', function () {
    $ana = $this->scenario->enroll($this->ana, $this->schoolClass, ['data_enturmacao' => '2030-02-05']);
    $bia = $this->scenario->enroll($this->bia, $this->schoolClass, ['data_enturmacao' => '2030-02-05']);

    $this->get("/cancelar-enturmacao-em-lote/{$this->schoolClass->getKey()}")
        ->assertOk()
        ->assertViewHas('enrollments', fn ($enrollments) => $enrollments->count() === 2);

    $this->post("/cancelar-enturmacao-em-lote/{$this->schoolClass->getKey()}", [])
        ->assertSessionHasErrors(['date' => 'A data de saída é obrigatória.', 'enrollments' => 'Ao menos uma matrícula deve ser selecionada.']);

    $this->post("/cancelar-enturmacao-em-lote/{$this->schoolClass->getKey()}", ['date' => '10/03/2030', 'enrollments' => [$ana->getKey(), $bia->getKey()]])
        ->assertOk()
        ->assertSee('Foram desenturmados 2 alunos.');

    expect(($this->active)())->toBe([]);

    $this->post("/cancelar-enturmacao-em-lote/{$this->schoolClass->getKey()}", ['date' => '01/01/2030', 'enrollments' => [$ana->getKey()]])
        ->assertOk()
        ->assertSee('Não foi possível desenturmar 1 aluno.');
});

test('o cancelamento de matrícula em lote lista as matrículas canceláveis e cancela só as da turma', function () {
    $this->scenario->enroll($this->ana, $this->schoolClass, ['data_enturmacao' => '2030-02-05']);
    $this->scenario->enroll($this->bia, $this->schoolClass, ['data_enturmacao' => '2030-02-05']);
    $other = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Caio Costa')]);

    $this->get("/cancelar-matricula-em-lote/{$this->schoolClass->getKey()}")
        ->assertOk()
        ->assertViewHas('enrollments', fn ($enrollments) => $enrollments->count() === 2);

    $this->post("/cancelar-matricula-em-lote/{$this->schoolClass->getKey()}", [])
        ->assertSessionHasErrors('registrations');

    $this->post("/cancelar-matricula-em-lote/{$this->schoolClass->getKey()}", ['registrations' => [$this->ana->getKey(), $this->bia->getKey(), $other->getKey()]])
        ->assertOk()
        ->assertSee('Foram canceladas 2 matrículas.');

    expect($this->ana->refresh()->ativo)->toBe(0)
        ->and($this->bia->refresh()->ativo)->toBe(0)
        ->and($other->refresh()->ativo)->toBe(1);
});

test('usuários de escola só operam turmas das próprias escolas', function () {
    $user = LegacyUserFactory::new()->school()->withAccess(Process::BATCH_ENROLLMENT)->withAccess(Process::REGISTRATIONS)->withAccess(Process::CANCEL_REGISTRATION)->create();
    ($this->login)($user);

    $this->get("/enturmacao-em-lote/{$this->schoolClass->getKey()}")->assertForbidden();
    $this->get("/cancelar-enturmacao-em-lote/{$this->schoolClass->getKey()}")->assertForbidden();
    $this->get("/cancelar-matricula-em-lote/{$this->schoolClass->getKey()}")->assertForbidden();

    ($this->login)(LegacyUserFactory::new()->school()->withSchool($this->scenario->school)->withAccess(Process::BATCH_ENROLLMENT)->withAccess(Process::REGISTRATIONS)->create());

    $this->get("/enturmacao-em-lote/{$this->schoolClass->getKey()}")
        ->assertOk()
        ->assertViewHas('canCancelRegistration', false);
});

test('as mensagens contam quantos foram enturmados e quantos falharam por erro inesperado', function () {
    $this->post("/enturmacao-em-lote/{$this->schoolClass->getKey()}", ['date' => '10/03/2030', 'registrations' => [$this->ana->getKey(), $this->bia->getKey()]])
        ->assertOk()
        ->assertSee('Foram enturmados 2 alunos.');

    $this->schoolClass->update(['max_aluno' => 10]);
    $enrollments = Mockery::mock(EnrollmentService::class);
    $enrollments->shouldReceive('enroll')->andThrow(new RuntimeException('Turma bloqueada'));
    app()->instance(EnrollmentService::class, $enrollments);
    $other = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Caio Costa'), 'data_matricula' => '2030-02-01']);
    $another = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Davi Dias'), 'data_matricula' => '2030-02-01']);

    $this->post("/enturmacao-em-lote/{$this->schoolClass->getKey()}", ['date' => '10/03/2030', 'registrations' => [$other->getKey(), $another->getKey()]])
        ->assertOk()
        ->assertSee('Não foi possível enturmar 2 alunos.')
        ->assertViewHas('fails', fn ($fails) => $fails->first((string) $other->getKey()) === 'Turma bloqueada');
});

test('as mensagens da desenturmação contam uma saída e várias falhas', function () {
    $ana = $this->scenario->enroll($this->ana, $this->schoolClass, ['data_enturmacao' => '2030-02-05']);
    $bia = $this->scenario->enroll($this->bia, $this->schoolClass, ['data_enturmacao' => '2030-02-05']);

    $this->post("/cancelar-enturmacao-em-lote/{$this->schoolClass->getKey()}", ['date' => '01/01/2030', 'enrollments' => [$ana->getKey(), $bia->getKey()]])
        ->assertOk()
        ->assertSee('Não foi possível desenturmar 2 alunos.');

    $this->post("/cancelar-enturmacao-em-lote/{$this->schoolClass->getKey()}", ['date' => '10/03/2030', 'enrollments' => [$ana->getKey()]])
        ->assertOk()
        ->assertSee('Foi desenturmado 1 aluno.');
});

test('as mensagens do cancelamento de matrícula contam uma ou várias falhas e um cancelamento', function () {
    $this->scenario->enroll($this->ana, $this->schoolClass, ['data_enturmacao' => '2030-02-05']);
    $this->scenario->enroll($this->bia, $this->schoolClass, ['data_enturmacao' => '2030-02-05']);
    $real = app(RegistrationService::class);

    $registrations = Mockery::mock($real)->makePartial();
    $registrations->shouldReceive('cancelRegistration')->with(Mockery::on(fn ($registration) => $registration->is($this->ana)), Mockery::any())->andThrow(new RegistrationException('Matrícula com notas lançadas'));
    $registrations->shouldReceive('cancelRegistration')->with(Mockery::on(fn ($registration) => $registration->is($this->bia)), Mockery::any())->andThrow(new RuntimeException('falha inesperada'));
    app()->instance(RegistrationService::class, $registrations);

    $this->post("/cancelar-matricula-em-lote/{$this->schoolClass->getKey()}", ['registrations' => [$this->ana->getKey(), $this->bia->getKey()]])
        ->assertOk()
        ->assertSee('Não foi possível cancelar 2 matrículas.')
        ->assertViewHas('fails', fn ($fails) => $fails->first((string) $this->ana->getKey()) === 'Matrícula com notas lançadas'
            && $fails->first((string) $this->bia->getKey()) === 'Não foi possível cancelar a matrícula.');

    $this->post("/cancelar-matricula-em-lote/{$this->schoolClass->getKey()}", ['registrations' => [$this->ana->getKey()]])
        ->assertOk()
        ->assertSee('Não foi possível cancelar 1 matrícula.');

    app()->instance(RegistrationService::class, $real);

    $this->post("/cancelar-matricula-em-lote/{$this->schoolClass->getKey()}", ['registrations' => [$this->ana->getKey()]])
        ->assertOk()
        ->assertSee('Foi cancelada 1 matrícula.');
});

test('sem usuário logado não pode cancelar matrículas', function () {
    auth()->logout();

    expect((fn () => $this->canCancelRegistration())->call(app(BatchEnrollmentController::class)))->toBeFalse();
});
