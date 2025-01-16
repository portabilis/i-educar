<?php

namespace Tests\Unit\Services;

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGrade;
use App\Services\SchoolLevelsService;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SchoolLevelsServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var SchoolLevelsService
     */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SchoolLevelsService::class);
        $this->disableForeignKeys();
        LegacyGrade::query()->truncate();
        LegacyEvaluationRule::query()->truncate();
    }

    protected function tearDown(): void
    {
        $this->enableForeignKeys();
        parent::tearDown();
    }

    public function test_retorna_regras_avaliacao()
    {
        $regraAvaliacaoFake = LegacyEvaluationRuleFactory::new()->create();
        /** @var LegacyGrade $level */
        $level = LegacyGradeFactory::new()->create();

        $level->evaluationRules()->attach($regraAvaliacaoFake->id, ['ano_letivo' => 2019]);

        $evaluationRules = $this->service->getEvaluationRules($level->cod_serie);

        $this->assertCount(1, $evaluationRules);
        $this->assertEquals($regraAvaliacaoFake->all(), $evaluationRules->first()->all());
    }

    public function test_sem_regras_deve_retornar_vazio()
    {
        $level = LegacyGradeFactory::new()->create();
        $evaluationRules = $this->service->getEvaluationRules($level->cod_serie);
        $this->assertEmpty($evaluationRules);
    }

    public function test_sem_regra_avaliacao_deve_retornar_false()
    {
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
    }

    public function test_regra_avaliacao_permite_definir_componentes_etapa()
    {
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
    }
}
