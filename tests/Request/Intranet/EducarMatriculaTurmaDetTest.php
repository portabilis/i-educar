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

test('mostra a enturmação da matrícula na turma', function () {
    $this->post('/intranet/educar_matricula_turma_det.php', [
        'ref_cod_matricula' => $this->registration->getKey(),
        'ref_cod_turma' => $this->schoolClass->getKey(),
    ])
        ->assertOk()
        ->assertSee('Turma Alfa');
});

test('volta para a listagem de matrículas quando aberta sem dados', function () {
    $this->get('/intranet/educar_matricula_turma_det.php')->assertRedirect('educar_matricula_lst.php');
});
