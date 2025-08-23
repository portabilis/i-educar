<?php

namespace Tests\Unit\Services;

use App\Services\AnthropometricService;
use DateTime;
use PHPUnit\Framework\TestCase;

class AnthropometricServiceTest extends TestCase
{
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

    /**
     * @group imc-calculation
     */
    public function test_calcular_imc_with_valid_data(): void
    {
        // Teste básico: peso 70kg, altura 175cm
        $imc = $this->service->calcularIMC(70.0, 175.0);
        $this->assertEquals(22.86, $imc);

        // Teste com sobrepeso: peso 85kg, altura 170cm
        $imc = $this->service->calcularIMC(85.0, 170.0);
        $this->assertEquals(29.41, $imc);

        // Teste com obesidade: peso 100kg, altura 165cm
        $imc = $this->service->calcularIMC(100.0, 165.0);
        $this->assertEquals(36.73, $imc);
    }

    /**
     * @group imc-calculation
     */
    public function test_calcular_imc_with_invalid_data(): void
    {
        // Peso negativo
        $this->assertNull($this->service->calcularIMC(-1.0, 175.0));

        // Altura negativa
        $this->assertNull($this->service->calcularIMC(70.0, -1.0));

        // Peso muito baixo
        $this->assertNull($this->service->calcularIMC(2.0, 175.0));

        // Peso muito alto
        $this->assertNull($this->service->calcularIMC(350.0, 175.0));

        // Altura muito baixa
        $this->assertNull($this->service->calcularIMC(70.0, 30.0));

        // Altura muito alta
        $this->assertNull($this->service->calcularIMC(70.0, 300.0));
    }

    /**
     * @group imc-classification-adult
     */
    public function test_obter_classificacao_imc_adulto(): void
    {
        // Baixo peso
        $result = $this->service->obterClassificacaoIMCAdulto(17.0);
        $this->assertEquals('underweight', $result['code']);
        $this->assertEquals('Baixo peso', $result['label']);

        // Peso normal
        $result = $this->service->obterClassificacaoIMCAdulto(22.0);
        $this->assertEquals('normal', $result['code']);
        $this->assertEquals('Peso normal', $result['label']);

        // Sobrepeso
        $result = $this->service->obterClassificacaoIMCAdulto(27.0);
        $this->assertEquals('overweight', $result['code']);
        $this->assertEquals('Sobrepeso', $result['label']);

        // Obesidade grau I
        $result = $this->service->obterClassificacaoIMCAdulto(32.0);
        $this->assertEquals('obesity_class_1', $result['code']);
        $this->assertEquals('Obesidade grau I', $result['label']);

        // Obesidade grau II
        $result = $this->service->obterClassificacaoIMCAdulto(37.0);
        $this->assertEquals('obesity_class_2', $result['code']);
        $this->assertEquals('Obesidade grau II', $result['label']);

        // Obesidade grau III
        $result = $this->service->obterClassificacaoIMCAdulto(45.0);
        $this->assertEquals('obesity_class_3', $result['code']);
        $this->assertEquals('Obesidade grau III (grave)', $result['label']);
    }

    /**
     * @group imc-classification-child
     */
    public function test_obter_classificacao_imc_crianca(): void
    {
        // Sem dados do banco, deve retornar no_reference_data
        $result = $this->service->obterClassificacaoIMCCrianca(16.0, 120, 'M');
        $this->assertEquals('no_reference_data', $result['code']);
        $this->assertEquals('Dados de referência não disponíveis', $result['label']);

        // Teste com sexo inválido
        $result = $this->service->obterClassificacaoIMCCrianca(16.0, 120, 'X');
        $this->assertEquals('no_reference_data', $result['code']);
        $this->assertEquals('Dados de referência não disponíveis', $result['label']);
    }

    /**
     * @group age-calculation
     */
    public function test_calcular_idade_em_meses(): void
    {
        $nascimento = new DateTime('2010-01-15');
        $referencia = new DateTime('2020-01-15');

        $idadeMeses = $this->service->calcularIdadeEmMeses($nascimento, $referencia);
        $this->assertEquals(120, $idadeMeses); // 10 anos = 120 meses
    }

    /**
     * @group age-calculation
     */
    public function test_calcular_idade_em_anos(): void
    {
        $nascimento = new DateTime('2010-01-15');
        $referencia = new DateTime('2020-01-15');

        $idadeAnos = $this->service->calcularIdadeEmAnos($nascimento, $referencia);
        $this->assertEquals(10, $idadeAnos);
    }

