<?php

use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Alfa']);
    $this->registration = LegacyRegistrationFactory::new()->withEnrollment($this->schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);
});

test('marca a matrícula como formando', function () {
    $this->get('/intranet/educar_matricula_formando_cad.php?ref_cod_matricula=' . $this->registration->getKey() . '&formando=1')
        ->assertRedirect();

    expect($this->registration->refresh()->formando)->toBe(1);
});

test('volta para a listagem de matrículas sem os parâmetros', function () {
    $this->get('/intranet/educar_matricula_formando_cad.php')->assertRedirect();
});
