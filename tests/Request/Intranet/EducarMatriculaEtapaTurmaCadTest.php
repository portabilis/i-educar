<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->student]);

    // Etapa 3 do Educacenso: educação infantil unificada (creche e pré-escola).
    $this->multi = $this->scenario->schoolClass(['nm_turma' => 'Turma Multi', 'etapa_educacenso' => 3]);
    $this->enrollment = $this->scenario->enroll($this->registration, $this->multi, ['etapa_educacenso' => 1]);

    $this->url = "/intranet/educar_matricula_etapa_turma_cad.php?ref_cod_matricula={$this->registration->getKey()}&ref_cod_aluno={$this->student->getKey()}";
    $this->field = "etapas_educacenso[{$this->multi->getKey()}-{$this->enrollment->sequencial}]";
});

test('oferece, para cada turma multisseriada, as etapas que a turma admite', function () {
    $this->scenario->enroll($this->registration, $this->scenario->schoolClass(['nm_turma' => 'Turma Comum']), ['sequencial' => 2]);

    $html = $this->get($this->url)->assertOk()->assertSee('Ana Alves')->assertSee('Etapa do aluno na turma Turma Multi')->getContent();
    $options = LegacyForm::from($html)->options($this->field);

    expect(array_keys($options))->toBe([0, 1, 2])
        ->and($options[0])->toBe('Nenhuma')
        ->and(LegacyFields::from($html)['etapas_educacenso'])->toBe(["{$this->multi->getKey()}-{$this->enrollment->sequencial}" => '1'])
        // A turma comum não entra.
        ->and($html)->not->toContain('Turma Comum');
});

test('grava a etapa escolhida na enturmação e volta para a matrícula', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), [
        'etapas_educacenso' => ["{$this->multi->getKey()}-{$this->enrollment->sequencial}" => 2],
    ]);

    $this->post($this->url, $payload)
        ->assertRedirect('educar_matricula_det.php?cod_matricula=' . $this->registration->getKey())
        ->assertSessionHas('legacy', 'Etapas atualizadas com sucesso.<br>');

    expect($this->enrollment->refresh()->etapa_educacenso)->toBe(2);
});

test('responde 404 quando a enturmação enviada não é da matrícula', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), [
        'etapas_educacenso' => ['999999999-1' => 2],
    ]);

    $this->post($this->url, $payload)->assertNotFound();
});

test('volta para a listagem de matrículas do aluno quando a matrícula não existe', function () {
    $this->get("/intranet/educar_matricula_etapa_turma_cad.php?ref_cod_matricula=999999999&ref_cod_aluno={$this->student->getKey()}")
        ->assertRedirect('educar_matricula_lst.php?ref_cod_aluno=' . $this->student->getKey());
});

test('volta para a listagem de matrículas do aluno quando o usuário só pode visualizar matrículas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->withAccess(687)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->url)->assertRedirect('educar_matricula_lst.php?ref_cod_aluno=' . $this->student->getKey());
});

test('manda para o início quem não pode ver a tela ou é de biblioteca', function (Closure $user) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs($user->call($this));

    $this->get($this->url)->assertRedirect('/intranet/index.php');
})->with([
    'sem acesso ao processo 687' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)->create(['ref_cod_instituicao' => $this->scenario->institution])],
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(578)->withAccess(687)->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
