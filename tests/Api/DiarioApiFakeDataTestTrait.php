<?php

namespace Tests\Api;

use App\Models\LegacyEnrollment;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyLevelFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyRoundingTableFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyValueRoundingTableFactory;
use Database\Factories\UserFactory;

trait DiarioApiFakeDataTestTrait
{
    /**
     * Cria dados base para testes das regras de avaliação
     *
     * @param LegacyEvaluationRule $evaluationRule
     *
     * @return LegacyEnrollment
     */
    public function getCommonFakeData($evaluationRule)
    {
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();

        $level = LegacyLevelFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200'
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $level,
        ]);

        /** @var LegacySchoolClass $schoolClass */
        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $schoolGrade->grade->course_id,
        ]);
        $level = $schoolClass->grade;

        $level->evaluationRules()->attach($evaluationRule->id, ['ano_letivo' => now()->year]);

        $school = $schoolClass->school;

        $school->courses()->attach($schoolClass->course_id, [
            'ativo' => 1,
            'anos_letivos' => '{' . now()->year . '}',
            'ref_usuario_cad' => UserFactory::new()->admin()->make()->id,
            'data_cadastro' => now(),
        ]);

        $enrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_turma' => $schoolClass,
            'ref_cod_matricula' => LegacyRegistrationFactory::new()->create([
                'ref_ref_cod_escola' => $schoolClass->school_id,
                'ref_ref_cod_serie' => $schoolClass->grade_id,
                'ref_cod_curso' => $schoolClass->course_id,
            ]),
        ]);

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school->id,
        ]);

        return $enrollment;
    }

    /**
     * Adiciona uma etapa ao ano letivo (pmieducar.ano_letivo_modulo)
     *
     * @param LegacySchool $school
     * @param              $number
     * @param null         $year
     */
    public function addAcademicYearStage($school, $number, $year = null)
    {
        if (!$year) {
            $year = now()->year;
        }

        LegacyAcademicYearStageFactory::new()->create([
            'ref_ano' => $year,
            'ref_ref_cod_escola' => $school->id,
            'sequencial' => $number,
        ]);
    }

    /**
     * Cria dados base para testes das regras de avaliação não continuada
     *
     * @return LegacyEnrollment
     */
    public function getPromotionFromAverageAndAttendanceWithoutRetake()
    {
        $roundingTable = LegacyRoundingTableFactory::new()->numeric()->create();
        LegacyValueRoundingTableFactory::new()->count(10)->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $evaluationRule = LegacyEvaluationRuleFactory::new()->mediaPresencaSemRecuperacao()->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $enrollment = $this->getCommonFakeData($evaluationRule);

        return $enrollment;
    }

    public function getProgressionWithAverageCalculationWeightedRecovery()
    {
        $roundingTable = LegacyRoundingTableFactory::new()->numeric()->create();
        LegacyValueRoundingTableFactory::new()->count(10)->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $evaluationRule = LegacyEvaluationRuleFactory::new()->progressaoCalculoMediaRecuperacaoPonderada()->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $enrollment = $this->getCommonFakeData($evaluationRule);

        return $enrollment;
    }

    public function createStages($school, $stages)
    {
        for ($count = 1; $count <= $stages; $count++) {
            $this->addAcademicYearStage($school, $count);
        }
    }

    /**
     * @param LegacySchoolClass $schoolClass
     * @param integer           $disciplines
     */
    public function createDisciplines($schoolClass, $disciplines)
    {
        for ($count = 1; $count <= $disciplines; $count++) {
            $school = $schoolClass->school;
            $grade = $schoolClass->grade;

            $discipline = LegacyDisciplineFactory::new()->create();
            $schoolClass->disciplines()->attach($discipline->id, [
                'ano_escolar_id' => $grade->cod_serie,
                'escola_id' => $school->id
            ]);

            LegacyDisciplineAcademicYearFactory::new()->create([
                'componente_curricular_id' => $discipline->id,
                'ano_escolar_id' => $schoolClass->grade_id,
            ]);

            LegacySchoolGradeDisciplineFactory::new()->create([
                'ref_ref_cod_escola' => $school->id,
                'ref_ref_cod_serie' => $grade->cod_serie,
                'ref_cod_disciplina' => $discipline->id
            ]);
        }
    }
}
