<?php

namespace App\Models;

class DeficiencyType
{
    public const DEFICIENCY = 1;

    public const DISORDER = 2;

    /**
     * @return array<int, string>
     */
    public static function getDescriptiveValues(): array
    {
        return [
            self::DEFICIENCY => 'Deficiência',
            self::DISORDER => 'Transtorno',
        ];
    }

    public static function getValueDescription(int $value): string
    {
        /** @phpstan-ignore-next-line */
        return match ($value) {
            self::DEFICIENCY => 'Deficiência',
            self::DISORDER => 'Transtorno',
        };
    }
}
