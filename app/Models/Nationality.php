<?php

namespace App\Models;

use App\Contracts\Enum;

class Nationality implements Enum
{
    public const BRAZILIAN = 1;
    public const NATURALIZED_BRAZILIAN = 2;
    public const FOREIGN = 3;

    public function getDescriptiveValues(): array
    {
        return [
            self::BRAZILIAN => 'Brasileira',
            self::NATURALIZED_BRAZILIAN => 'Naturalizado brasileiro',
            self::FOREIGN => 'Estrangeira',
        ];
    }
}
