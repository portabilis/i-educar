<?php

namespace App\Services;

use DateTime;

/**
 * Serviço antropométrico WHO 2007
 * - IMC e classificação (OMS)
 * - Z-score por idade/sexo via LMS (OMS 2007)
 * - Circunferência de cintura (adultos: pontos de corte fixos; pediatria: ≥P90)
 * - Retorno com código e rótulo (PT-BR)
 *
 * REQUER dados WHO 2007 carregados no banco de dados via:
 * - php artisan migrate (criar tabelas)
 * - php artisan anthropometric:load (carregar dados XLSX)
 * - $service->carregarReferenciasDoBanco() (usar dados)
 */
class AnthropometricService
{
    private AnthropometricCalculator $calculator;
    private AnthropometricDataLoader $dataLoader;

    public function __construct(
        ?AnthropometricCalculator $calculator = null,
        ?AnthropometricDataLoader $dataLoader = null
    ) {
        $this->calculator = $calculator ?? new AnthropometricCalculator();
        $this->dataLoader = $dataLoader ?? new AnthropometricDataLoader();
    }

    /**
     * Carrega referências do banco de dados (método preferencial).
     */
    public function carregarReferenciasDoBanco(string $source = 'WHO_2007'): bool
    {
        return $this->dataLoader->carregarReferenciasDoBanco($source);
    }

    /**
     * Verifica se está usando dados do banco de dados (não fallback).
     */
    public function isUsingDatabaseData(): bool
    {
        return $this->dataLoader->isUsingDatabaseData();
    }

    /** Calcula IMC (peso em kg, altura em cm) */
    public function calcularIMC(float $peso, float $altura): ?float
    {
        return $this->calculator->calcularIMC($peso, $altura);
    }

    /** Classificação do IMC em adultos (OMS) */
    public function obterClassificacaoIMCAdulto(float $imc): array
    {
        $classifications = [
            ['limit' => 18.5, 'code' => 'underweight', 'label' => 'Baixo peso'],
            ['limit' => 25.0, 'code' => 'normal', 'label' => 'Peso normal'],
            ['limit' => 30.0, 'code' => 'overweight', 'label' => 'Sobrepeso'],
            ['limit' => 35.0, 'code' => 'obesity_class_1', 'label' => 'Obesidade grau I'],
            ['limit' => 40.0, 'code' => 'obesity_class_2', 'label' => 'Obesidade grau II']
        ];

        foreach ($classifications as $classification) {
            if ($imc < $classification['limit']) {
                return ['code' => $classification['code'], 'label' => $classification['label']];
            }
        }

        return ['code' => 'obesity_class_3', 'label' => 'Obesidade grau III (grave)'];
    }

    /**
     * Classificação do IMC para crianças/adolescentes (5–19 anos) via Z-score OMS 2007
     */
    public function obterClassificacaoIMCCrianca(float $imc, int $idadeEmMeses, string $sexo): array
    {
        $validationResult = $this->validateChildIMCInputs($sexo);
        if ($validationResult !== null) {
            return $validationResult;
        }

        $sexoNorm = $this->calculator->normalizarSexo($sexo);
        $z = $this->calcularEscoreZIMC($imc, $idadeEmMeses, $sexoNorm);

        return $z === null
            ? ['code' => 'not_evaluable', 'label' => $this->calculator->getNotEvaluableLabel()]
            : $this->calculator->getChildIMCClassification($z);
    }

    /**
     * Validate inputs for child IMC classification
     */
    private function validateChildIMCInputs(string $sexo): ?array
    {
        if (!$this->dataLoader->isUsingDatabaseData()) {
            return ['code' => 'no_reference_data', 'label' => $this->calculator->getNoReferenceDataLabel()];
        }

        if ($this->calculator->normalizarSexo($sexo) === null) {
            return ['code' => 'not_evaluable', 'label' => $this->calculator->getNotEvaluableLabel()];
        }

        return null; // Valid inputs
    }

