<?php

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGrade;
use App\Services\SchoolLevelsService;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->service = app(SchoolLevelsService::class);
    $this->disableForeignKeys();
    LegacyGrade::query()->truncate();
    LegacyEvaluationRule::query()->truncate();
});

afterEach(function () {
    $this->enableForeignKeys();
});

test('retorna regras avaliacao', function () {
    $regraAvaliacaoFake = LegacyEvaluationRuleFactory::new()->create();
    /** @var LegacyGrade $level */
    $level = LegacyGradeFactory::new()->create();

    $level->evaluationRules()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);

    $evaluationRules = $this->service->getEvaluationRules($level->cod_serie);

    $this->assertCount(1, $evaluationRules);
    $this->assertEquals($regraAvaliacaoFake->all(), $evaluationRules->first()->all());
});

test('sem regras deve retornar vazio', function () {
    $level = LegacyGradeFactory::new()->create();
    $evaluationRules = $this->service->getEvaluationRules($level->cod_serie);
    $this->assertEmpty($evaluationRules);
});

test('sem regra avaliacao deve retornar false', function () {
    $result = $this->service->levelAllowDefineDisciplinePerStage(null, 2019);
    $this->assertFalse($result);

    $level = LegacyGradeFactory::new()->create();
    $result = $this->service->levelAllowDefineDisciplinePerStage($level->cod_serie, 2019);
    $this->assertFalse($result);

    $level = LegacyGradeFactory::new()->create();
    $regraAvaliacaoFake = LegacyEvaluationRuleFactory::new()->create([
        'definir_componente_etapa' => true,
    ]);
    $level->evaluationRules()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);
    $result = $this->service->levelAllowDefineDisciplinePerStage($level->cod_serie, 2021);
    $this->assertFalse($result);
});

test('regra avaliacao permite definir componentes etapa', function () {
    $level = LegacyGradeFactory::new()->create();
    $regraAvaliacaoFake = LegacyEvaluationRuleFactory::new()->create([
        'definir_componente_etapa' => true,
    ]);

    $level->evaluationRules()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);
    $result = $this->service->levelAllowDefineDisciplinePerStage($level->cod_serie, 2019);

    $this->assertTrue($result);

    $level = LegacyGradeFactory::new()->create();
    $regraAvaliacaoFake = LegacyEvaluationRuleFactory::new()->create([
        'definir_componente_etapa' => false,
    ]);

    $level->evaluationRules()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);
    $result = $this->service->levelAllowDefineDisciplinePerStage($level->cod_serie, 2019);

    $this->assertFalse($result);
});
