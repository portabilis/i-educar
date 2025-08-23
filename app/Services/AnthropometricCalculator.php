<?php

namespace App\Services;

use DateTime;

/**
 * Calculadora de métricas antropométricas
 * Responsável pelos cálculos matemáticos e interpolações
 */
class AnthropometricCalculator
{
    /**
     * Constants for repeated literal strings
     */
    private const NO_REFERENCE_DATA_LABEL = 'Dados de referência não disponíveis';
    private const NOT_EVALUABLE_LABEL = 'Não avaliável';

    /**
     * Validate IMC calculation parameters
     */
    public function isValidIMCParameters(float $peso, float $altura): bool
    {
        return $peso > 0 && $altura > 0 &&
               $peso >= 5 && $peso <= 300 &&
               $altura >= 50 && $altura <= 250;
    }

    /** Calcula IMC (peso em kg, altura em cm) */
    public function calcularIMC(float $peso, float $altura): ?float
    {
        if (!$this->isValidIMCParameters($peso, $altura)) {
            return null;
        }

        $m = $altura / 100.0;
        return round($peso / ($m * $m), 2);
    }

    /**
     * Get child IMC classification based on Z-score
     */
    public function getChildIMCClassification(float $z): array
    {
        if ($z < -3) {
            return ['code' => 'severe_thinness', 'label' => 'Magreza acentuada', 'z' => $z];
        }

        if ($z < -2) {
            return ['code' => 'thinness', 'label' => 'Magreza', 'z' => $z];
        }

        // Consolidate remaining classifications
        return $this->getIMCClassificationForPositiveZScore($z);
    }

    /**
     * Get IMC classification for Z-scores >= -2
     */
    private function getIMCClassificationForPositiveZScore(float $z): array
    {
        if ($z <= 1) {
            return ['code' => 'normal', 'label' => 'Eutrofia', 'z' => $z];
        }
        if ($z <= 2) {
            return ['code' => 'overweight_risk', 'label' => 'Risco de sobrepeso', 'z' => $z];
        }

        $code = $z <= 3 ? 'obesity' : 'severe_obesity';
        $label = $z <= 3 ? 'Obesidade' : 'Obesidade grave';
        return ['code' => $code, 'label' => $label, 'z' => $z];
    }

    /**
     * Calculate Z-score using LMS method
     */
    public function calculateZScore(float $imc, array $lms): float
    {
        [$L, $M, $S] = [$lms['L'], $lms['M'], $lms['S']];

        return ($L == 0.0)
            ? (log($imc / $M) / $S)                               // caso limite quando L≈0
            : ((pow(($imc / $M), $L) - 1.0) / ($L * $S));
    }

    /** Normaliza sexo para 'M' ou 'F' */
    public function normalizarSexo(string $sexo): ?string
    {
        $s = strtoupper(trim($sexo));

        $genderMap = [
            'M' => 'M', 'F' => 'F',
            'MALE' => 'M', 'MASCULINO' => 'M',
            'FEMALE' => 'F', 'FEMININO' => 'F'
        ];

        return $genderMap[$s] ?? null;
    }

    /**
     * Process reference date avoiding nested ternary operations
     */
    public function processReferenceDate(string|DateTime|null $dataReferencia): DateTime
    {
        if (!$dataReferencia) {
            return new DateTime();
        }

        return $dataReferencia instanceof DateTime ? clone $dataReferencia : new DateTime($dataReferencia);
    }

    /** Idade em meses (inteiros, como a WHO usa) */
    public function calcularIdadeEmMeses(string|DateTime $dataNascimento, string|DateTime|null $dataReferencia = null): int
    {
        $dn = $dataNascimento instanceof DateTime ? clone $dataNascimento : new DateTime($dataNascimento);
        $ref = $this->processReferenceDate($dataReferencia);
        $i = $dn->diff($ref);

        return ($i->y * 12) + $i->m;
    }

    /** Idade em anos (inteiros) */
    public function calcularIdadeEmAnos(string|DateTime $dataNascimento, string|DateTime|null $dataReferencia = null): int
    {
        $dn = $dataNascimento instanceof DateTime ? clone $dataNascimento : new DateTime($dataNascimento);
        $ref = $this->processReferenceDate($dataReferencia);

        return $dn->diff($ref)->y;
    }

    /**
     * Interpolação linear simples entre dois valores
     */
    public function interpolateValue(float $a, float $b, float $t): float
    {
        return $a + ($b - $a) * $t;
    }

    /**
     * Encontra as idades que cercam a idade alvo para interpolação
     */
    public function findBoundingAges(array $idades, int $targetAge): array
    {
        sort($idades);
        $menor = null;
        $maior = null;

        foreach ($idades as $idade) {
            if ($idade < $targetAge) {
                $menor = $idade;
            } elseif ($idade > $targetAge) {
                $maior = $idade;
                break;
            }
        }

        return [$menor, $maior];
    }

    /**
     * Process interpolation bounds and return appropriate result
     */
    public function processInterpolationBounds(array $dataSet, ?int $menor, ?int $maior, string $sexo): mixed
    {
        if ($menor === null && $maior === null) {
            return null;
        }

        if ($menor === null || $maior === null) {
            $age = $menor ?? $maior;
            return $dataSet[$age][$sexo] ?? null;
        }

        return null; // Continue with normal interpolation
    }

    /**
     * Perform interpolation between two data points
     */
    public function performDataSetInterpolation(array $dataSet, int $menor, int $maior, int $targetAge, string $sexo, callable $interpolateCallback): mixed
    {
        $a = $dataSet[$menor][$sexo] ?? null;
        $b = $dataSet[$maior][$sexo] ?? null;

        if (!$a || !$b) {
            return null;
        }

        $t = ($targetAge - $menor) / ($maior - $menor);
        return $interpolateCallback($a, $b, $t);
    }

    public function getNoReferenceDataLabel(): string
    {
        return self::NO_REFERENCE_DATA_LABEL;
    }

    public function getNotEvaluableLabel(): string
    {
        return self::NOT_EVALUABLE_LABEL;
    }
}