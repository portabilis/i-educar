<?php

namespace App\Models;

use App\Contracts\Enum;

class GuardianType implements Enum
{
    const FATHER = 1;
    const MOTHER = 2;
    const BOTH = 3;
    const OTHER = 4;

    public function getDescriptiveValues(): array
    {
        return [
            self::FATHER => 'Pai',
            self::MOTHER => 'Mãe',
            self::BOTH => 'Pai e Mãe',
            self::OTHER => 'Outra pessoa',
        ];
    }
}
