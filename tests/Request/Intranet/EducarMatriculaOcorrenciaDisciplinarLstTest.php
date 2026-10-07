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

test('lista as ocorrências disciplinares da matrícula', function () {
    LegacyRegistrationDisciplinaryOccurrenceTypeFactory::new()->create([
        'ref_cod_matricula' => $this->registration,
        'observacao' => 'Ocorrência Alfa',
    ]);

    $this->get('/intranet/educar_matricula_ocorrencia_disciplinar_lst.php?ref_cod_matricula=' . $this->registration->getKey())
        ->assertOk()
        ->assertSee('educar_matricula_ocorrencia_disciplinar_det.php');
});

test('volta para a listagem de matrículas quando a matrícula não é informada', function () {
    $this->get('/intranet/educar_matricula_ocorrencia_disciplinar_lst.php')->assertRedirect('educar_matricula_lst.php');
});
