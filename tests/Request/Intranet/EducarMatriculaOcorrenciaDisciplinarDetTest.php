<?php

use Database\Factories\LegacyRegistrationDisciplinaryOccurrenceTypeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Alfa']);
    $this->registration = LegacyRegistrationFactory::new()->withEnrollment($this->schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);
    $this->query = '?ref_cod_matricula=' . $this->registration->getKey() . '&ref_cod_aluno=' . $this->registration->ref_cod_aluno;
});

test('mostra o detalhe da ocorrência disciplinar', function () {
    $occurrence = LegacyRegistrationDisciplinaryOccurrenceTypeFactory::new()->create([
        'ref_cod_matricula' => $this->registration,
        'observacao' => 'Ocorrência Alfa',
    ]);

    $query = http_build_query([
        'ref_cod_matricula' => $this->registration->getKey(),
        'ref_cod_tipo_ocorrencia_disciplinar' => $occurrence->ref_cod_tipo_ocorrencia_disciplinar,
        'sequencial' => $occurrence->sequencial,
    ]);

    $this->get('/intranet/educar_matricula_ocorrencia_disciplinar_det.php?' . $query)
        ->assertOk()
        ->assertSee('Ocorrência Alfa');
});
