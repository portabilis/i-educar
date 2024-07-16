<?php

namespace App\Models;

use App\Contracts\Enum;

class Gender implements Enum
{
    public const MALE = 1;

    public const FEMALE = 2;

    /**
     * @return array<int, string>
     */
    public function getDescriptiveValues(): array
    {
        return [
            self::MALE => 'Masculino',
            self::FEMALE => 'Feminino',
        ];
    }
}
