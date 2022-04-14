<?php

namespace App\Models;

use App\Contracts\Enum;

class LocalizationZone implements Enum
{
    public const URBAN = 1;
    public const RURAL = 2;

    public function getDescriptiveValues(): array
    {
        return [
            self::URBAN => 'Urbana',
            self::RURAL => 'Rural',
        ];
    }
}
