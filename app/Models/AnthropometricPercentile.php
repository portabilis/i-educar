<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;

class AnthropometricPercentile extends AbstractAnthropometricModel
{
    protected $table = 'pmieducar.anthropometric_percentiles';




    /**
     * Retorna dados P90 formatados para uso como dados de cintura (compatibilidade)
     */
    public static function getAllP90ForWaistCache(string $source = 'WHO_2007'): array
    {
        return static::getCachedData('p90_waist_data', $source, function () use ($source) {
            $data = static::where('source', $source)
                ->whereNotNull('p90')
                ->orderBy('age_months')
                ->get();

            $grouped = [];
            foreach ($data as $item) {
                $ageYears = floor($item->age_months / 12);
                $grouped[$ageYears][$item->gender] = (float) $item->p90;
            }

            return $grouped;
        });
    }

    /**
     * Get percentile specific fillable fields
     */
    protected function getSpecificFillable(): array
    {
        return [
            'p01', 'p1', 'p3', 'p5', 'p10', 'p15', 'p25', 'p50',
            'p75', 'p85', 'p90', 'p95', 'p97', 'p99', 'p999'
        ];
    }

    /**
     * Get percentile specific casts
     */
    protected function getSpecificCasts(): array
    {
        return [
            'p01' => parent::DECIMAL_PRECISION, 'p1' => parent::DECIMAL_PRECISION, 'p3' => parent::DECIMAL_PRECISION,
            'p5' => parent::DECIMAL_PRECISION, 'p10' => parent::DECIMAL_PRECISION, 'p15' => parent::DECIMAL_PRECISION,
            'p25' => parent::DECIMAL_PRECISION, 'p50' => parent::DECIMAL_PRECISION, 'p75' => parent::DECIMAL_PRECISION,
            'p85' => parent::DECIMAL_PRECISION, 'p90' => parent::DECIMAL_PRECISION, 'p95' => parent::DECIMAL_PRECISION,
            'p97' => parent::DECIMAL_PRECISION, 'p99' => parent::DECIMAL_PRECISION, 'p999' => parent::DECIMAL_PRECISION
        ];
    }

    /**
     * Get percentile specific data for cache
     */
    public function getSpecificDataForCache(): array
    {
        return static::convertToFloatArray($this, [
            'p01' => 'p01', 'p1' => 'p1', 'p3' => 'p3', 'p5' => 'p5', 'p10' => 'p10',
            'p15' => 'p15', 'p25' => 'p25', 'p50' => 'p50', 'p75' => 'p75', 'p85' => 'p85',
            'p90' => 'p90', 'p95' => 'p95', 'p97' => 'p97', 'p99' => 'p99', 'p999' => 'p999'
        ]);
    }


    /**
     * Retorna percentil interpolado para uma idade específica
     */
    public static function getInterpolatedPercentile(int $ageMonths, string $gender, string $percentile = 'p50', string $source = 'WHO_2007'): ?float
    {
        return static::getInterpolatedField($ageMonths, $gender, $percentile, $source);
    }
}
