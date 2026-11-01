<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;

/**
 * A tela não tem formulário: recebe a matrícula e o valor por GET, grava e
 * volta para o detalhe da matrícula.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->registration = $this->scenario->registration(['formando' => 0]);

    $this->mark = fn ($formando) => $this->get(
        "/intranet/educar_matricula_formando_cad.php?ref_cod_matricula={$this->registration->getKey()}&formando={$formando}"
    );

    $this->detail = 'educar_matricula_det.php?cod_matricula=' . $this->registration->getKey();
});

test('marca e desmarca a matrícula como formando', function () {
    ($this->mark)(1)->assertRedirect($this->detail);

    expect($this->registration->refresh())
        ->formando->toBe(1)
        ->ref_usuario_exc->toBe($this->user->getKey());

    ($this->mark)(0)->assertRedirect($this->detail);

    expect($this->registration->refresh()->formando)->toBe(0);
});

test('sem o valor, ou com matrícula inexistente, só volta para o detalhe', function () {
    ($this->mark)('')->assertRedirect($this->detail);

    expect($this->registration->refresh()->formando)->toBe(0);

    $this->get('/intranet/educar_matricula_formando_cad.php?ref_cod_matricula=999999999&formando=1')
        ->assertRedirect('educar_matricula_det.php?cod_matricula=999999999');
});

test('não marca quando o usuário só pode visualizar matrículas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    ($this->mark)(1)->assertRedirect('educar_matricula_lst.php');

    expect($this->registration->refresh()->formando)->toBe(0);
});
