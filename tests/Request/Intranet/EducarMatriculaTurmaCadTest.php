<?php

use App\Models\LegacyCourse;
use App\Models\LegacyEnrollment;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->schoolClass = LegacySchoolClassFactory::new()->create(['nm_turma' => 'Turma Alfa']);
    $this->registration = LegacyRegistrationFactory::new()->withEnrollment($this->schoolClass)->create(['ativo' => 1, 'ultima_matricula' => 1]);
});

test('enturma a matrícula em outra turma da mesma série', function () {
    LegacyCourse::query()->whereKey($this->schoolClass->ref_cod_curso)->update(['padrao_ano_escolar' => 1]);
    LegacySchoolAcademicYearFactory::new()->withStageType(LegacyStageTypeFactory::new()->create())->create([
        'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ano' => $this->schoolClass->ano,
    ]);

    $destination = LegacySchoolClassFactory::new()->create([
        'ref_ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
        'ref_ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
        'ref_cod_curso' => $this->schoolClass->ref_cod_curso,
    ]);

    $this->post('/intranet/educar_matricula_turma_cad.php', [
        'ref_cod_matricula' => $this->registration->getKey(),
        'ref_cod_turma_origem' => '',
        'ref_cod_turma_destino' => $destination->getKey(),
        'data_enturmacao' => now()->month(6)->day(1)->format('d/m/Y'),
    ])->assertRedirect('educar_matricula_det.php?cod_matricula=' . $this->registration->getKey());

    expect(LegacyEnrollment::query()->where('ref_cod_matricula', $this->registration->getKey())->where('ref_cod_turma', $destination->getKey())->exists())->toBeTrue();
});

test('volta para a listagem de matrículas quando aberta sem dados', function () {
    $this->get('/intranet/educar_matricula_turma_cad.php')->assertRedirect('educar_matricula_lst.php');
});

test('volta para a listagem de alunos quando a matrícula não é informada', function () {
    $this->post('/intranet/educar_matricula_turma_cad.php', ['data_enturmacao' => '01/06/2026'])
        ->assertRedirect('/intranet/educar_aluno_lst.php');
});
