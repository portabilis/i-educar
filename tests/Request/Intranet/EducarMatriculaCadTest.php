<?php

use App\Models\LegacyEnrollment;
use App\Models\LegacyRegistration;
use App\Setting;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa', 'max_aluno' => 30]);
    $this->student = $this->scenario->student('Ana Alves');
    $this->url = '/intranet/educar_matricula_cad.php?ref_cod_aluno=' . $this->student->getKey();

    // O que o formulário envia depois que o usuário escolhe ano, escola, curso, série e turma.
    $this->payload = fn (array $overrides = []) => LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides + [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_curso' => $this->scenario->course->getKey(),
        'ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_cod_turma' => $this->schoolClass->getKey(),
        'data_matricula' => '01/03/2030',
    ]);

    $this->registrations = fn () => LegacyRegistration::query()->where('ref_cod_aluno', $this->student->getKey())->orderBy('cod_matricula')->get();
});

// A tela abre uma transação ao gravar e, nas recusas, devolve o formulário
// sem desfazê-la; sem isto o teste seguinte ficaria esperando um bloqueio.
afterEach(function () {
    while (DB::transactionLevel() > 1) {
        DB::rollBack();
    }
});

test('abre o formulário com o nome do aluno e a data de hoje', function () {
    $response = $this->get($this->url)->assertOk()->assertSee('Ana Alves')->assertSee('Matrícula de dependência?');
    $fields = LegacyFields::from($response->getContent());

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['ref_cod_aluno'])->toBe((string) $this->student->getKey())
        ->and($fields['data_matricula'])->toBe(now()->format('d/m/Y'))
        ->and($fields['ano_em_andamento'])->toBe('1');
});

test('não oferece a matrícula de dependência quando a configuração a desliga', function () {
    Setting::query()->updateOrCreate(['key' => 'legacy.app.matricula.dependencia'], ['value' => '0']);

    $this->get($this->url)->assertOk()->assertDontSee('Matrícula de dependência?');
});

test('matricula o aluno e o enturma na turma escolhida', function () {
    $this->post($this->url, ($this->payload)(['observacoes' => 'Entrou por ordem judicial']))
        ->assertRedirect('educar_aluno_det.php?cod_aluno=' . $this->student->getKey())
        ->assertSessionHas('legacy', 'Cadastro efetuado com sucesso.<br />');

    $registration = ($this->registrations)()->sole();

    expect($registration)
        ->ref_ref_cod_escola->toBe($this->scenario->school->getKey())
        ->ref_ref_cod_serie->toBe($this->scenario->grade->getKey())
        ->ref_cod_curso->toBe($this->scenario->course->getKey())
        ->ano->toBe(2030)
        ->aprovado->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO)
        ->ativo->toBe(1)
        ->ultima_matricula->toBe(1)
        ->observacoes->toBe('Entrou por ordem judicial')
        ->ref_usuario_cad->toBe($this->user->getKey())
        ->and((bool) $registration->dependencia)->toBeFalse()
        ->and($registration->data_matricula->format('Y-m-d'))->toBe('2030-03-01');

    $enrollment = LegacyEnrollment::query()->where('ref_cod_matricula', $registration->getKey())->sole();

    expect($enrollment)
        ->ref_cod_turma->toBe($this->schoolClass->getKey())
        ->ativo->toBe(1)
        ->sequencial->toBe(1)
        ->and($enrollment->data_enturmacao->format('Y-m-d'))->toBe('2030-03-01');
});

test('grava a matrícula de dependência', function () {
    $this->scenario->evaluationRule(['qtd_matriculas_dependencia' => 1]);

    $this->post($this->url, ($this->payload)(['dependencia' => 'on']))
        ->assertRedirect('educar_aluno_det.php?cod_aluno=' . $this->student->getKey());

    expect((bool) ($this->registrations)()->sole()->dependencia)->toBeTrue();
});

test('cancela a matrícula pelo link do detalhe, desativando matrícula e enturmação', function () {
    $registration = $this->scenario->registration(['ref_cod_aluno' => $this->student]);
    $enrollment = $this->scenario->enroll($registration, $this->schoolClass);

    $this->get($this->url . '&cod_matricula=' . $registration->getKey())
        ->assertRedirect('educar_aluno_det.php?cod_aluno=' . $this->student->getKey())
        ->assertSessionHas('legacy', 'Exclusão efetuada com sucesso.<br />');

    expect($registration->refresh())
        ->ativo->toBe(0)
        ->ultima_matricula->toBe(0)
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and($enrollment->refresh()->ativo)->toBe(0);
});

test('avisa quando a matrícula a cancelar já está cancelada ou não existe', function (Closure $registration, string $message) {
    $key = $registration->call($this);

    $this->get($this->url . '&cod_matricula=' . $key)->assertOk()->assertSee($message);
})->with([
    'já cancelada' => [fn () => $this->scenario->registration(['ref_cod_aluno' => $this->student, 'ativo' => 0])->getKey(), 'A matrícula já está cancelada.'],
    'inexistente' => [fn () => 999999999, 'Exclusão não realizada.'],
]);

test('avisa quando o cancelamento falha por um erro inesperado', function () {
    $registration = $this->scenario->registration(['ref_cod_aluno' => $this->student]);

    LegacyRegistration::saving(fn () => throw new RuntimeException('Falha ao gravar.'));

    $this->get($this->url . '&cod_matricula=' . $registration->getKey())->assertOk()->assertSee('Exclusão não realizada.');
});

test('não cancela a matrícula de quem não tem a permissão de cancelamento', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $registration = $this->scenario->registration(['ref_cod_aluno' => $this->student]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578)->withAccess(680)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->url . '&cod_matricula=' . $registration->getKey())->assertOk();

    expect($registration->refresh()->ativo)->toBe(1);
});

test('volta para a listagem de alunos quando o aluno não existe', function () {
    $this->get('/intranet/educar_matricula_cad.php?ref_cod_aluno=0')->assertRedirect('educar_aluno_lst.php');
});

test('volta para o detalhe do aluno quando o usuário só pode visualizar matrículas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->withAccess(680)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->url)->assertRedirect('educar_aluno_det.php?cod_aluno=' . $this->student->getKey());
});

test('manda para o início quem não pode ver a tela de nova matrícula ou é de biblioteca', function (Closure $user) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs($user->call($this));

    $this->get($this->url)->assertRedirect('/intranet/index.php');
})->with([
    'sem acesso ao processo 680' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)->create(['ref_cod_instituicao' => $this->scenario->institution])],
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(578)->withAccess(680)->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
