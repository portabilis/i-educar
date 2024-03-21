<?php

namespace App\Services\Reports;

use Illuminate\Support\Collection;

class Util
{
    public static function sumTimes(array|Collection|null $times): string
    {
        if (empty($times)) {
            return '00:00';
        }

        $minutes = 0;
        foreach ($times as $time) {
            [$hour, $minute] = explode(':', $time);
            $minutes += $hour * 60;
            $minutes += $minute;
        }
        $hours = floor($minutes / 60);
        $minutes -= $hours * 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public static function formatWorkload(?float $workload): string
    {
        if ($workload) {
            $hour = (int) $workload;
            $workload -= $hour;
            $minutes = round($workload * 60);
            if ($minutes < 10) {
                $minutes = '0' . $minutes;
            }

            if ($hour < 10) {
                $hour = '0' . $hour;
            }

            return $hour . ':' . $minutes;
        }

        return '00:00';
    }

    public static function format(mixed $value, ?int $decimalPlaces = null): string
    {
        return number_format($value, $decimalPlaces ?? 1, ',', '.');
    }

    public static function float(mixed $value): float
    {
        return (float) str_replace(',', '.', $value);
    }

    public static function moduleName(?Collection $modules = null): array
    {
        if ($modules === null) {
            return [
                [
                    'step' => 'An',
                    'name' => 'ANUAL',
                ],
            ];
        }

        return $modules->map(static fn ($module) => match ($modules->count()) {
            1 => [
                'step' => $module,
                'name' => 'ANUAL',
                'abbreviation' => 'ANUAL',
            ],
            2 => [
                'step' => $module,
                'name' => "{$module}° SEMESTRE",
                'abbreviation' => "{$module}° SEM.",
            ],
            3 => [
                'step' => $module,
                'name' => "{$module}° TRIMESTRE",
                'abbreviation' => "{$module}° TRIM.",
            ],
            4 => [
                'step' => $module,
                'name' => "{$module}° BIMESTRE",
                'abbreviation' => "{$module}° BIM.",
            ],
            default => [],
        })->toArray();
    }

    public static function mask(string|int $val, string $mask): string
    {
        $maskared = '';
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) {
                    $maskared .= $val[$k++];
                }
            } else {
                if (isset($mask[$i])) {
                    $maskared .= $mask[$i];
                }
            }
        }

        return $maskared;
    }

    public static function formatPostcode(?string $postcode): ?string
    {
        if ($postcode && strlen($postcode) === 8) {
            return self::mask($postcode, '#####-###');
        }

        return $postcode;
    }
}
