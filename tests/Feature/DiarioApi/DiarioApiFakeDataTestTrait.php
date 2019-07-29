<?php

namespace Tests\Feature\DiarioApi;

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyCourse;
use App\Models\LegacyDiscipline;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacyEnrollment;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyLevel;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolGrade;
use App\User;

trait DiarioApiFakeDataTestTrait
{
    /**
     * Cria dados base para testes das regras de avaliação
     *
     * @param LegacyEvaluationRule $evaluationRule
     * @return LegacyEnrollment
     */
    public function getCommonFakeData($evaluationRule)
    {
        $course = factory(LegacyCourse::class, 'padrao-ano-escolar')->create();

        $level = factory(LegacyLevel::class)->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200'
        ]);

        $schoolGrade = factory(LegacySchoolGrade::class)->create([
            'ref_cod_serie' => $level,
        ]);

        /** @var LegacySchoolClass $schoolClass */
        $schoolClass = factory(LegacySchoolClass::class)->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $schoolGrade->grade->course_id,
        ]);
        $level = $schoolClass->grade;

        $level->evaluationRules()->attach($evaluationRule->id, ['ano_letivo' => 2019]);

        $discipline = factory(LegacyDiscipline::class)->create();

        $school = $schoolClass->school;

        $schoolClass->disciplines()->attach($discipline->id, ['ano_escolar_id' => 1, 'escola_id' => $school->id]);
        $school->courses()->attach($schoolClass->course_id, [
            'ativo' => 1,
            'anos_letivos' => '{'.now()->year.'}',
            'ref_usuario_cad' => factory(User::class, 'admin')->make()->id,
            'data_cadastro' => now(),
        ]);

        factory(LegacyDisciplineAcademicYear::class)->create([
            'componente_curricular_id' => $discipline->id,
            'ano_escolar_id' => $schoolClass->grade_id,
        ]);

        $enrollment = factory(LegacyEnrollment::class)->create([
            'ref_cod_turma' => $schoolClass,
            'ref_cod_matricula' => factory(LegacyRegistration::class)->create([
                'ref_ref_cod_escola' => $schoolClass->school_id,
                'ref_ref_cod_serie' => $schoolClass->grade_id,
                'ref_cod_curso' => $schoolClass->course_id,
            ]),
        ]);

        factory(LegacySchoolAcademicYear::class)->create([
            'ref_cod_escola' => $school->id,
        ]);

        factory(LegacyAcademicYearStage::class)->create([
            'ref_ano' => now()->year,
            'ref_ref_cod_escola' => $school->id,
        ]);

        return $enrollment;
    }

    /**
     * Adiciona uma etapa ao ano letivo (pmieducar.ano_letivo_modulo)
     *
     * @param LegacySchoolClass $schoolClass
     * @param $number
     */
    public function addAcademicYearStage($schoolClass, $number, $year = null)
    {
        if (!$year) {
            $year = now()->year;
        }

        factory(LegacyAcademicYearStage::class)->create([
            'ref_ano' => $year,
            'ref_ref_cod_escola' => $schoolClass->school_id,
            'sequencial' => $number,
        ]);
    }
}
