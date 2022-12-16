<?php

namespace Tests\Api;

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolClassTeacherDisciplineFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStageTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class CriaAnoLetivoTest extends TestCase
{
    use DatabaseTransactions, LoginFirstUser;

    public function testCreateNewSchoolAcademicYear()
    {

        $school = LegacySchoolFactory::new()->create();

        $schoolAcademicYearFactory = LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school,
        ]);

        $course = LegacyCourseFactory::new()->standardAcademicYear()->create(
            ['ref_cod_instituicao'=> $school->ref_cod_instituicao]
        );

        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200'
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
            'ref_cod_instituicao' => $school->ref_cod_instituicao
        ]);

        $discipline = LegacyDisciplineFactory::new()->create(
            ['institution_id' => $school->ref_cod_instituicao]
        );

        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
            'ano_escolar_id' => $grade,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id'  => $grade,
            'turma_id'  => $schoolClass
        ]);

        $stageType = LegacyStageTypeFactory::new()->create([
           'ref_cod_instituicao' => $school->ref_cod_instituicao,
            'num_etapas' => 1
        ]);

        $nextYear = $schoolAcademicYearFactory->ano + 1;

        $request = [
            'tipoacao' => 'Novo',
            'ref_ano' => $nextYear,
            'ref_ref_cod_escola' => $school->getKey(),
            'ref_cod_modulo' => $stageType->getKey(),
            'copiar_alocacoes_e_vinculos_professores' => true,
            'copiar_alocacoes_demais_servidores' => true,
            'data_inicio' => ['01/01/' . $nextYear],
            'data_fim' => ['10/10/' . $nextYear],
            'dias_letivos' => [100]
        ];

        $this->post('/intranet/educar_ano_letivo_modulo_cad.php?ref_cod_escola=' . $school->getKey() . '&ano=' . $nextYear, $request)
            ->assertRedirectContains('educar_escola_det.php?cod_escola=' . $school->getKey() . '#ano_letivo');

        $this->assertDatabaseHas($schoolAcademicYearFactory, [
            'ano' => $nextYear,
            'ref_cod_escola'=> $school->getKey()
        ]);
    }
}
