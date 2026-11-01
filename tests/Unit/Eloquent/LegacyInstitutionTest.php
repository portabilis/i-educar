<?php

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGeneralConfiguration;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use Database\Factories\LegacyAverageFormulaFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyRoundingTableFactory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyInstitution::class, relations: [
    'generalConfiguration' => LegacyGeneralConfiguration::class,
    'schools' => LegacySchool::class,
]));

EloquentModelTestCase::registerModelTests();

test('scope active', function () {
    $found = LegacyInstitution::active()->get();

    $this->assertCount(1, $found);
});

test('attributes', function () {
    $this->assertEquals($this->model->cod_instituicao, $this->model->id);
    $this->assertEquals($this->model->nm_instituicao, $this->model->name);
    $this->assertEquals($this->model->cidade, $this->model->city);
    $this->assertEquals($this->model->state, $this->model->ref_sigla_uf);
});

test('relocation date', function () {
    $this->assertEquals($this->model->relocationDate, $this->model->data_base_remanejamento);
});

test('educacenso date', function () {
    $this->assertEquals($this->model->educacensoDate, $this->model->data_educacenso);
});

test('is mandatory censo fields', function () {
    $this->assertEquals((bool) $this->model->obrigar_campos_censo, $this->model->isMandatoryCensoFields());
});

test('get allow registration out academic year attribute', function () {
    $this->assertEquals((bool) $this->model->permitir_matricula_fora_periodo_letivo, $this->model->allowRegistrationOutAcademicYear);
});

test('relationship evaluation rules', function () {
    LegacyRoundingTableFactory::new()->create([
        'instituicao_id' => $this->model,
    ]);
    LegacyEvaluationRuleFactory::new()->create([
        'formula_media_id' => LegacyAverageFormulaFactory::new()->create([
            'institution_id' => $this->model,
        ]),
        'instituicao_id' => $this->model,
    ]);

    $this->assertCount(1, $this->model->evaluationRules);
    $this->assertInstanceOf(LegacyEvaluationRule::class, $this->model->evaluationRules->first());
});

test('get relocation date', function () {
    $this->assertEquals($this->model->relocationDate?->format('Y-m-d'), $this->model->getRelocationDate());
});
