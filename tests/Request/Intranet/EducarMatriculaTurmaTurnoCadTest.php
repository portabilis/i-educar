<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->student = $this->scenario->student('Ana Alves');
    $this->registration = $this->scenario->registration(['ref_cod_aluno' => $this->student]);

    $this->integral = $this->scenario->schoolClass(['nm_turma' => 'Turma Integral', 'turma_turno_id' => $this->scenario->period(4)]);
    $this->enrollment = $this->scenario->enroll($this->registration, $this->integral, ['turno_id' => 1]);

    $this->url = "/intranet/educar_matricula_turma_turno_cad.php?ref_cod_matricula={$this->registration->getKey()}&ref_cod_aluno={$this->student->getKey()}";
    $this->key = "{$this->integral->getKey()}-{$this->enrollment->sequencial}";
    $this->detail = url('intranet/educar_matricula_det.php?cod_matricula=' . $this->registration->getKey());
});

test('oferece matutino e vespertino para a enturmação em turma integral, com o turno atual', function () {
    $this->scenario->enroll($this->registration, $this->scenario->schoolClass(['nm_turma' => 'Turma Parcial', 'turma_turno_id' => $this->scenario->period(1)]), ['sequencial' => 2]);

    $html = $this->get($this->url)->assertOk()->assertSee('Ana Alves')->assertSee('Turno do aluno na turma: Turma Integral')->getContent();

    expect(LegacyForm::from($html)->options("turno[{$this->key}]"))->toBe([0 => 'Selecione', 1 => 'Matutino', 2 => 'Vespertino'])
        ->and(LegacyFields::from($html)['turno'])->toBe([$this->key => '1'])
        // A turma que não é integral não entra.
        ->and($html)->not->toContain('Turma Parcial');
});

test('em curso de EJA oferece também o noturno', function () {
    $this->scenario->course->update(['modalidade_curso' => ModalidadeCurso::EJA]);

    $html = $this->get($this->url)->assertOk()->getContent();

    expect(LegacyForm::from($html)->options("turno[{$this->key}]"))->toBe([0 => 'Selecione', 1 => 'Matutino', 2 => 'Vespertino', 3 => 'Noturno']);
});

test('grava o turno escolhido e volta para a matrícula', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['turno' => [$this->key => '2']]);

    $this->post($this->url, $payload)->assertRedirect($this->detail)->assertSessionHas('success', 'Turno alterado com sucesso!');

    expect($this->enrollment->refresh()->turno_id)->toBe(2);
});

test('avisa quando o turno enviado é o que já estava gravado', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent());

    $this->post($this->url, $payload)->assertRedirect($this->detail)->assertSessionHas('success', 'Não houve alteração no valor do campo Turno.');

    expect($this->enrollment->refresh()->turno_id)->toBe(1);
});

test('volta para a listagem de matrículas do aluno quando a matrícula não existe', function () {
    $this->get("/intranet/educar_matricula_turma_turno_cad.php?ref_cod_matricula=999999999&ref_cod_aluno={$this->student->getKey()}")
        ->assertRedirect('educar_matricula_lst.php?ref_cod_aluno=' . $this->student->getKey());
});

test('volta para a listagem de matrículas do aluno quando o usuário só pode visualizar matrículas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->withAccess(689)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->url)->assertRedirect('educar_matricula_lst.php?ref_cod_aluno=' . $this->student->getKey());
});

test('manda para o início quem não pode ver a tela ou é de biblioteca', function (Closure $user) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs($user->call($this));

    $this->get($this->url)->assertRedirect('/intranet/index.php');
})->with([
    'sem acesso ao processo 689' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)->create(['ref_cod_instituicao' => $this->scenario->institution])],
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(578)->withAccess(689)->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
