<?php

namespace App\Models;

use App\Contracts\Enum;

class PersonType implements Enum
{
    const INDIVIDUAL = 1;
    const ORGANIZATION = 2;

    public function getDescriptiveValues(): array
    {
        return [
            self::INDIVIDUAL => 'Pessoa Física',
            self::ORGANIZATION => 'Pessoa Jurídica',
        ];
    }
}