    /**
     * Z-score do IMC (OMS 2007) usando parâmetros LMS
     * Fórmula: Z = ((IMC/M)^L - 1) / (L*S)
     */
    public function calcularEscoreZIMC(float $imc, int $idadeEmMeses, string $sexo): ?float
    {
        $sexoNorm = $this->calculator->normalizarSexo($sexo);
        if ($sexoNorm === null) {
            return null;
        }

        $lms = $this->dataLoader->obterLMS($idadeEmMeses, $sexoNorm, $this->calculator);
        if ($lms === null || $lms['M'] <= 0 || $lms['S'] <= 0) {
            return null;
        }

        $z = $this->calculator->calculateZScore($imc, $lms);
        return round($z, 2);
    }

    /**
     * Classificação de risco por circunferência da cintura
     */
    public function obterClassificacaoRiscoCircunferenciaCintura(float $circCinturaCm, string $sexo, ?int $idadeAnos = null): array
    {
        $sexoNorm = $this->calculator->normalizarSexo($sexo);
        if ($sexoNorm === null) {
            return ['code' => 'not_evaluable', 'label' => $this->calculator->getNotEvaluableLabel()];
        }

        if ($idadeAnos === null || $idadeAnos >= 20) {
            return $this->obterRiscoCinturaAdulto($circCinturaCm, $sexoNorm);
        }

        if ($idadeAnos >= 5) {
            return $this->obterRiscoCinturaCrianca($circCinturaCm, $sexoNorm, $idadeAnos);
        }

        // Para crianças < 5 anos, não há padrões estabelecidos de cintura
        return ['code' => 'not_applicable_age', 'label' => 'Avaliação de cintura não aplicável para < 5 anos'];
    }

    private function obterRiscoCinturaAdulto(float $circCinturaCm, string $sexo): array
    {
        $thresholds = $sexo === 'M' ? [94, 102] : [80, 88];

        if ($circCinturaCm < $thresholds[0]) {
            return ['code' => 'no_risk', 'label' => 'Sem risco'];
        }

        if ($circCinturaCm < $thresholds[1]) {
            return ['code' => 'increased', 'label' => 'Risco aumentado'];
        }

        return ['code' => 'high', 'label' => 'Risco muito aumentado'];
    }

    private function obterRiscoCinturaCrianca(float $circCinturaCm, string $sexo, int $idadeAnos): array
    {
        if (!$this->dataLoader->isUsingDatabaseData()) {
            return ['code' => 'no_reference_data', 'label' => $this->calculator->getNoReferenceDataLabel()];
        }

        $p90 = $this->dataLoader->getP90($idadeAnos, $sexo, $this->calculator);
        if ($p90 === null) {
            return ['code' => 'no_reference_data', 'label' => $this->calculator->getNoReferenceDataLabel()];
        }

        $riskStatus = $circCinturaCm >= $p90 ? 'increased' : 'no_risk';
        $riskLabel = $circCinturaCm >= $p90 ? 'Risco aumentado (≥ P90)' : 'Sem risco (< P90)';

        return ['code' => $riskStatus, 'label' => $riskLabel, 'p90' => $p90];
    }

    /** Idade em meses (inteiros, como a WHO usa) */
    public function calcularIdadeEmMeses(string|DateTime $dataNascimento, string|DateTime|null $dataReferencia = null): int
    {
        return $this->calculator->calcularIdadeEmMeses($dataNascimento, $dataReferencia);
    }

    /** Idade em anos (inteiros) */
    public function calcularIdadeEmAnos(string|DateTime $dataNascimento, string|DateTime|null $dataReferencia = null): int
    {
        return $this->calculator->calcularIdadeEmAnos($dataNascimento, $dataReferencia);
    }

