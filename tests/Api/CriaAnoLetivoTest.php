<?php

namespace Tests\Api;

use App\Models\EmployeeAllocation;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyDisciplineSchoolClass;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassGrade;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacySchoolClassTeacher;
use App\Models\LegacySchoolClassTeacherDiscipline;
use Database\Factories\EmployeeAllocationFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolClassStageFactory;
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
    use DatabaseTransactions;
    use LoginFirstUser;

    public function testCreateNewSchoolAcademicYear()
    {
        $school = LegacySchoolFactory::new()->create();

        $schoolAcademicYearFactory = LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school,
        ]);

        $course = LegacyCourseFactory::new()->standardAcademicYear()->create(
            ['ref_cod_instituicao' => $school->ref_cod_instituicao]
        );

        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
            'ref_cod_instituicao' => $school->ref_cod_instituicao,
            'ano' => $schoolAcademicYearFactory->ano,
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
            'tipo_nota' => 1,
            'hora_falta' => null,
        ]);

        $employee = EmployeeFactory::new()->create(
            ['institution_id' => $school->ref_cod_instituicao]
        );

        $period = LegacyPeriodFactory::new()->create();

        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'ano' => $schoolAcademicYearFactory->ano,
        ]);

        $role = LegacyRoleFactory::new()->create([
            'ref_cod_instituicao' => $school->ref_cod_instituicao,
        ]);

        $legacyEmployeeRole = LegacyEmployeeRoleFactory::new()->create(
            [
                'ref_cod_funcao' => $role,
                'ref_cod_servidor' => $employee,
                'ref_ref_cod_instituicao' => $school->ref_cod_instituicao,
            ]
        );

        EmployeeAllocationFactory::new()->create([
            'ref_ref_cod_instituicao' => $school->ref_cod_instituicao,
            'ref_cod_escola' => $school->getKey(),
            'ref_cod_servidor' => $employee,
            'ref_cod_servidor_funcao' => $legacyEmployeeRole,
            'ano' => $schoolAcademicYearFactory->ano,
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $stageType = LegacyStageTypeFactory::new()->create([
            'ref_cod_instituicao' => $school->ref_cod_instituicao,
            'num_etapas' => 1,
        ]);

        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
            'ref_cod_modulo' => $stageType,
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
            'dias_letivos' => [100],
        ];

        $this->post('/intranet/educar_ano_letivo_modulo_cad.php?ref_cod_escola=' . $school->getKey() . '&ano=' . $nextYear, $request)
            ->assertRedirectContains('educar_escola_det.php?cod_escola=' . $school->getKey() . '#ano_letivo');

        $this->assertDatabaseHas($schoolAcademicYearFactory, [
            'ano' => $nextYear,
            'ref_cod_escola' => $school->getKey(),
        ]);

        $this->assertDatabaseHas(
            $schoolClass,
            [
                'ref_ref_cod_escola' => $schoolGrade->school_id,
                'ref_ref_cod_serie' => $schoolGrade->grade_id,
                'ref_cod_curso' => $course->getKey(),
                'ref_cod_instituicao' => $school->ref_cod_instituicao,
                'ano' => $nextYear,
            ]
        );

        $this->assertDatabaseHas(
            LegacyAcademicYearStage::class,
            [
                'ref_ref_cod_escola' => $schoolGrade->school_id,
                'ref_cod_modulo' => $stageType->getKey(),
                'ref_ano' => $nextYear,
                'data_inicio' => '01/01/' . $nextYear,
                'data_fim' => '10/10/' . $nextYear,
            ]
        );

        $newSchoolClass = LegacySchoolClass::query()->where(
            [
                'ref_ref_cod_escola' => $schoolGrade->school_id,
                'ref_ref_cod_serie' => $schoolGrade->grade_id,
                'ref_cod_curso' => $course->getKey(),
                'ref_cod_instituicao' => $school->ref_cod_instituicao,
                'ano' => $nextYear,
            ]
        )->first();

        $this->assertDatabaseHas(
            $legacySchoolClassTeacher,
            [
                'servidor_id' => $employee->getKey(),
                'turma_id' => $newSchoolClass->getKey(),
                'turno_id' => $period->getKey(),
                'ano' => $nextYear,
            ]
        );

        $this->assertDatabaseHas(
            EmployeeAllocation::class,
            [
                'ref_ref_cod_instituicao' => $school->ref_cod_instituicao,
                'ref_cod_escola' => $school->getKey(),
                'ref_cod_servidor' => $employee->getKey(),
                'ref_cod_servidor_funcao' => $legacyEmployeeRole->getKey(),
                'ano' => $nextYear,
            ]
        );

        $professorTurma = LegacySchoolClassTeacher::query()->where(
            [
                'ano' => $nextYear,
                'servidor_id' => $employee->getKey(),
            ]
        )->get();

        $this->assertCount(1, $professorTurma);
        $this->assertDatabaseHas(LegacySchoolClassTeacherDiscipline::class, [
            'professor_turma_id' => $professorTurma->first()->getKey(),
            'componente_curricular_id' => $discipline->getKey(),
        ]);

        $this->assertDatabaseHas(LegacySchoolClassStage::class, [
            'ref_cod_turma' => $newSchoolClass->getKey(),
            'ref_cod_modulo' => $stageType->getKey(),
        ]);

        $this->assertDatabaseHas(LegacyDisciplineSchoolClass::class, [
            'componente_curricular_id' => $discipline->getKey(),
            'escola_id' => $school->getKey(),
            'turma_id' => $newSchoolClass->getKey(),
            'ano_escolar_id' => $grade->getKey(),
        ]);
    }

    public function testCreateNewSchoolAcademicYearWithSchoolClassMultiplesGrades()
    {
        $school = LegacySchoolFactory::new()->create();

        $schoolAcademicYearFactory = LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school,
        ]);

        $course = LegacyCourseFactory::new()->standardAcademicYear()->create(
            ['ref_cod_instituicao' => $school->ref_cod_instituicao]
        );

        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->multiplesGrades()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
            'ref_cod_instituicao' => $school->ref_cod_instituicao,
            'ano' => $schoolAcademicYearFactory->ano,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create(
            ['institution_id' => $school->ref_cod_instituicao]
        );

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $schoolGrade->school_id,
            'serie_id' => $schoolGrade->grade_id,
            'turma_id' => $schoolClass,
        ]);

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
            'tipo_nota' => 1,
            'hora_falta' => null,
        ]);

        $employee = EmployeeFactory::new()->create(
            ['institution_id' => $school->ref_cod_instituicao]
        );

        $period = LegacyPeriodFactory::new()->create();

        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'ano' => $schoolAcademicYearFactory->ano,
        ]);

        $role = LegacyRoleFactory::new()->create([
            'ref_cod_instituicao' => $school->ref_cod_instituicao,
        ]);

        $legacyEmployeeRole = LegacyEmployeeRoleFactory::new()->create(
            [
                'ref_cod_funcao' => $role,
                'ref_cod_servidor' => $employee,
                'ref_ref_cod_instituicao' => $school->ref_cod_instituicao,
            ]
        );

        EmployeeAllocationFactory::new()->create([
            'ref_ref_cod_instituicao' => $school->ref_cod_instituicao,
            'ref_cod_escola' => $school->getKey(),
            'ref_cod_servidor' => $employee,
            'ref_cod_servidor_funcao' => $legacyEmployeeRole,
            'ano' => $schoolAcademicYearFactory->ano,
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        $courseNew = LegacyCourseFactory::new()->standardAcademicYear()->create(
            ['ref_cod_instituicao' => $school->ref_cod_instituicao]
        );

        $gradeNew = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $courseNew,
            'dias_letivos' => '200',
        ]);

        LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $gradeNew,
            'ref_cod_escola' => $school,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $gradeNew,
            'turma_id' => $schoolClass,
        ]);

        $stageType = LegacyStageTypeFactory::new()->create([
            'ref_cod_instituicao' => $school->ref_cod_instituicao,
            'num_etapas' => 1,
        ]);

        LegacySchoolClassStageFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
            'ref_cod_modulo' => $stageType,
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
            'dias_letivos' => [100],
        ];

        $this->post('/intranet/educar_ano_letivo_modulo_cad.php?ref_cod_escola=' . $school->getKey() . '&ano=' . $nextYear, $request)
            ->assertRedirectContains('educar_escola_det.php?cod_escola=' . $school->getKey() . '#ano_letivo');

        $this->assertDatabaseHas($schoolAcademicYearFactory, [
            'ano' => $nextYear,
            'ref_cod_escola' => $school->getKey(),
        ]);

        $this->assertDatabaseHas(
            $schoolClass,
            [
                'ref_ref_cod_escola' => $schoolGrade->school_id,
                'ref_ref_cod_serie' => $schoolGrade->grade_id,
                'ref_cod_curso' => $course->getKey(),
                'ref_cod_instituicao' => $school->ref_cod_instituicao,
                'ano' => $nextYear,
            ]
        );

        $newSchoolClass = LegacySchoolClass::query()->where(
            [
                'ref_ref_cod_escola' => $schoolGrade->school_id,
                'ref_ref_cod_serie' => $schoolGrade->grade_id,
                'ref_cod_curso' => $course->getKey(),
                'ref_cod_instituicao' => $school->ref_cod_instituicao,
                'ano' => $nextYear,
            ]
        )->first();

        $this->assertDatabaseHas(LegacySchoolClassStage::class, [
            'ref_cod_turma' => $newSchoolClass->getKey(),
            'ref_cod_modulo' => $stageType->getKey(),
        ]);

        // Turma multsseriada
        $this->assertDatabaseHas(LegacySchoolClassGrade::class, [
            'escola_id' => $school->getKey(),
            'serie_id' => $grade->getKey(),
            'turma_id' => $newSchoolClass->getKey(),
        ]);
    }
}
