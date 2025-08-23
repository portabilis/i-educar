<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Traits\InterpolatesAnthropometricData;

class AnthropometricZScore extends Model
{
    use InterpolatesAnthropometricData;
    protected $table = 'pmieducar.anthropometric_z_scores';

    protected $fillable = [
        'age_months',
        'gender',
        'l_value',
        'm_value',
        's_value',
        // Z-score deviations only
        'sd4neg',
        'sd3neg',
        'sd2neg',
        'sd1neg',
        'sd0',
        'sd1',
        'sd2',
        'sd3',
        'sd4',
        'source',
    ];

    protected $casts = [
        'age_months' => 'integer',
        'l_value' => 'decimal:6',
        'm_value' => 'decimal:6',
        's_value' => 'decimal:6',
        // Z-score deviations
        'sd4neg' => 'decimal:6',
        'sd3neg' => 'decimal:6',
        'sd2neg' => 'decimal:6',
        'sd1neg' => 'decimal:6',
        'sd0' => 'decimal:6',
        'sd1' => 'decimal:6',
        'sd2' => 'decimal:6',
        'sd3' => 'decimal:6',
        'sd4' => 'decimal:6',
    ];


    /**
     * Retorna todos os dados LMS agrupados por idade e sexo para cache
     */
    public static function getAllGroupedForCache(string $source = 'WHO_2007'): array
    {
        return static::getCachedData('lms_data', $source, function () use ($source) {
            $data = static::where('source', $source)
                ->orderBy('age_months')
                ->get();

            $grouped = [];
            foreach ($data as $item) {
                $grouped[$item->age_months][$item->gender] = static::convertToFloatArray(
                    $item, 
                    ['l_value' => 'L', 'm_value' => 'M', 's_value' => 'S']
                );
            }

            return $grouped;
        });
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
