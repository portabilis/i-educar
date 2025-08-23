<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;

class AnthropometricZScore extends AbstractAnthropometricModel
{
    protected $table = 'pmieducar.anthropometric_z_scores';




    /**
     * Get Z-score specific fillable fields
     */
    protected function getSpecificFillable(): array
    {
        return [
            'sd4neg', 'sd3neg', 'sd2neg', 'sd1neg', 'sd0',
            'sd1', 'sd2', 'sd3', 'sd4'
        ];
    }

    /**
     * Get Z-score specific casts
     */
    protected function getSpecificCasts(): array
    {
        return [
            'sd4neg' => 'decimal:6', 'sd3neg' => 'decimal:6', 'sd2neg' => 'decimal:6',
            'sd1neg' => 'decimal:6', 'sd0' => 'decimal:6', 'sd1' => 'decimal:6',
            'sd2' => 'decimal:6', 'sd3' => 'decimal:6', 'sd4' => 'decimal:6'
        ];
    }

    /**
     * Get Z-score specific data for cache (empty for Z-scores - only LMS needed)
     */
    public function getSpecificDataForCache(): array
    {
        return []; // Z-scores only need LMS data in cache
    }


    /**
     * Calcula LMS interpolado para uma idade específica
     */
    public static function getInterpolatedLMS(int $ageMonths, string $gender, string $source = 'WHO_2007'): ?array
    {
        $data = static::getForInterpolation($ageMonths, $gender, $source);

        // Se tem dados exatos
        if ($data['exact']) {
            return static::convertToFloatArray(
                $data['exact'], 
                ['l_value' => 'L', 'm_value' => 'M', 's_value' => 'S']
            );
        }

        // Se tem dados para interpolação
        if ($data['lower'] && $data['upper']) {
            $lower = $data['lower'];
            $upper = $data['upper'];

            return [
                'L' => static::interpolateValue($ageMonths, $lower->age_months, $upper->age_months, $lower->l_value, $upper->l_value),
                'M' => static::interpolateValue($ageMonths, $lower->age_months, $upper->age_months, $lower->m_value, $upper->m_value),
                'S' => static::interpolateValue($ageMonths, $lower->age_months, $upper->age_months, $lower->s_value, $upper->s_value),
            ];
        }

        // Se só tem um lado, usa extrapolação
        $ref = $data['lower'] ?: $data['upper'];
        if ($ref) {
            return static::convertToFloatArray(
                $ref, 
                ['l_value' => 'L', 'm_value' => 'M', 's_value' => 'S']
            );
        }

        return null;
    }
}
