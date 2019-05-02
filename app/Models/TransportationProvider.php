<?php

namespace App\Models;

use App\Contracts\Enum;

class TransportationProvider implements Enum
{
    const NONE = 0;
    const STATE = 1;
    const CITY = 2;

    public function getDescriptiveValues(): array
    {
        return [
            self::NONE => 'Não utiliza',
            self::STATE => 'Estadual',
            self::CITY => 'Municipal',
        ];
    }
}
