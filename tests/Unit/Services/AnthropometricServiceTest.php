<?php

namespace Tests\Unit\Services;

use App\Services\AnthropometricService;
use DateTime;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;

class AnthropometricServiceTest extends TestCase
{
    private const TEST_BIRTH_DATE_1990 = '1990-01-01';
    private const TEST_BIRTH_DATE_2010 = '2010-01-15';

    private AnthropometricService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AnthropometricService;

        // Tentar carregar dados do banco (se disponível), senão usa fallback
        try {
            $this->service->carregarReferenciasDoBanco();
        } catch (\Exception $e) {
            // Ignora erro - usa dados de fallback do service
        }
    }

    private function assertClassificationResult(array $expected, array $actual): void
    {
        $this->assertEquals($expected['code'], $actual['code']);
        $this->assertEquals($expected['label'], $actual['label']);
    }

    private function testIMCCalculation(float $peso, float $altura, ?float $expectedIMC): void
    {
        $imc = $this->service->calcularIMC($peso, $altura);
        if ($expectedIMC === null) {
            $this->assertNull($imc);
        } else {
            $this->assertEquals($expectedIMC, $imc);
        }
    }

    private function testWaistRiskAdult(float $circunferencia, string $sexo, string $expectedCode, string $expectedLabel): void
    {
        $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura($circunferencia, $sexo, 25);
        $this->assertClassificationResult(['code' => $expectedCode, 'label' => $expectedLabel], $result);
    }

    private function assertNoReferenceData(array $result): void
    {
        $this->assertClassificationResult(
            ['code' => 'no_reference_data', 'label' => 'Dados de referência não disponíveis'],
            $result
        );
    }

    #[Group('imc-calculation')]
    public function test_calcular_imc_with_valid_data(): void
    {
        // Teste básico: peso 70kg, altura 175cm
        $this->testIMCCalculation(70.0, 175.0, 22.86);

        // Teste com sobrepeso: peso 85kg, altura 170cm
        $this->testIMCCalculation(85.0, 170.0, 29.41);

        // Teste com obesidade: peso 100kg, altura 165cm
        $this->testIMCCalculation(100.0, 165.0, 36.73);
    }

    #[Group('imc-calculation')]
    public function test_calcular_imc_with_invalid_data(): void
    {
        $invalidCases = [
            [-1.0, 175.0], [70.0, -1.0], [2.0, 175.0],
            [350.0, 175.0], [70.0, 30.0], [70.0, 300.0]
        ];

        foreach ($invalidCases as [$peso, $altura]) {
            $this->testIMCCalculation($peso, $altura, null);
        }
    }

    #[Group('imc-classification-adult')]
    public function test_obter_classificacao_imc_adulto(): void
    {
        $testCases = [
            [17.0, 'underweight', 'Baixo peso'],
            [22.0, 'normal', 'Peso normal'],
            [27.0, 'overweight', 'Sobrepeso'],
            [32.0, 'obesity_class_1', 'Obesidade grau I'],
            [37.0, 'obesity_class_2', 'Obesidade grau II'],
            [45.0, 'obesity_class_3', 'Obesidade grau III (grave)']
        ];

        foreach ($testCases as [$imc, $expectedCode, $expectedLabel]) {
            $result = $this->service->obterClassificacaoIMCAdulto($imc);
            $this->assertClassificationResult(['code' => $expectedCode, 'label' => $expectedLabel], $result);
        }
    }

    #[Group('imc-classification-child')]
    public function test_obter_classificacao_imc_crianca(): void
    {
        $testCases = [[16.0, 120, 'M'], [16.0, 120, 'X']];

        foreach ($testCases as [$imc, $idadeMeses, $sexo]) {
            $result = $this->service->obterClassificacaoIMCCrianca($imc, $idadeMeses, $sexo);
            $this->assertNoReferenceData($result);
        }
    }

    #[Group('age-calculation')]
    public function test_calcular_idade_em_meses(): void
    {
        $nascimento = new DateTime(self::TEST_BIRTH_DATE_2010);
        $referencia = new DateTime('2020-01-15');

        $idadeMeses = $this->service->calcularIdadeEmMeses($nascimento, $referencia);
        $this->assertEquals(120, $idadeMeses); // 10 anos = 120 meses
    }

    #[Group('age-calculation')]
    public function test_calcular_idade_em_anos(): void
    {
        $nascimento = new DateTime(self::TEST_BIRTH_DATE_2010);
        $referencia = new DateTime('2020-01-15');

        $idadeAnos = $this->service->calcularIdadeEmAnos($nascimento, $referencia);
        $this->assertEquals(10, $idadeAnos);
    }

    #[Group('age-calculation')]
    public function test_calcular_idade_com_strings(): void
    {
        $idadeMeses = $this->service->calcularIdadeEmMeses(self::TEST_BIRTH_DATE_2010, '2020-01-15');
        $this->assertEquals(120, $idadeMeses);

        $idadeAnos = $this->service->calcularIdadeEmAnos(self::TEST_BIRTH_DATE_2010, '2020-01-15');
        $this->assertEquals(10, $idadeAnos);
    }

    #[Group('waist-risk-adult')]
    public function test_obter_classificacao_risco_circunferencia_cintura_adulto(): void
    {
        // Testes para homens
        $this->testWaistRiskAdult(90.0, 'M', 'no_risk', 'Sem risco');
        $this->testWaistRiskAdult(98.0, 'M', 'increased', 'Risco aumentado');
        $this->testWaistRiskAdult(110.0, 'M', 'high', 'Risco muito aumentado');

        // Testes para mulheres
        $this->testWaistRiskAdult(75.0, 'F', 'no_risk', 'Sem risco');
        $this->testWaistRiskAdult(85.0, 'F', 'increased', 'Risco aumentado');
        $this->testWaistRiskAdult(95.0, 'F', 'high', 'Risco muito aumentado');
    }

    #[Group('waist-risk-child')]
    public function test_obter_classificacao_risco_circunferencia_cintura_crianca(): void
    {
        $testCases = [[50.0, 'M', 8], [70.0, 'M', 8]];

        foreach ($testCases as [$circunferencia, $sexo, $idade]) {
            $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura($circunferencia, $sexo, $idade);
            $this->assertClassificationResult(
                ['code' => 'no_reference_data', 'label' => 'Dados de referência não disponíveis'],
                $result
            );
        }
    }

    #[Group('complete-evaluation')]
    public function test_obter_avaliacao_completa_adulto(): void
    {
        $avaliacao = $this->service->obterAvaliacaoCompleta(
            peso: 70.0,
            altura: 175.0,
            circunferenciaCintura: 85.0,
            dataNascimento: self::TEST_BIRTH_DATE_1990,
            sexo: 'M',
            dataAvaliacao: '2024-01-01'
        );

        $this->assertEquals(70.0, $avaliacao['peso_kg']);
        $this->assertEquals(175.0, $avaliacao['altura_cm']);
        $this->assertEquals(22.86, $avaliacao['imc']);
        $this->assertEquals(34, $avaliacao['idade_anos']);
        $this->assertEquals('M', $avaliacao['sexo']);

        // Verificar classificação de adulto
        $this->assertEquals('normal', $avaliacao['imc_classificacao']['code']);
        $this->assertEquals('Peso normal', $avaliacao['imc_classificacao']['label']);

        // Verificar risco da cintura
        $this->assertArrayHasKey('cintura_classificacao', $avaliacao);
        $this->assertEquals('no_risk', $avaliacao['cintura_classificacao']['code']);
    }

    #[Group('complete-evaluation')]
    public function test_obter_avaliacao_completa_crianca(): void
    {
        $avaliacao = $this->service->obterAvaliacaoCompleta(
            peso: 35.0,
            altura: 140.0,
            circunferenciaCintura: 60.0,
            dataNascimento: '2014-01-01',
            sexo: 'M',
            dataAvaliacao: '2024-01-01'
        );

        $this->assertEquals(35.0, $avaliacao['peso_kg']);
        $this->assertEquals(140.0, $avaliacao['altura_cm']);
        $this->assertEquals(17.86, $avaliacao['imc']);
        $this->assertEquals(10, $avaliacao['idade_anos']);
        $this->assertEquals(120, $avaliacao['idade_meses']);
        $this->assertEquals('M', $avaliacao['sexo']);

        // Verificar que tem classificação para criança
        $this->assertArrayHasKey('imc_classificacao', $avaliacao);

        // Verificar que tem Z-score
        $this->assertArrayHasKey('imc_zscore', $avaliacao);
    }

    #[Group('edge-cases')]
    public function test_obter_avaliacao_completa_com_dados_invalidos(): void
    {
        $avaliacao = $this->service->obterAvaliacaoCompleta(
            peso: -1.0, // peso inválido
            altura: 175.0,
            circunferenciaCintura: null,
            dataNascimento: self::TEST_BIRTH_DATE_1990,
            sexo: 'X', // sexo inválido
            dataAvaliacao: '2024-01-01'
        );

        $this->assertNull($avaliacao['imc']);
        $this->assertEquals('not_evaluable', $avaliacao['imc_classificacao']['code']);
        $this->assertEquals('Não avaliável', $avaliacao['imc_classificacao']['label']);
    }

    #[Group('normalization')]
    public function test_normalizacao_sexo(): void
    {
        // Teste através do método público que usa normalizarSexo internamente
        $avaliacao1 = $this->service->obterAvaliacaoCompleta(70.0, 175.0, null, self::TEST_BIRTH_DATE_1990, 'MASCULINO');
        $this->assertEquals('M', $avaliacao1['sexo']);

        $avaliacao2 = $this->service->obterAvaliacaoCompleta(70.0, 175.0, null, self::TEST_BIRTH_DATE_1990, 'FEMININO');
        $this->assertEquals('F', $avaliacao2['sexo']);

        $avaliacao3 = $this->service->obterAvaliacaoCompleta(70.0, 175.0, null, self::TEST_BIRTH_DATE_1990, 'male');
        $this->assertEquals('M', $avaliacao3['sexo']);

        $avaliacao4 = $this->service->obterAvaliacaoCompleta(70.0, 175.0, null, self::TEST_BIRTH_DATE_1990, 'female');
        $this->assertEquals('F', $avaliacao4['sexo']);
    }

    #[Group('z-score')]
    public function test_calcular_escore_zimc(): void
    {
        $testCases = [
            [16.8, 120, 'M'], [16.8, 120, 'X'],
            [16.8, 60, 'M'], [25.0, 240, 'M']
        ];

        foreach ($testCases as [$imc, $idadeMeses, $sexo]) {
            $zScore = $this->service->calcularEscoreZIMC($imc, $idadeMeses, $sexo);
            $this->assertNull($zScore);
        }
    }

    #[Group('boundary-values')]
    public function test_valores_limite_imc(): void
    {
        $boundaryTests = [
            [18.49, 'underweight'], [18.5, 'normal'],
            [24.99, 'normal'], [25.0, 'overweight'],
            [29.99, 'overweight'], [30.0, 'obesity_class_1']
        ];

        foreach ($boundaryTests as [$imc, $expectedCode]) {
            $result = $this->service->obterClassificacaoIMCAdulto($imc);
            $this->assertEquals($expectedCode, $result['code']);
        }
    }

    #[Group('performance')]
    public function test_performance_with_multiple_calculations(): void
    {
        $startTime = microtime(true);

        // Executa 1000 cálculos de IMC
        for ($i = 0; $i < 1000; $i++) {
            $peso = 50 + ($i % 100); // Peso entre 50-149kg
            $altura = 150 + ($i % 50); // Altura entre 150-199cm
            $this->service->calcularIMC($peso, $altura);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Deve executar 1000 cálculos em menos de 0.1 segundos
        $this->assertLessThan(0.1, $executionTime, 'Performance test failed: IMC calculations took too long');
    }

    #[Group('integration')]
    public function test_integracao_completa_scenarios(): void
    {
        // Cenário 1: Criança com sobrepeso
        $avaliacao = $this->service->obterAvaliacaoCompleta(
            peso: 50.0,
            altura: 150.0,
            circunferenciaCintura: 75.0,
            dataNascimento: '2014-01-01',
            sexo: 'F',
            dataAvaliacao: '2024-01-01'
        );

        $this->assertEquals(22.22, $avaliacao['imc']);
        $this->assertEquals(10, $avaliacao['idade_anos']);
        $this->assertArrayHasKey('imc_zscore', $avaliacao);

        // Cenário 2: Adulto com obesidade
        $avaliacao = $this->service->obterAvaliacaoCompleta(
            peso: 120.0,
            altura: 170.0,
            circunferenciaCintura: 110.0,
            dataNascimento: '1980-01-01',
            sexo: 'M',
            dataAvaliacao: '2024-01-01'
        );

        $this->assertEquals(41.52, $avaliacao['imc']);
        $this->assertEquals(44, $avaliacao['idade_anos']);
        $this->assertEquals('obesity_class_3', $avaliacao['imc_classificacao']['code']);
        $this->assertEquals('high', $avaliacao['cintura_classificacao']['code']);

        // Cenário 3: Sem circunferência
        $avaliacao = $this->service->obterAvaliacaoCompleta(
            peso: 65.0,
            altura: 165.0,
            circunferenciaCintura: null,
            dataNascimento: '1995-01-01',
            sexo: 'F'
        );

        $this->assertEquals(23.88, $avaliacao['imc']);
        $this->assertArrayNotHasKey('cintura_classificacao', $avaliacao);
    }

    #[Group('data-types')]
    public function test_tipos_de_dados_variados(): void
    {
        $dataTypeTests = [
            ['70.5', '175.0', 23.02], // strings numéricas
            [70, 175, 22.86], // inteiros
            [70.5, 175.0, 23.02] // floats
        ];

        foreach ($dataTypeTests as [$peso, $altura, $expectedIMC]) {
            $this->testIMCCalculation($peso, $altura, $expectedIMC);
        }
    }

    #[Group('edge-cases-extended')]
    public function test_casos_limite_estendidos(): void
    {
        $imcTests = [[17.0, 132, 'M'], [19.0, 156, 'F']];
        $waistTests = [[67.0, 'M', 10], [66.9, 'M', 10]];

        foreach ($imcTests as [$imc, $idadeMeses, $sexo]) {
            $result = $this->service->obterClassificacaoIMCCrianca($imc, $idadeMeses, $sexo);
            $this->assertNoReferenceData($result);
        }

        foreach ($waistTests as [$circunferencia, $sexo, $idade]) {
            $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura($circunferencia, $sexo, $idade);
            $this->assertNoReferenceData($result);
        }
    }

    #[Group('data-validation')]
    public function test_validacao_rigorosa_dados(): void
    {
        $validTests = [
            [5.0, 175.0, 1.63], [300.0, 175.0, 97.96],
            [70.0, 50.0, 280.0], [70.0, 250.0, 11.2]
        ];

        $invalidTests = [
            [4.9, 175.0], [300.1, 175.0],
            [70.0, 49.9], [70.0, 250.1]
        ];

        foreach ($validTests as [$peso, $altura, $expectedIMC]) {
            $this->testIMCCalculation($peso, $altura, $expectedIMC);
        }

        foreach ($invalidTests as [$peso, $altura]) {
            $this->testIMCCalculation($peso, $altura, null);
        }
    }
}
