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

test('lista as turmas disponíveis para enturmar a matrícula', function () {
    $this->get('/intranet/educar_matricula_turma_lst.php?ref_cod_matricula=' . $this->registration->getKey() . '&ano_letivo=' . $this->schoolClass->ano)
        ->assertOk()
        ->assertSee('Turma Alfa');
});

test('volta para a listagem de matrículas quando a matrícula não é informada', function () {
    $this->get('/intranet/educar_matricula_turma_lst.php')->assertRedirect('educar_matricula_lst.php');
});
