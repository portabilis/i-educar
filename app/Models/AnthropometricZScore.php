<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AnthropometricZScore extends Model
{
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
     * Busca dados LMS para uma idade e sexo específicos
     */
    public static function getForAge(int $ageMonths, string $gender, string $source = 'WHO_2007'): ?self
    {
        return static::where('age_months', $ageMonths)
            ->where('gender', strtoupper($gender))
            ->where('source', $source)
            ->first();
    }

    /**
     * Retorna todos os dados LMS agrupados por idade e sexo para cache
     */
    public static function getAllGroupedForCache(string $source = 'WHO_2007'): array
    {
        $cacheKey = "anthropometric_z_scores_data_{$source}";

        return Cache::remember($cacheKey, 3600, function () use ($source) {
            $data = static::where('source', $source)
                ->orderBy('age_months')
                ->get();

            $grouped = [];
            foreach ($data as $item) {
                $grouped[$item->age_months][$item->gender] = [
                    'L' => (float) $item->l_value,
                    'M' => (float) $item->m_value,
                    'S' => (float) $item->s_value,
                ];
            }

            return $grouped;
        });
    }

    /**
     * Busca dados para interpolação
     */
    public static function getForInterpolation(int $ageMonths, string $gender, string $source = 'WHO_2007'): array
    {
        $gender = strtoupper($gender);

        // Busca a idade exata
        $exact = static::getForAge($ageMonths, $gender, $source);
        if ($exact) {
            return [
                'exact' => $exact,
                'lower' => null,
                'upper' => null,
            ];
        }

        // Busca idades para interpolação
        $lower = static::where('age_months', '<', $ageMonths)
            ->where('gender', $gender)
            ->where('source', $source)
            ->orderBy('age_months', 'desc')
            ->first();

        $upper = static::where('age_months', '>', $ageMonths)
            ->where('gender', $gender)
            ->where('source', $source)
            ->orderBy('age_months', 'asc')
            ->first();

        return [
            'exact' => null,
            'lower' => $lower,
            'upper' => $upper,
        ];
    }

    /**
     * Calcula LMS interpolado para uma idade específica
     */
    public static function getInterpolatedLMS(int $ageMonths, string $gender, string $source = 'WHO_2007'): ?array
    {
        $data = static::getForInterpolation($ageMonths, $gender, $source);

        // Se tem dados exatos
        if ($data['exact']) {
            return [
                'L' => (float) $data['exact']->l_value,
                'M' => (float) $data['exact']->m_value,
                'S' => (float) $data['exact']->s_value,
            ];
        }

        // Se tem dados para interpolação
        if ($data['lower'] && $data['upper']) {
            $lower = $data['lower'];
            $upper = $data['upper'];

            $t = ($ageMonths - $lower->age_months) / ($upper->age_months - $lower->age_months);

            return [
                'L' => (float) $lower->l_value + ((float) $upper->l_value - (float) $lower->l_value) * $t,
                'M' => (float) $lower->m_value + ((float) $upper->m_value - (float) $lower->m_value) * $t,
                'S' => (float) $lower->s_value + ((float) $upper->s_value - (float) $lower->s_value) * $t,
            ];
        }

        // Se só tem um lado, usa extrapolação
        $ref = $data['lower'] ?: $data['upper'];
        if ($ref) {
            return [
                'L' => (float) $ref->l_value,
                'M' => (float) $ref->m_value,
                'S' => (float) $ref->s_value,
            ];
        }

        return null;
    }
}
