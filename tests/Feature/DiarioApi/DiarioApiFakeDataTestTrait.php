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
use App\Models\LegacyRoundingTable;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolGrade;
use App\Models\LegacySchoolGradeDiscipline;
use App\Models\LegacyValueRoundingTable;
use App\User;

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
        $course = LegacyCourse::factory()->standardAcademicYear()->create();

        $level = LegacyLevel::factory()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200'
        ]);

        $schoolGrade = LegacySchoolGrade::factory()->create([
            'ref_cod_serie' => $level,
        ]);

        /** @var LegacySchoolClass $schoolClass */
        $schoolClass = LegacySchoolClass::factory()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $schoolGrade->grade->course_id,
        ]);
        $level = $schoolClass->grade;

        $level->evaluationRules()->attach($evaluationRule->id, ['ano_letivo' => now()->year]);

        $school = $schoolClass->school;

        $school->courses()->attach($schoolClass->course_id, [
            'ativo' => 1,
            'anos_letivos' => '{'.now()->year.'}',
            'ref_usuario_cad' => User::factory()->admin()->make()->id,
            'data_cadastro' => now(),
        ]);

        $enrollment = LegacyEnrollment::factory()->create([
            'ref_cod_turma' => $schoolClass,
            'ref_cod_matricula' => LegacyRegistration::factory()->create([
                'ref_ref_cod_escola' => $schoolClass->school_id,
                'ref_ref_cod_serie' => $schoolClass->grade_id,
                'ref_cod_curso' => $schoolClass->course_id,
            ]),
        ]);

        LegacySchoolAcademicYear::factory()->create([
            'ref_cod_escola' => $school->id,
        ]);

        return $enrollment;
    }

    /**
     * Adiciona uma etapa ao ano letivo (pmieducar.ano_letivo_modulo)
     *
     * @param LegacySchool $school
     * @param $number
     * @param null $year
     */
    public function addAcademicYearStage($school, $number, $year = null)
    {
        if (!$year) {
            $year = now()->year;
        }

        LegacyAcademicYearStage::factory()->create([
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
        $roundingTable = LegacyRoundingTable::factory()->numeric()->create();
        LegacyValueRoundingTable::factory()->count(10)->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $evaluationRule = LegacyEvaluationRule::factory()->mediaPresencaSemRecuperacao()->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $enrollment = $this->getCommonFakeData($evaluationRule);

        return $enrollment;
    }

    public function getProgressionWithAverageCalculationWeightedRecovery()
    {
        $roundingTable = LegacyRoundingTable::factory()->numeric()->create();
        LegacyValueRoundingTable::factory()->count(10)->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $evaluationRule = LegacyEvaluationRule::factory()->progressaoCalculoMediaRecuperacaoPonderada()->create([
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

            $discipline = LegacyDiscipline::factory()->create();
            $schoolClass->disciplines()->attach($discipline->id, [
                'ano_escolar_id' => $grade->cod_serie,
                'escola_id' => $school->id
            ]);

            LegacyDisciplineAcademicYear::factory()->create([
                'componente_curricular_id' => $discipline->id,
                'ano_escolar_id' => $schoolClass->grade_id,
            ]);

            LegacySchoolGradeDiscipline::factory()->create([
                'ref_ref_cod_escola' => $school->id,
                'ref_ref_cod_serie' => $grade->cod_serie,
                'ref_cod_disciplina' => $discipline->id
            ]);
        }
    }
}