    /**
     * @group age-calculation
     */
    public function test_calcular_idade_com_strings(): void
    {
        $idadeMeses = $this->service->calcularIdadeEmMeses('2010-01-15', '2020-01-15');
        $this->assertEquals(120, $idadeMeses);

        $idadeAnos = $this->service->calcularIdadeEmAnos('2010-01-15', '2020-01-15');
        $this->assertEquals(10, $idadeAnos);
    }

    /**
     * @group waist-risk-adult
     */
    public function test_obter_classificacao_risco_circunferencia_cintura_adulto(): void
    {
        // Homem adulto - sem risco
        $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura(90.0, 'M', 25);
        $this->assertEquals('no_risk', $result['code']);
        $this->assertEquals('Sem risco', $result['label']);

        // Homem adulto - risco aumentado
        $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura(98.0, 'M', 25);
        $this->assertEquals('increased', $result['code']);
        $this->assertEquals('Risco aumentado', $result['label']);

        // Homem adulto - risco muito aumentado
        $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura(110.0, 'M', 25);
        $this->assertEquals('high', $result['code']);
        $this->assertEquals('Risco muito aumentado', $result['label']);

        // Mulher adulta - sem risco
        $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura(75.0, 'F', 25);
        $this->assertEquals('no_risk', $result['code']);
        $this->assertEquals('Sem risco', $result['label']);

        // Mulher adulta - risco aumentado
        $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura(85.0, 'F', 25);
        $this->assertEquals('increased', $result['code']);
        $this->assertEquals('Risco aumentado', $result['label']);

        // Mulher adulta - risco muito aumentado
        $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura(95.0, 'F', 25);
        $this->assertEquals('high', $result['code']);
        $this->assertEquals('Risco muito aumentado', $result['label']);
    }

    /**
     * @group waist-risk-child
     */
    public function test_obter_classificacao_risco_circunferencia_cintura_crianca(): void
    {
        // Sem dados do banco, deve retornar no_reference_data
        $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura(50.0, 'M', 8);
        $this->assertEquals('no_reference_data', $result['code']);
        $this->assertEquals('Dados de referência não disponíveis', $result['label']);

        // Mesmo com valor alto, sem dados do banco retorna no_reference_data
        $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura(70.0, 'M', 8);
        $this->assertEquals('no_reference_data', $result['code']);
        $this->assertEquals('Dados de referência não disponíveis', $result['label']);
    }

