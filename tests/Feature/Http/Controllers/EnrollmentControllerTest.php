<?php

use App\Listeners\LoginLegacySession;
use App\Models\LegacyEnrollment;
use App\Process;
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
    $this->alfa = $this->scenario->schoolClass(['nm_turma' => 'Alfa']);
    $this->beta = $this->scenario->schoolClass(['nm_turma' => 'Beta']);
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->scenario->student('Bia Borges'), 'data_matricula' => '2030-02-01']);

    $this->enroll = fn ($schoolClass, array $data = []) => $this->from('/origem')->post("/matricula/{$this->registration->getKey()}/enturmar/{$schoolClass->getKey()}", $data + ['enrollment_date' => '10/03/2030']);
    $this->relocate = fn ($schoolClass, array $data = []) => $this->from('/origem')->post("/matricula/{$this->registration->getKey()}/remanejar/{$schoolClass->getKey()}", $data + ['enrollment_date' => '10/04/2030']);
    $this->detail = '/intranet/educar_matricula_det.php?cod_matricula=' . $this->registration->getKey();
    $this->active = fn () => LegacyEnrollment::query()->where('ref_cod_matricula', $this->registration->getKey())->where('ativo', 1)->pluck('ref_cod_turma')->all();
});

test('a tela de enturmação mostra se o aluno já está na turma', function () {
    $this->get("/matricula/{$this->registration->getKey()}/enturmar/{$this->alfa->getKey()}")
        ->assertOk()
        ->assertViewHas('enableCancelButton', false)
        ->assertViewHas('canEnroll', true);

    $this->scenario->enroll($this->registration, $this->alfa, ['data_enturmacao' => '2030-03-01']);

    $this->get("/matricula/{$this->registration->getKey()}/enturmar/{$this->alfa->getKey()}")
        ->assertOk()
        ->assertViewHas('enableCancelButton', true);
});

// As rotas de gravar já barram pelo middleware quem não tem a permissão da
// própria ação; o controller ainda confere a desenturmação e o remanejamento.
test('sem permissão, as telas e as ações de enturmar, desenturmar e remanejar voltam com erro', function () {
    $url = "/matricula/{$this->registration->getKey()}/enturmar/{$this->alfa->getKey()}";

    ($this->login)(LegacyUserFactory::new()->institutional()->withAccess(Process::REGISTRATIONS)->create());
    $this->from('/origem')->get($url)->assertRedirect('/origem')->assertSessionHas('error', 'Você não tem permissão para enturmar alunos.');

    ($this->login)(LegacyUserFactory::new()->institutional()->withAccess(Process::REGISTRATIONS)->withAccess(Process::ENROLLMENT)->withAccess(Process::RELOCATE, modify: false)->create());
    $this->get($url)->assertOk();
    ($this->enroll)($this->alfa, ['is_cancellation' => 1])->assertRedirect('/origem')->assertSessionHas('error', 'Você não tem permissão para desenturmar alunos.');
    $this->from('/origem')->get("/matricula/{$this->registration->getKey()}/remanejar/{$this->alfa->getKey()}")->assertRedirect('/origem')->assertSessionHas('error', 'Você não tem permissão para remanejar alunos.');

    $this->scenario->enroll($this->registration, $this->alfa, ['data_enturmacao' => '2030-03-01']);
    $this->from('/origem')->get($url)->assertRedirect('/origem')->assertSessionHas('error', 'Você não tem permissão para desenturmar alunos.');
});

test('a data de enturmação é obrigatória e válida', function () {
    ($this->enroll)($this->alfa, ['enrollment_date' => ''])
        ->assertSessionHasErrors(['enrollment_date' => 'A data de enturmação é obrigatória.']);

    ($this->enroll)($this->alfa, ['enrollment_date' => '2030-03-10'])
        ->assertSessionHasErrors(['enrollment_date' => 'A data de enturmação deve ser uma data válida.']);
});

test('enturma, desenturma e avisa quando o serviço recusa', function () {
    ($this->enroll)($this->alfa)
        ->assertRedirect($this->detail)
        ->assertSessionHas('success', 'Enturmação realizada com sucesso.');

    expect(($this->active)())->toBe([$this->alfa->getKey()]);

    ($this->enroll)($this->alfa)
        ->assertRedirect('/origem')
        ->assertSessionHas('error');

    $enrollment = LegacyEnrollment::query()->where('ref_cod_matricula', $this->registration->getKey())->first();

    ($this->enroll)($this->alfa, ['is_cancellation' => 1, 'enrollment_from_id' => $enrollment->getKey(), 'enrollment_date' => '20/03/2030'])
        ->assertRedirect($this->detail)
        ->assertSessionHas('success', 'Desenturmação realizada com sucesso.');

    expect(($this->active)())->toBe([]);
});

test('a tela de remanejamento mostra as enturmações ativas', function () {
    $this->scenario->enroll($this->registration, $this->alfa, ['data_enturmacao' => '2030-03-01']);

    $this->get("/matricula/{$this->registration->getKey()}/remanejar/{$this->alfa->getKey()}")
        ->assertOk()
        ->assertViewHas('enableCancelButton', true)
        ->assertViewHas('enrollments', fn ($enrollments) => $enrollments->count() === 1);
});

test('remaneja para outra turma, exige a turma de origem e avisa quando o serviço recusa', function () {
    ($this->relocate)($this->beta)
        ->assertRedirect('/origem')
        ->assertSessionHas('error', 'É necessário selecionar a turma de origem para o remanejamento.');

    $enrollment = $this->scenario->enroll($this->registration, $this->alfa, ['data_enturmacao' => '2030-03-01']);

    ($this->relocate)($this->beta, ['enrollment_from_id' => $enrollment->getKey()])
        ->assertRedirect($this->detail)
        ->assertSessionHas('success', 'Remanejamento realizado com sucesso.');

    expect(($this->active)())->toBe([$this->beta->getKey()])
        ->and((bool) $enrollment->refresh()->remanejado)->toBeTrue();

    ($this->relocate)($this->beta, ['enrollment_from_id' => $enrollment->getKey(), 'enrollment_date' => '01/01/2030'])
        ->assertRedirect('/origem')
        ->assertSessionHas('error');
});

test('remanejar de volta para a mesma turma marca o remanejamento no mesmo grupo', function () {
    $enrollment = $this->scenario->enroll($this->registration, $this->alfa, ['data_enturmacao' => '2030-03-01']);

    ($this->relocate)($this->alfa, ['enrollment_from_id' => $enrollment->getKey()])
        ->assertRedirect($this->detail)
        ->assertSessionHas('success', 'Remanejamento realizado com sucesso.');

    expect(($this->active)())->toBe([$this->alfa->getKey()])
        ->and((bool) $enrollment->refresh()->remanejado_mesma_turma)->toBeTrue();
});
