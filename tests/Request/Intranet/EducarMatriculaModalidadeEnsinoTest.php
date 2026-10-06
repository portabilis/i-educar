<?php

use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Alfa']);
    $this->registration = LegacyRegistrationFactory::new()->withEnrollment($this->schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);
});

test('abre e grava a modalidade de ensino da matrícula', function () {
    $url = '/intranet/educar_matricula_modalidade_ensino.php?ref_cod_matricula=' . $this->registration->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent());

    $this->post($url, $payload)
        ->assertOk()
        ->assertSee('Modalidade de ensino atualizado com sucesso.');
});

test('volta para a listagem de matrículas quando a matrícula não é informada', function () {
    $this->get('/intranet/educar_matricula_modalidade_ensino.php')->assertRedirect();
});
