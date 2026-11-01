<?php

use App\Models\LegacyDisciplineScoreAverage;
use Database\Factories\LegacyDisciplineScoreAverageFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationScoreFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->student, 'data_matricula' => '2030-02-10']);
    $this->enrollment = $this->scenario->enroll($this->registration, $this->scenario->schoolClass(), ['data_enturmacao' => '2030-02-10']);

    $this->url = "/intranet/educar_falecido_cad.php?ref_cod_matricula={$this->registration->getKey()}&ref_cod_aluno={$this->student->getKey()}";
    $this->detail = 'educar_matricula_det.php?cod_matricula=' . $this->registration->getKey();

    $this->payload = fn (array $overrides = []) => LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides + [
        'data_cancel' => '15/05/2030',
        'observacao' => 'Comunicado pela família',
    ]);
});

test('abre com o aluno e a data de hoje', function () {
    $html = $this->get($this->url)->assertOk()->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($html)->toContain('Ana Alves')
        ->and($fields['data_cancel'])->toBe(now()->format('d/m/Y'))
        ->and($fields['ref_cod_matricula'])->toBe((string) $this->registration->getKey())
        ->and($fields['ref_cod_aluno'])->toBe((string) $this->student->getKey());
});

test('registra o falecimento na matrícula e encerra as enturmações na data informada', function () {
    $this->post($this->url, ($this->payload)())->assertRedirect($this->detail);

    expect($this->registration->refresh())
        ->aprovado->toBe(App_Model_MatriculaSituacao::FALECIDO)
        ->observacao->toBe('Comunicado pela família')
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and($this->registration->data_cancel->format('Y-m-d'))->toBe('2030-05-15');

    expect($this->enrollment->refresh())
        ->ativo->toBe(0)
        ->and((bool) $this->enrollment->falecido)->toBeTrue()
        ->and((bool) $this->enrollment->abandono)->toBeFalse()
        ->and($this->enrollment->data_exclusao->format('Y-m-d'))->toBe('2030-05-15')
        ->and($this->enrollment->data_enturmacao->format('Y-m-d'))->toBe('2030-02-10');
});

test('sem observação grava "Não informado"', function () {
    $this->post($this->url, ($this->payload)(['observacao' => '']))->assertRedirect($this->detail);

    expect($this->registration->refresh()->observacao)->toBe('Não informado');
});

test('leva o falecimento para as médias lançadas', function () {
    $average = LegacyDisciplineScoreAverageFactory::new()->create([
        'nota_aluno_id' => LegacyRegistrationScoreFactory::new()->create(['matricula_id' => $this->registration]),
    ]);

    $this->post($this->url, ($this->payload)())->assertRedirect($this->detail);

    $situation = LegacyDisciplineScoreAverage::query()
        ->where('nota_aluno_id', $average->nota_aluno_id)
        ->where('componente_curricular_id', $average->componente_curricular_id)
        ->value('situacao');

    expect($situation)->toBe(App_Model_MatriculaSituacao::FALECIDO);
});

test('registra o falecimento de uma matrícula sem enturmação ativa', function () {
    $this->enrollment->update(['ativo' => 0]);

    $this->post($this->url, ($this->payload)())->assertRedirect($this->detail);

    expect($this->registration->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::FALECIDO)
        ->and((bool) $this->enrollment->refresh()->falecido)->toBeFalse();
});

test('recusa a observação com mais de 300 caracteres', function () {
    $this->post($this->url, ($this->payload)(['observacao' => str_repeat('a', 301)]))
        ->assertOk()
        ->assertSee('O campo observação deve conter no máximo 300 caracteres.');

    expect($this->registration->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO);
});

test('recusa a data anterior à da matrícula', function (?string $registrationDate, string $created) {
    $this->registration->update(['data_matricula' => $registrationDate, 'data_cadastro' => $created]);

    $this->post($this->url, ($this->payload)(['data_cancel' => '05/02/2030']))
        ->assertOk()
        ->assertSee('Data de falecimento não pode ser inferior a data da matrícula.');

    expect($this->registration->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO);
})->with([
    'pela data da matrícula' => ['2030-02-10', '2030-01-01 08:00:00'],
    // Sem data de matrícula, vale a data de cadastro.
    'pela data de cadastro' => [null, '2030-02-10 08:00:00'],
]);

test('avisa quando a matrícula não vem no envio', function () {
    $this->post($this->url, ($this->payload)(['ref_cod_matricula' => '']))
        ->assertOk()
        ->assertSee('A alteração não pode ser realizado.');

    expect($this->registration->refresh()->aprovado)->toBe(App_Model_MatriculaSituacao::EM_ANDAMENTO);
});

test('defeito: a mensagem de sucesso não chega à sessão', function () {
    // A tela usa `$this->mensagem .=`, que a classe base não intercepta.
    $this->post($this->url, ($this->payload)())
        ->assertRedirect($this->detail)
        ->assertSessionMissing('legacy');
});

test('volta para a listagem de matrículas do aluno quando o usuário só pode visualizar matrículas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->withAccess(686)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->url)->assertRedirect('educar_matricula_lst.php?ref_cod_aluno=' . $this->student->getKey());
});

test('manda para o início quem não pode ver a tela ou é de biblioteca', function (Closure $user) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs($user->call($this));

    $this->get($this->url)->assertRedirect('/intranet/index.php');
})->with([
    'sem acesso ao processo 686' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)->create(['ref_cod_instituicao' => $this->scenario->institution])],
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(578)->withAccess(686)->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
