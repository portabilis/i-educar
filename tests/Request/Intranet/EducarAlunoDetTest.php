<?php

use Database\Factories\LegacyDocumentFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyStudentMedicalRecordFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\UniformDistributionFactory;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('mostra o detalhe do aluno com filiação, documentos, ficha médica e uniforme', function () {
    $mother = LegacyIndividualFactory::new()->create();
    $father = LegacyIndividualFactory::new()->create();
    $individual = LegacyIndividualFactory::new()->create([
        'idpes_mae' => $mother->getKey(),
        'idpes_pai' => $father->getKey(),
    ]);
    $student = LegacyStudentFactory::new()->create(['ref_idpes' => $individual]);

    LegacyDocumentFactory::new()->create(['idpes' => $individual->getKey()]);
    LegacyStudentMedicalRecordFactory::new()->create(['ref_cod_aluno' => $student]);
    UniformDistributionFactory::new()->create(['student_id' => $student]);

    $this->get('/intranet/educar_aluno_det.php?cod_aluno=' . $student->getKey())
        ->assertOk()
        ->assertSee('Aluno - Detalhe')
        ->assertSee((string) $student->getKey());
});

test('sai do detalhe quando o aluno não existe', function () {
    $this->get('/intranet/educar_aluno_det.php?cod_aluno=0')
        ->assertRedirect('/intranet/educar_aluno_lst.php');
});
