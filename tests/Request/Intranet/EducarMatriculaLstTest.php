<?php

use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('lista as matrículas do aluno', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Alfa']);
    $registration = LegacyRegistrationFactory::new()->withEnrollment($schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);

    $this->get('/intranet/educar_matricula_lst.php?ref_cod_aluno=' . $registration->ref_cod_aluno)
        ->assertOk()
        ->assertSee('Matrícula - Listagem')
        ->assertSee('educar_matricula_det.php?cod_matricula=' . $registration->getKey());
});

test('volta para a listagem de alunos quando o aluno não é informado', function () {
    $this->get('/intranet/educar_matricula_lst.php')
        ->assertRedirect('educar_aluno_lst.php');
});
