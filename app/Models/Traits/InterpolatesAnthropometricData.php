<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Cache;

trait InterpolatesAnthropometricData
{
    /**
     * Busca dados para uma idade e sexo específicos
     */
    public static function getForAge(int $ageMonths, string $gender, string $source = 'WHO_2007'): ?self
    {
        return static::where('age_months', $ageMonths)
            ->where('gender', strtoupper($gender))
            ->where('source', $source)
            ->first();
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
     * Executa interpolação linear entre dois valores
     */
    protected static function interpolateValue(float $targetAge, float $lowerAge, float $upperAge, float $lowerValue, float $upperValue): float
    {
        $t = ($targetAge - $lowerAge) / ($upperAge - $lowerAge);
        return $lowerValue + ($upperValue - $lowerValue) * $t;
    }

    /**
     * Aplica cache com chave baseada na classe atual
     */
    protected static function getCachedData(string $cacheKeySuffix, string $source, callable $dataLoader): array
    {
        $className = strtolower(class_basename(static::class));
        $cacheKey = "anthropometric_{$className}_{$cacheKeySuffix}_{$source}";
        
        return Cache::remember($cacheKey, 3600, $dataLoader);
    }

    /**
     * Converte dados do modelo para array float
     */
    protected static function convertToFloatArray($item, array $fieldMapping): array
    {
        $result = [];
        foreach ($fieldMapping as $sourceField => $targetField) {
            $result[$targetField] = (float) $item->$sourceField;
        }
        return $result;
    }
}