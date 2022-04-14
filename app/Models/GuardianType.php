<?php

namespace App\Models;

use App\Contracts\Enum;

class GuardianType implements Enum
{
    public const FATHER = 1;
    public const MOTHER = 2;
    public const BOTH = 3;
    public const OTHER = 4;

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
