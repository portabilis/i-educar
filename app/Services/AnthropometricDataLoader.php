<?php

namespace App\Services;

/**
 * Carregador de dados antropométricos de referência
 * Responsável por carregar e cache dos dados do banco
 */
class AnthropometricDataLoader
{
    /** Dados LMS e P90 carregados do banco de dados */
    private array $lms = [];
    private array $waistP90 = [];

    /** Cache de dados carregados do banco */
    private array $lmsCache = [];
    private array $waistCache = [];

    private string $currentSource = 'WHO_2007';
    private bool $usingDatabaseData = false;

    /**
     * Carrega referências do banco de dados (método preferencial).
     * Usa cache para melhor performance.
     */
    public function carregarReferenciasDoBanco(string $source = 'WHO_2007'): bool
    {
        try {
            $this->currentSource = $source;

            $hasLmsData = false;
            $hasWaistData = false;

            // Carregar dados Z-scores do banco
            if (class_exists('\\App\\Models\\AnthropometricZScore')) {
                $this->lmsCache = \App\Models\AnthropometricZScore::getAllGroupedForCache($source);
                if (!empty($this->lmsCache)) {
                    $this->lms = $this->lmsCache;
                    $hasLmsData = true;
                }
            }

            // Carregar dados de cintura P90 da tabela de percentis
            if (class_exists('\\App\\Models\\AnthropometricPercentile')) {
                $this->waistCache = \App\Models\AnthropometricPercentile::getAllP90ForWaistCache($source);
                if (!empty($this->waistCache)) {
                    $this->waistP90 = $this->waistCache;
                    $hasWaistData = true;
                }
            }

            // Só marcar como carregado se realmente veio do banco
            $this->usingDatabaseData = $hasLmsData || $hasWaistData;

            return $this->usingDatabaseData;

        } catch (\Exception $e) {
            // Log do erro mas não falha - usa dados de fallback
            if (function_exists('logger')) {
                logger()->warning('Falha ao carregar dados antropométricos do banco', [
                    'source' => $source,
                    'error' => $e->getMessage(),
                ]);
            }
            $this->usingDatabaseData = false;

            return false;
        }
    }

    /**
     * Verifica se está usando dados do banco de dados (não fallback).
     */
    public function isUsingDatabaseData(): bool
    {
        return $this->usingDatabaseData;
    }

    /** Retorna LMS interpolado para idade/sexo ou null */
    public function obterLMS(int $idadeMeses, string $sexo, AnthropometricCalculator $calculator): ?array
    {
        $sexo = strtoupper($sexo);
        if (!in_array($sexo, ['M', 'F'], true)) {
            return null;
        }

        // Usar dados carregados do banco - se não há dados, retorna null
        if (isset($this->lms[$idadeMeses][$sexo])) {
            return $this->lms[$idadeMeses][$sexo];
        }

        return $this->interpolateFromDataSet($this->lms, $idadeMeses, $sexo, $calculator, function($a, $b, $t, $calc) {
            return [
                'L' => $calc->interpolateValue($a['L'], $b['L'], $t),
                'M' => $calc->interpolateValue($a['M'], $b['M'], $t),
                'S' => $calc->interpolateValue($a['S'], $b['S'], $t)
            ];
        });
    }

    /** Retorna P90 interpolado para idade/sexo a partir dos dados do banco */
    public function getP90(int $idadeAnos, string $sexo, AnthropometricCalculator $calculator): ?float
    {
        $sexo = strtoupper($sexo);
        if (!in_array($sexo, ['M', 'F'], true)) {
            return null;
        }

        // Usar dados P90 carregados do banco - se não há dados, retorna null
        if (isset($this->waistP90[$idadeAnos][$sexo])) {
            return (float) $this->waistP90[$idadeAnos][$sexo];
        }

        return $this->interpolateFromDataSet($this->waistP90, $idadeAnos, $sexo, $calculator, function($a, $b, $t, $calc) {
            return $calc->interpolateValue((float) $a, (float) $b, $t);
        });
    }

    /**
     * Método genérico para interpolação de dados de qualquer dataset
     */
    private function interpolateFromDataSet(array $dataSet, int $targetAge, string $sexo, AnthropometricCalculator $calculator, callable $interpolateCallback): mixed
    {
        $idades = array_keys($dataSet);
        if (empty($idades)) {
            return null;
        }

        [$menor, $maior] = $calculator->findBoundingAges($idades, $targetAge);

        // Check boundary conditions first
        $boundaryResult = $calculator->processInterpolationBounds($dataSet, $menor, $maior, $sexo);
        if ($boundaryResult !== null || $menor === null || $maior === null) {
            return $boundaryResult;
        }

        // Perform interpolation with validation
        return $calculator->performDataSetInterpolation($dataSet, $menor, $maior, $targetAge, $sexo, function($a, $b, $t) use ($calculator, $interpolateCallback) {
            return $interpolateCallback($a, $b, $t, $calculator);
        });
    }
}
