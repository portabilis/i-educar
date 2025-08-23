<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Traits\InterpolatesAnthropometricData;

class AnthropometricPercentile extends Model
{
    use InterpolatesAnthropometricData;
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
        'source',
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
     * Retorna todos os dados de percentis agrupados por idade e sexo para cache
     */
    public static function getAllGroupedForCache(string $source = 'WHO_2007'): array
    {
        return static::getCachedData('percentiles_data', $source, function () use ($source) {
            $data = static::where('source', $source)
                ->orderBy('age_months')
                ->get();

            $grouped = [];
            foreach ($data as $item) {
                $lmsData = static::convertToFloatArray($item, ['l_value' => 'L', 'm_value' => 'M', 's_value' => 'S']);
                $percentileData = static::convertToFloatArray($item, [
                    'p01' => 'p01', 'p1' => 'p1', 'p3' => 'p3', 'p5' => 'p5', 'p10' => 'p10',
                    'p15' => 'p15', 'p25' => 'p25', 'p50' => 'p50', 'p75' => 'p75', 'p85' => 'p85',
                    'p90' => 'p90', 'p95' => 'p95', 'p97' => 'p97', 'p99' => 'p99', 'p999' => 'p999'
                ]);
                
                $grouped[$item->age_months][$item->gender] = array_merge($lmsData, $percentileData);
            }

            return $grouped;
        });
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

            return static::interpolateValue(
                $ageMonths, 
                $lower->age_months, 
                $upper->age_months, 
                $lower->$percentile, 
                $upper->$percentile
            );
        }

        // Se só tem um lado, usa extrapolação
        $ref = $data['lower'] ?: $data['upper'];
        if ($ref) {
            return (float) $ref->$percentile;
        }

        return null;
    }
}
