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

        $level->evaluationRules()->attach($evaluationRule->id, ['ano_letivo' => now()->year]);

        $school = $schoolClass->school;

        $school->courses()->attach($schoolClass->course_id, [
            'ativo' => 1,
            'anos_letivos' => '{'.now()->year.'}',
            'ref_usuario_cad' => factory(User::class, 'admin')->make()->id,
            'data_cadastro' => now(),
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

        factory(LegacyAcademicYearStage::class)->create([
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
        $roundingTable = factory(LegacyRoundingTable::class, 'numeric')->create();
        factory(LegacyValueRoundingTable::class, 10)->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $evaluationRule = factory(LegacyEvaluationRule::class, 'media-presenca-sem-recuperacao')->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $enrollment = $this->getCommonFakeData($evaluationRule);

        return $enrollment;
    }

    public function getProgressionWithAverageCalculationWeightedRecovery()
    {
        $roundingTable = factory(LegacyRoundingTable::class, 'numeric')->create();
        factory(LegacyValueRoundingTable::class, 10)->create([
            'tabela_arredondamento_id' => $roundingTable->id,
        ]);

        $evaluationRule = factory(LegacyEvaluationRule::class, 'progressao-calculo-media-recuperacao-ponderada')->create([
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
     * @param integer $disciplines
     */
    public function createDisciplines($schoolClass, $disciplines)
    {
        for ($count = 1; $count <= $disciplines; $count++) {
            $school = $schoolClass->school;
            $grade = $schoolClass->grade;

            $discipline = factory(LegacyDiscipline::class)->create();
            $schoolClass->disciplines()->attach($discipline->id, [
                'ano_escolar_id' => $grade->cod_serie,
                'escola_id' => $school->id
            ]);

            factory(LegacyDisciplineAcademicYear::class)->create([
                'componente_curricular_id' => $discipline->id,
                'ano_escolar_id' => $schoolClass->grade_id,
            ]);

            factory(LegacySchoolGradeDiscipline::class)->create([
                'ref_ref_cod_escola' => $school->id,
                'ref_ref_cod_serie' => $grade->cod_serie,
                'ref_cod_disciplina' => $discipline->id
            ]);
        }
    }
}
