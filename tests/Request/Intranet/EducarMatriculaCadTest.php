<?php

use App\Models\LegacyCourse;
use App\Models\LegacyRegistration;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('volta para a listagem de alunos quando o aluno não existe', function () {
    $this->get('/intranet/educar_matricula_cad.php?ref_cod_aluno=0')
        ->assertRedirect('educar_aluno_lst.php');
});

test('matricula o aluno na turma', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();
    $student = LegacyStudentFactory::new()->create();

    LegacySchoolAcademicYearFactory::new()->withStageType(LegacyStageTypeFactory::new()->create())->create([
        'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
        'ano' => $schoolClass->ano,
    ]);

    LegacyCourse::query()->whereKey($schoolClass->ref_cod_curso)->update(['padrao_ano_escolar' => 1]);

    $url = '/intranet/educar_matricula_cad.php?ref_cod_aluno=' . $student->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'ano' => $schoolClass->ano,
        'ref_cod_instituicao' => $schoolClass->ref_cod_instituicao,
        'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
        'ref_cod_curso' => $schoolClass->ref_cod_curso,
        'ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
        'ref_cod_turma' => $schoolClass->getKey(),
        'data_matricula' => now()->month(3)->day(1)->format('d/m/Y'),
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post($url, $payload)->assertRedirect();

    expect(LegacyRegistration::query()->where('ref_cod_aluno', $student->getKey())->exists())->toBeTrue();
});