    /** Avaliação completa — estrutura consolidada */
    public function obterAvaliacaoCompleta(
        float $peso,
        float $altura,
        ?float $circunferenciaCintura,
        string|DateTime $dataNascimento,
        string $sexo,
        string|DateTime|null $dataAvaliacao = null
    ): array {
        $imc = $this->calcularIMC($peso, $altura);
        $idadeAnos = $this->calcularIdadeEmAnos($dataNascimento, $dataAvaliacao);
        $idadeMeses = $this->calcularIdadeEmMeses($dataNascimento, $dataAvaliacao);
        $sexoNorm = $this->calculator->normalizarSexo($sexo);

        // Validação de dados plausíveis por faixa etária
        $validationWarning = $this->validateMeasurementsByAge($peso, $altura, $idadeAnos);
        if ($validationWarning) {
            $validationWarning['dados_originais'] = [
                'peso_kg' => $peso,
                'altura_cm' => $altura,
                'idade_anos' => $idadeAnos
            ];
            return $validationWarning;
        }

        $out = [
            'peso_kg' => $peso,
            'altura_cm' => $altura,
            'imc' => $imc,
            'idade_anos' => $idadeAnos,
            'idade_meses' => $idadeMeses,
            'sexo' => $sexoNorm ?? $sexo,
        ];

        if ($imc !== null && $sexoNorm !== null) {
            if ($idadeAnos >= 20) {
                // Adultos (≥20 anos): classificação por IMC
                $out['imc_classificacao'] = $this->obterClassificacaoIMCAdulto($imc);
            } elseif ($idadeMeses >= 61) {
                // Crianças/adolescentes (5-19 anos): WHO 2007
                $out['imc_classificacao'] = $this->obterClassificacaoIMCCrianca($imc, $idadeMeses, $sexoNorm);
                $out['imc_zscore'] = $out['imc_classificacao']['z'] ?? $this->calcularEscoreZIMC($imc, $idadeMeses, $sexoNorm);
            } elseif ($idadeMeses >= 24) {
                // Crianças pequenas (2-5 anos): WHO 2006 - por enquanto não implementado
                $out['imc_classificacao'] = ['code' => 'not_available_who_2006', 'label' => 'Avaliação requer padrões WHO 2006 (2-5 anos) - não implementado'];
            } else {
                // Bebês (0-2 anos): não se avalia IMC
                $out['imc_classificacao'] = ['code' => 'not_applicable_age', 'label' => 'IMC não aplicável para < 2 anos'];
            }
        } else {
            $out['imc_classificacao'] = ['code' => 'not_evaluable', 'label' => $this->calculator->getNotEvaluableLabel()];
        }

        if ($circunferenciaCintura !== null) {
            $out['cintura_cm'] = $circunferenciaCintura;
            $out['cintura_classificacao'] = $this->obterClassificacaoRiscoCircunferenciaCintura(
                $circunferenciaCintura,
                $sexoNorm ?? $sexo,
                $idadeAnos
            );
        }

        return $out;
    }

    /**
     * Valida se as medidas são plausíveis para a idade
     */
    private function validateMeasurementsByAge(float $peso, float $altura, int $idadeAnos): ?array
    {
        // Limites aproximados por faixa etária (podem ser ajustados)
        $limits = [
            // [idade_min, idade_max, altura_max_cm, peso_max_kg]
            [0, 2, 95, 20],      // 0-2 anos
            [2, 5, 130, 35],     // 2-5 anos  
            [5, 12, 170, 80],    // 5-12 anos
            [12, 18, 200, 150],  // 12-18 anos
            [18, 120, 250, 300], // adultos
        ];

        foreach ($limits as [$idadeMin, $idadeMax, $alturaMax, $pesoMax]) {
            if ($idadeAnos >= $idadeMin && $idadeAnos < $idadeMax) {
                if ($altura > $alturaMax || $peso > $pesoMax) {
                    return [
                        'code' => 'implausible_measurements',
                        'label' => "Medidas implausíveis para idade {$idadeAnos} anos (altura: {$altura}cm, peso: {$peso}kg). Verifique os dados.",
                        'validacao_falhou' => true,
                        'limites_esperados' => [
                            'altura_max_cm' => $alturaMax,
                            'peso_max_kg' => $pesoMax,
                            'faixa_etaria' => "{$idadeMin}-{$idadeMax} anos"
                        ]
                    ];
                }
                break;
            }
        }

        return null;
    }
}
