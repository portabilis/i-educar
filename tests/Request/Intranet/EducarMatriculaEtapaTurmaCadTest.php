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

test('abre e grava a etapa do aluno na turma', function () {
    $url = '/intranet/educar_matricula_etapa_turma_cad.php?ref_cod_matricula=' . $this->registration->getKey() . '&ref_cod_aluno=' . $this->registration->ref_cod_aluno;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent());

    $this->post($url, $payload)->assertRedirect('educar_matricula_det.php?cod_matricula=' . $this->registration->getKey());
});

test('volta para a listagem de matrículas quando a matrícula não é informada', function () {
    $this->get('/intranet/educar_matricula_etapa_turma_cad.php')->assertRedirect();
});
