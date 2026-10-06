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

test('abre e grava os pareceres da turma', function () {
    $url = '/intranet/educar_parecer_turma_cad.php?cod_turma=' . $this->schoolClass->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent());

    $this->post($url, $payload)->assertRedirect('educar_turma_det.php?cod_turma=' . $this->schoolClass->getKey());
});
