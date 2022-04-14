<?php

namespace App\Models;

use App\Contracts\Enum;

class Gender implements Enum
{
    public const MALE = 1;
    public const FEMALE = 2;

    public function getDescriptiveValues(): array
    {
        return [
            self::MALE => 'Masculino',
            self::FEMALE => 'Feminino',
        ];
    }
}
