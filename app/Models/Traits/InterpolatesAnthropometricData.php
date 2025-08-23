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

    /**
     * Generic interpolated data getter - removes duplication between Z-scores and Percentiles
     */
    public static function getInterpolatedData(int $ageMonths, string $gender, array $fieldMapping, string $source = 'WHO_2007'): ?array
    {
        $data = static::getForInterpolation($ageMonths, $gender, $source);

        // Se tem dados exatos
        if ($data['exact']) {
            return static::convertToFloatArray($data['exact'], $fieldMapping);
        }

        // Se tem dados para interpolação completa
        if ($data['lower'] && $data['upper']) {
            return static::performInterpolation($data['lower'], $data['upper'], $ageMonths, $fieldMapping);
        }

        // Se só tem um lado, usa extrapolação
        $ref = $data['lower'] ?: $data['upper'];
        return $ref ? static::convertToFloatArray($ref, $fieldMapping) : null;
    }

    /**
     * Perform interpolation calculation
     */
    private static function performInterpolation($lower, $upper, int $ageMonths, array $fieldMapping): array
    {
        $result = [];
        foreach ($fieldMapping as $dbField => $outputKey) {
            $result[$outputKey] = static::interpolateValue(
                $ageMonths,
                $lower->age_months,
                $upper->age_months,
                $lower->$dbField,
                $upper->$dbField
            );
        }
        return $result;
    }

    /**
     * Get single interpolated field value
     */
    public static function getInterpolatedField(int $ageMonths, string $gender, string $field, string $source = 'WHO_2007'): ?float
    {
        $data = static::getForInterpolation($ageMonths, $gender, $source);

        // Se tem dados exatos
        if ($data['exact']) {
            return (float) $data['exact']->$field;
        }

        // Se tem dados para interpolação completa
        if ($data['lower'] && $data['upper']) {
            return static::interpolateValue(
                $ageMonths,
                $data['lower']->age_months,
                $data['upper']->age_months,
                $data['lower']->$field,
                $data['upper']->$field
            );
        }

        // Se só tem um lado, usa extrapolação
        $ref = $data['lower'] ?: $data['upper'];
        return $ref ? (float) $ref->$field : null;
    }
}
