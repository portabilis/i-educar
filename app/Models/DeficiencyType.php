<?php

namespace App\Models;

class DeficiencyType
{
    public const DEFICIENCY = 1;

    public const DISORDER = 2;

    public static function getDescriptiveValues(): array
    {
        return [
            self::DEFICIENCY => 'Deficiência',
            self::DISORDER => 'Transtorno',
        ];
    }

    public static function getValueDescription($value)
    {
        return match ((int) $value) {
            self::DEFICIENCY => 'Deficiência',
            self::DISORDER => 'Transtorno',
        };
    }
}
