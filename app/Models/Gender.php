<?php

namespace App\Models;

use App\Contracts\Enum;

class Gender implements Enum
{
    const MALE = 1;
    const FEMALE = 2;

    public function getDescriptiveValues(): array
    {
        return [
            self::MALE => 'Masculino',
            self::FEMALE => 'Feminino',
        ];
    }
}
