<?php

use Database\Factories\LegacyAbandonmentTypeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Alfa']);
    $this->registration = LegacyRegistrationFactory::new()->withEnrollment($this->schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);
    $this->query = '?ref_cod_matricula=' . $this->registration->getKey() . '&ref_cod_aluno=' . $this->registration->ref_cod_aluno;
});

test('registra que o aluno deixou de frequentar', function () {
    $url = '/intranet/educar_abandono_cad.php' . $this->query;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'abandono_tipo' => LegacyAbandonmentTypeFactory::new()->create(['ref_cod_instituicao' => $this->schoolClass->ref_cod_instituicao])->getKey(),
        'data_cancel' => now()->format('d/m/Y'),
    ]);

    $this->post($url, $payload)->assertRedirect();
});
