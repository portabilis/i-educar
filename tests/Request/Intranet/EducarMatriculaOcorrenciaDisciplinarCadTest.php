<?php

use Database\Factories\LegacyDisciplinaryOccurrenceTypeFactory;
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

test('registra uma ocorrência disciplinar na matrícula', function () {
    $type = LegacyDisciplinaryOccurrenceTypeFactory::new()->create(['ref_cod_instituicao' => $this->schoolClass->ref_cod_instituicao]);
    $url = '/intranet/educar_matricula_ocorrencia_disciplinar_cad.php?ref_cod_matricula=' . $this->registration->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'ref_cod_tipo_ocorrencia_disciplinar' => $type->getKey(),
        'data_cadastro' => now()->format('d/m/Y'),
        'hora_cadastro' => '10:00',
        'observacao' => 'Ocorrência Alfa',
    ]);

    $this->post($url, $payload)
        ->assertRedirect('educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula=' . $this->registration->getKey());

    $this->assertDatabaseHas('pmieducar.matricula_ocorrencia_disciplinar', [
        'ref_cod_matricula' => $this->registration->getKey(),
        'observacao' => 'Ocorrência Alfa',
    ]);
});
