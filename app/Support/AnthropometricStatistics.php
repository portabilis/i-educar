<?php

namespace App\Support;

use App\Models\AnthropometricZScore;
use App\Models\AnthropometricPercentile;

class AnthropometricStatistics
{
    /**
     * Get all statistics data
     */
    public static function getAllStats(): array
    {
        return [
            'z_scores' => self::getZScoreStats(),
            'percentiles' => self::getPercentileStats(),
            'totals' => self::getTotalStats(),
            'age_ranges' => self::getAgeRanges(),
        ];
    }

    /**
     * Get Z-score statistics
     */
    public static function getZScoreStats(): array
    {
        return [
            'total' => AnthropometricZScore::count(),
            'boys' => AnthropometricZScore::where('gender', 'M')->count(),
            'girls' => AnthropometricZScore::where('gender', 'F')->count(),
        ];
    }

    /**
     * Get percentile statistics
     */
    public static function getPercentileStats(): array
    {
        return [
            'total' => AnthropometricPercentile::count(),
            'boys' => AnthropometricPercentile::where('gender', 'M')->count(),
            'girls' => AnthropometricPercentile::where('gender', 'F')->count(),
        ];
    }

    /**
     * Get total statistics
     */
    public static function getTotalStats(): array
    {
        $zScoreCount = AnthropometricZScore::count();
        $percentileCount = AnthropometricPercentile::count();

        return [
            'z_scores' => $zScoreCount,
            'percentiles' => $percentileCount,
            'waist_p90' => $percentileCount, // P90 via percentiles
            'grand_total' => $zScoreCount + $percentileCount,
        ];
    }

    /**
     * Get age ranges for both data types
     */
    public static function getAgeRanges(): array
    {
        $ranges = [];

        if (AnthropometricZScore::count() > 0) {
            $zScoreRange = AnthropometricZScore::selectRaw('MIN(age_months) as min_age, MAX(age_months) as max_age')->first();
            $ranges['z_scores'] = [
                'min_months' => $zScoreRange->min_age,
                'max_months' => $zScoreRange->max_age,
                'min_years' => floor($zScoreRange->min_age / 12),
                'max_years' => floor($zScoreRange->max_age / 12),
            ];
        }

        if (AnthropometricPercentile::count() > 0) {
            $percentileRange = AnthropometricPercentile::selectRaw('MIN(age_months) as min_age, MAX(age_months) as max_age')->first();
            $ranges['percentiles'] = [
                'min_months' => $percentileRange->min_age,
                'max_months' => $percentileRange->max_age,
                'min_years' => floor($percentileRange->min_age / 12),
                'max_years' => floor($percentileRange->max_age / 12),
            ];
        }

        return $ranges;
    }

    /**
     * Generate table data for console output
     */
    public static function getTableData(): array
    {
        $stats = self::getAllStats();

        return [
            ['Z-scores Total', $stats['z_scores']['total'], 'Registros de Z-scores para cálculo BMI'],
            ['├─ Meninos Z-score', $stats['z_scores']['boys'], 'Z-scores masculinos'],
            ['└─ Meninas Z-score', $stats['z_scores']['girls'], 'Z-scores femininos'],
            ['Percentis Total', $stats['percentiles']['total'], 'Registros de percentis BMI'],
            ['├─ Meninos Percentis', $stats['percentiles']['boys'], 'Percentis masculinos'],
            ['└─ Meninas Percentis', $stats['percentiles']['girls'], 'Percentis femininos'],
            ['P90 Cintura', $stats['totals']['waist_p90'], 'P90 da circunferência (via percentis)'],
            ['Total Geral', $stats['totals']['grand_total'], 'Todos os registros carregados'],
        ];
    }

    /**
     * Generate age range info messages
     */
    public static function getAgeRangeMessages(): array
    {
        $ranges = self::getAgeRanges();
        $messages = [];

        if (isset($ranges['z_scores'])) {
            $z = $ranges['z_scores'];
            $messages[] = "Faixa etária Z-scores: {$z['min_months']} - {$z['max_months']} meses ({$z['min_years']} - {$z['max_years']} anos)";
        }

        if (isset($ranges['percentiles'])) {
            $p = $ranges['percentiles'];
            $messages[] = "Faixa etária Percentis: {$p['min_months']} - {$p['max_months']} meses ({$p['min_years']} - {$p['max_years']} anos)";
            $messages[] = 'P90 Cintura: Disponível via percentis (mesma faixa etária)';
        }

        return $messages;
    }

    /**
     * Check data completeness
     */
    public static function getCompletenessStatus(): array
    {
        $totals = self::getTotalStats();
        $hasZScores = $totals['z_scores'] > 0;
        $hasPercentiles = $totals['percentiles'] > 0;
        
        if ($hasZScores && $hasPercentiles) {
            return ['status' => 'complete', 'message' => 'Dados completos: Z-scores E Percentis carregados em tabelas separadas'];
        }
        
        if ($hasZScores || $hasPercentiles) {
            $missing = $hasZScores ? 'percentis' : 'Z-scores';
            $existing = $hasZScores ? 'Z-scores' : 'Percentis';
            return ['status' => 'partial', 'message' => "Apenas {$existing} carregados. Faltam {$missing}."];
        }
        
        return ['status' => 'empty', 'message' => 'Nenhum dado antropométrico carregado'];
    }
}
