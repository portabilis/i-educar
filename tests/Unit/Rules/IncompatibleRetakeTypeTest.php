<?php

use App\Models\LegacyAverageFormula;
use App\Models\LegacyEvaluationRule;
use App\Rules\IncompatibleRetakeType;
use Database\Factories\LegacyAverageFormulaFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolClassFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->schoolClass = LegacySchoolClassFactory::new()->create(['ano' => 2030]);
    $this->rule = new IncompatibleRetakeType;

    $this->configureGrade = function (int $gradeId, int $parallelType, ?LegacyAverageFormula $formula): void {
        $evaluationRule = LegacyEvaluationRuleFactory::new()->create([
            'formula_recuperacao_id' => $formula?->getKey(),
            'tipo_recuperacao_paralela' => $parallelType,
        ]);

        LegacyEvaluationRuleGradeYearFactory::new()->create([
            'serie_id' => $gradeId,
            'regra_avaliacao_id' => $evaluationRule->getKey(),
            'ano_letivo' => 2030,
        ]);
    };

    $this->input = function (array $gradeIds): array {
        return array_map(fn (int $gradeId) => [
            'turma_id' => $this->schoolClass->getKey(),
            'serie_id' => $gradeId,
        ], $gradeIds);
    };
});

test('rejects an empty selection', function () {
    $this->assertFalse($this->rule->passes('grades', []));
    $this->assertSame(
        'As séries selecionadas devem possuir o mesmo tipo de recuperação.',
        $this->rule->message(),
    );
});

test('accepts grades with the same retake type and formula', function () {
    $formula = LegacyAverageFormulaFactory::new()->create();
    $firstGrade = $this->schoolClass->ref_ref_cod_serie;
    $secondGrade = LegacyGradeFactory::new()->create()->getKey();
    ($this->configureGrade)($firstGrade, LegacyEvaluationRule::PARALLEL_REMEDIAL_PER_STAGE, $formula);
    ($this->configureGrade)($secondGrade, LegacyEvaluationRule::PARALLEL_REMEDIAL_PER_STAGE, $formula);

    $this->assertTrue($this->rule->passes('grades', ($this->input)([$firstGrade, $secondGrade])));
});

test('rejects grades with different parallel retake types', function () {
    $formula = LegacyAverageFormulaFactory::new()->create();
    $firstGrade = $this->schoolClass->ref_ref_cod_serie;
    $secondGrade = LegacyGradeFactory::new()->create()->getKey();
    ($this->configureGrade)($firstGrade, LegacyEvaluationRule::PARALLEL_REMEDIAL_PER_STAGE, $formula);
    ($this->configureGrade)($secondGrade, LegacyEvaluationRule::PARALLEL_REMEDIAL_PER_SPECIFIC_STAGE, $formula);

    $this->assertFalse($this->rule->passes('grades', ($this->input)([$firstGrade, $secondGrade])));
});

test('rejects grades with different retake formulas', function () {
    $firstFormula = LegacyAverageFormulaFactory::new()->create();
    $secondFormula = LegacyAverageFormulaFactory::new()->create();
    $firstGrade = $this->schoolClass->ref_ref_cod_serie;
    $secondGrade = LegacyGradeFactory::new()->create()->getKey();
    ($this->configureGrade)($firstGrade, LegacyEvaluationRule::PARALLEL_REMEDIAL_PER_STAGE, $firstFormula);
    ($this->configureGrade)($secondGrade, LegacyEvaluationRule::PARALLEL_REMEDIAL_PER_STAGE, $secondFormula);

    $this->assertFalse($this->rule->passes('grades', ($this->input)([$firstGrade, $secondGrade])));
});

test('ignores grades that do not use parallel retakes when comparing types', function () {
    $formula = LegacyAverageFormulaFactory::new()->create();
    $firstGrade = $this->schoolClass->ref_ref_cod_serie;
    $secondGrade = LegacyGradeFactory::new()->create()->getKey();
    ($this->configureGrade)($firstGrade, LegacyEvaluationRule::PARALLEL_REMEDIAL_PER_STAGE, $formula);
    ($this->configureGrade)($secondGrade, LegacyEvaluationRule::PARALLEL_REMEDIAL_NONE, $formula);

    $this->assertTrue($this->rule->passes('grades', ($this->input)([$firstGrade, $secondGrade])));
});