    /**
     * @group complete-evaluation
     */
    public function test_obter_avaliacao_completa_adulto(): void
    {
        $avaliacao = $this->service->obterAvaliacaoCompleta(
            peso: 70.0,
            altura: 175.0,
            circunferenciaCintura: 85.0,
            dataNascimento: '1990-01-01',
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

    /**
     * @group complete-evaluation
     */
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

    /**
     * @group edge-cases
     */
    public function test_obter_avaliacao_completa_com_dados_invalidos(): void
    {
        $avaliacao = $this->service->obterAvaliacaoCompleta(
            peso: -1.0, // peso inválido
            altura: 175.0,
            circunferenciaCintura: null,
            dataNascimento: '1990-01-01',
            sexo: 'X', // sexo inválido
            dataAvaliacao: '2024-01-01'
        );

        $this->assertNull($avaliacao['imc']);
        $this->assertEquals('not_evaluable', $avaliacao['imc_classificacao']['code']);
        $this->assertEquals('Não avaliável', $avaliacao['imc_classificacao']['label']);
    }

    /**
     * @group normalization
     */
    public function test_normalizacao_sexo(): void
    {
        // Teste através do método público que usa normalizarSexo internamente
        $avaliacao1 = $this->service->obterAvaliacaoCompleta(70.0, 175.0, null, '1990-01-01', 'MASCULINO');
        $this->assertEquals('M', $avaliacao1['sexo']);

        $avaliacao2 = $this->service->obterAvaliacaoCompleta(70.0, 175.0, null, '1990-01-01', 'FEMININO');
        $this->assertEquals('F', $avaliacao2['sexo']);

        $avaliacao3 = $this->service->obterAvaliacaoCompleta(70.0, 175.0, null, '1990-01-01', 'male');
        $this->assertEquals('M', $avaliacao3['sexo']);

        $avaliacao4 = $this->service->obterAvaliacaoCompleta(70.0, 175.0, null, '1990-01-01', 'female');
        $this->assertEquals('F', $avaliacao4['sexo']);
    }

    /**
     * @group z-score
     */
    public function test_calcular_escore_zimc(): void
    {
        // Sem dados do banco, deve retornar null
        $zScore = $this->service->calcularEscoreZIMC(16.8, 120, 'M');
        $this->assertNull($zScore);

        // Teste com sexo inválido
        $zScore = $this->service->calcularEscoreZIMC(16.8, 120, 'X');
        $this->assertNull($zScore);

        // Sem dados do banco, qualquer idade retorna null
        $zScore = $this->service->calcularEscoreZIMC(16.8, 60, 'M');
        $this->assertNull($zScore);

        // Sem dados do banco, qualquer valor retorna null
        $zScore = $this->service->calcularEscoreZIMC(25.0, 240, 'M');
        $this->assertNull($zScore);
    }

    /**
     * @group boundary-values
     */
    public function test_valores_limite_imc(): void
    {
        // Teste valores exatos dos limites de classificação

        // Limite baixo peso/normal (18.5)
        $result = $this->service->obterClassificacaoIMCAdulto(18.49);
        $this->assertEquals('underweight', $result['code']);

        $result = $this->service->obterClassificacaoIMCAdulto(18.5);
        $this->assertEquals('normal', $result['code']);

        // Limite normal/sobrepeso (25.0)
        $result = $this->service->obterClassificacaoIMCAdulto(24.99);
        $this->assertEquals('normal', $result['code']);

        $result = $this->service->obterClassificacaoIMCAdulto(25.0);
        $this->assertEquals('overweight', $result['code']);

        // Limite sobrepeso/obesidade I (30.0)
        $result = $this->service->obterClassificacaoIMCAdulto(29.99);
        $this->assertEquals('overweight', $result['code']);

        $result = $this->service->obterClassificacaoIMCAdulto(30.0);
        $this->assertEquals('obesity_class_1', $result['code']);
    }

    /**
     * @group performance
     */
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

    /**
     * @group integration
     */
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

    /**
     * @group data-types
     */
    public function test_tipos_de_dados_variados(): void
    {
        // Teste com strings numéricas
        $imc = $this->service->calcularIMC('70.5', '175.0');
        $this->assertEquals(23.02, $imc);

        // Teste com inteiros
        $imc = $this->service->calcularIMC(70, 175);
        $this->assertEquals(22.86, $imc);

        // Teste com floats
        $imc = $this->service->calcularIMC(70.5, 175.0);
        $this->assertEquals(23.02, $imc);
    }

    /**
     * @group edge-cases-extended
     */
    public function test_casos_limite_estendidos(): void
    {
        // Sem dados do banco, testes devem retornar no_reference_data

        // Teste com diferentes idades para verificar que não há dados de referência
        $result = $this->service->obterClassificacaoIMCCrianca(17.0, 132, 'M'); // 11 anos
        $this->assertEquals('no_reference_data', $result['code']);

        $result = $this->service->obterClassificacaoIMCCrianca(19.0, 156, 'F'); // 13 anos
        $this->assertEquals('no_reference_data', $result['code']);

        // Teste com circunferência sem dados de referência
        $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura(67.0, 'M', 10);
        $this->assertEquals('no_reference_data', $result['code']); // Sem dados do banco

        $result = $this->service->obterClassificacaoRiscoCircunferenciaCintura(66.9, 'M', 10);
        $this->assertEquals('no_reference_data', $result['code']); // Sem dados do banco
    }

    /**
     * @group data-validation
     */
    public function test_validacao_rigorosa_dados(): void
    {
        // Teste com dados muito próximos dos limites de validação

        // Peso mínimo válido
        $imc = $this->service->calcularIMC(5.0, 175.0);
        $this->assertEquals(1.63, $imc);

        // Peso máximo válido
        $imc = $this->service->calcularIMC(300.0, 175.0);
        $this->assertEquals(97.96, $imc);

        // Altura mínima válida
        $imc = $this->service->calcularIMC(70.0, 50.0);
        $this->assertEquals(280.0, $imc);

        // Altura máxima válida
        $imc = $this->service->calcularIMC(70.0, 250.0);
        $this->assertEquals(11.2, $imc);

        // Valores inválidos por 0.1
        $this->assertNull($this->service->calcularIMC(4.9, 175.0));
        $this->assertNull($this->service->calcularIMC(300.1, 175.0));
        $this->assertNull($this->service->calcularIMC(70.0, 49.9));
        $this->assertNull($this->service->calcularIMC(70.0, 250.1));
    }
}
