<?php

namespace Database\Seeders;

use App\Models\AnthropometricPercentile;

class AnthropometricPercentileGirlsSeeder extends AbstractAnthropometricSeeder
{
    protected $filename = __DIR__ . '/../xls/anthro/bmi-girls-perc-who2007-exp.xlsx';
    protected $model = AnthropometricPercentile::class;
    protected string $gender = 'F';
    protected string $typeDescription = 'percentis';

    /**
     * Get percentile specific fields
     */
    protected function getSpecificFields(array $data): array
    {
        return [
            'p01' => $data['P01'] ?? null,
            'p1' => $data['P1'] ?? null,
            'p3' => $data['P3'] ?? null,
            'p5' => $data['P5'] ?? null,
            'p10' => $data['P10'] ?? null,
            'p15' => $data['P15'] ?? null,
            'p25' => $data['P25'] ?? null,
            'p50' => $data['P50'] ?? null,
            'p75' => $data['P75'] ?? null,
            'p85' => $data['P85'] ?? null,
            'p90' => $data['P90'] ?? null,
            'p95' => $data['P95'] ?? null,
            'p97' => $data['P97'] ?? null,
            'p99' => $data['P99'] ?? null,
            'p999' => $data['P999'] ?? null,
        ];
    }
}
