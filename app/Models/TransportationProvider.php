<?php

namespace App\Models;

use App\Contracts\Enum;

class TransportationProvider implements Enum
{
    public const NONE = 0;

    public const STATE = 1;

    public const CITY = 2;

    public function getDescriptiveValues(): array
    {
        return [
            self::NONE => 'Não utiliza',
            self::STATE => 'Estadual',
            self::CITY => 'Municipal',
        ];
    }

    public function from($value): int
    {
        return match ($value) {
            'municipal' => self::CITY,
            'estadual' => self::STATE,
            default => self::NONE,
        };
    }

    public function getValueDescription($value)
    {
        return match ((int) $value) {
            self::CITY => 'municipal',
            self::STATE => 'estadual',
            default => 'nenhum',
        };
    }
}
