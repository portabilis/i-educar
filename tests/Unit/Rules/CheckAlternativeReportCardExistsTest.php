<?php

namespace Tests\Unit\Rules;

use App\Rules\CheckAlternativeReportCardExists;
use Mockery;
use Tests\TestCase;

class CheckAlternativeReportCardExistsTest extends TestCase
{
    public function test_campo_boletim_turma_multiseriada()
    {
        $param = new \stdClass;
        $param->ref_ref_cod_serie = 1;
        $param->ano = 2025;
        $param->tipo_boletim_diferenciado = 1;
        $param->multiseriada = 1;

        $rule = new CheckAlternativeReportCardExists();

        $result = $rule->passes(null, $param);

        $this->assertTrue($result);
    }

    public function test_turma_multiseriada()
    {
        $param = new \stdClass;
        $param->ref_ref_cod_serie = 1;
        $param->ano = 2025;
        $param->tipo_boletim_diferenciado = null;
        $param->multiseriada = 1;

        $rule = new CheckAlternativeReportCardExists();

        $result = $rule->passes(null, $param);

        $this->assertTrue($result);
    }

    public function test_campo_boletim_diferenciado()
    {
        $param = new \stdClass;
        $param->ref_ref_cod_serie = 1;
        $param->ano = 2025;
        $param->tipo_boletim_diferenciado = 1;
        $param->multiseriada = 0;

        $rule = new CheckAlternativeReportCardExists();

        $result = $rule->passes(null, $param);

        $this->assertTrue($result);
    }

    public function test_regra_existente_campo_boletim_nulo()
    {
        $param = new \stdClass;
        $param->ref_ref_cod_serie = 1;
        $param->ano = 2025;
        $param->tipo_boletim_diferenciado = null;
        $param->multiseriada = 0;

        $legacyGradeMock = Mockery::mock('alias:\App\Models\LegacyGrade');

        $evaluationRulesMock = Mockery::mock();
        $evaluationRulesMock->shouldReceive('wherePivot->get->first')
            ->andReturn((object)['regra_diferenciada_id' => 1]);

        $gradeMock = Mockery::mock();
        $gradeMock->shouldReceive('evaluationRules')
            ->andReturn($evaluationRulesMock);

        $legacyGradeMock->shouldReceive('findOrFail')
            ->with(1)
            ->andReturn($gradeMock);

        $rule = new CheckAlternativeReportCardExists();

        $result = $rule->passes(null, $param);

        $expectedMessage = "O campo Boletim diferenciado é obrigatório quando a regra de avaliação da série possui regra diferenciada definida.";

        $this->assertFalse($result);
        $this->assertEquals($expectedMessage, $rule->message());
    }

    public function test_regra_diferenciada_nula()
    {
        $param = new \stdClass;
        $param->ref_ref_cod_serie = 1;
        $param->ano = 2025;
        $param->tipo_boletim_diferenciado = null;
        $param->multiseriada = 0;

        $legacyGradeMock = Mockery::mock('alias:\App\Models\LegacyGrade');

        $evaluationRulesMock = Mockery::mock();
        $evaluationRulesMock->shouldReceive('wherePivot->get->first')
            ->andReturn((object)['regra_diferenciada_id' => null]);

        $gradeMock = Mockery::mock();
        $gradeMock->shouldReceive('evaluationRules')
            ->andReturn($evaluationRulesMock);

        $legacyGradeMock->shouldReceive('findOrFail')
            ->with(1)
            ->andReturn($gradeMock);

        $rule = new CheckAlternativeReportCardExists();

        $result = $rule->passes(null, $param);


        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
