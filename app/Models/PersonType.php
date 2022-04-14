<?php

namespace App\Models;

use App\Contracts\Enum;

class PersonType implements Enum
{
    public const INDIVIDUAL = 1;
    public const ORGANIZATION = 2;

    public function getDescriptiveValues(): array
    {
        return [
            self::INDIVIDUAL => 'Pessoa Física',
            self::ORGANIZATION => 'Pessoa Jurídica',
        ];
    }
}
