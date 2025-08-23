<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AnthropometricPercentile extends Model
{
    protected $table = 'pmieducar.anthropometric_percentiles';
    
    protected $fillable = [
        'age_months',
        'gender',
        'l_value',
        'm_value',
        's_value',
        // Percentiles only
        'p01',
        'p1',
        'p3',
        'p5',
        'p10',
        'p15',
        'p25',
        'p50',
        'p75',
        'p85',
        'p90',
        'p95',
        'p97',
        'p99',
        'p999',
        'source'
    ];

    protected $casts = [
        'age_months' => 'integer',
        'l_value' => 'decimal:6',
        'm_value' => 'decimal:6',
        's_value' => 'decimal:6',
        // Percentiles
        'p01' => 'decimal:6',
        'p1' => 'decimal:6',
        'p3' => 'decimal:6',
        'p5' => 'decimal:6',
        'p10' => 'decimal:6',
        'p15' => 'decimal:6',
        'p25' => 'decimal:6',
        'p50' => 'decimal:6',
        'p75' => 'decimal:6',
        'p85' => 'decimal:6',
        'p90' => 'decimal:6',
        'p95' => 'decimal:6',
        'p97' => 'decimal:6',
        'p99' => 'decimal:6',
        'p999' => 'decimal:6',
    ];

    /**
     * Busca dados de percentis para uma idade e sexo específicos
     */
    public static function getForAge(int $ageMonths, string $gender, string $source = 'WHO_2007'): ?self
    {
        return static::where('age_months', $ageMonths)
                    ->where('gender', strtoupper($gender))
                    ->where('source', $source)
                    ->first();
    }

    /**
     * Retorna dados P90 formatados para uso como dados de cintura (compatibilidade)
     */
    public static function getAllP90ForWaistCache(string $source = 'WHO_2007'): array
    {
        $cacheKey = "anthropometric_p90_waist_data_{$source}";
        
        return Cache::remember($cacheKey, 3600, function () use ($source) {
            $data = static::where('source', $source)
                         ->whereNotNull('p90')
                         ->orderBy('age_months')
                         ->get();
            
            $grouped = [];
            foreach ($data as $item) {
                $ageYears = floor($item->age_months / 12); // Converter meses para anos
                $grouped[$ageYears][$item->gender] = (float) $item->p90;
            }
            
            return $grouped;
        });
    }

    /**
     * Retorna todos os dados de percentis agrupados por idade e sexo para cache
     */
    public static function getAllGroupedForCache(string $source = 'WHO_2007'): array
    {
        $cacheKey = "anthropometric_percentiles_data_{$source}";
        
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
                    // Percentiles
                    'p01' => (float) $item->p01,
                    'p1' => (float) $item->p1,
                    'p3' => (float) $item->p3,
                    'p5' => (float) $item->p5,
                    'p10' => (float) $item->p10,
                    'p15' => (float) $item->p15,
                    'p25' => (float) $item->p25,
                    'p50' => (float) $item->p50,
                    'p75' => (float) $item->p75,
                    'p85' => (float) $item->p85,
                    'p90' => (float) $item->p90,
                    'p95' => (float) $item->p95,
                    'p97' => (float) $item->p97,
                    'p99' => (float) $item->p99,
                    'p999' => (float) $item->p999,
                ];
            }
            
            return $grouped;
        });
    }

    /**
     * Busca dados para interpolação por idade
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
                'upper' => null
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
            'upper' => $upper
        ];
    }

    /**
     * Retorna percentil interpolado para uma idade específica
     */
    public static function getInterpolatedPercentile(int $ageMonths, string $gender, string $percentile = 'p50', string $source = 'WHO_2007'): ?float
    {
        $data = static::getForInterpolation($ageMonths, $gender, $source);
        
        // Se tem dados exatos
        if ($data['exact']) {
            return (float) $data['exact']->$percentile;
        }
        
        // Se tem dados para interpolação
        if ($data['lower'] && $data['upper']) {
            $lower = $data['lower'];
            $upper = $data['upper'];
            
            $t = ($ageMonths - $lower->age_months) / ($upper->age_months - $lower->age_months);
            
            return (float) $lower->$percentile + ((float) $upper->$percentile - (float) $lower->$percentile) * $t;
        }
        
        // Se só tem um lado, usa extrapolação
        $ref = $data['lower'] ?: $data['upper'];
        if ($ref) {
            return (float) $ref->$percentile;
        }
        
        return null;
    }
}