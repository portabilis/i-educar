<?php

namespace iEducar\Modules\SchoolClass;

use App\Contracts\Enum;

class Period implements Enum
{
    public const MORNING = 1;
    public const AFTERNOON = 2;
    public const NIGTH = 3;
    public const FULLTIME = 4;

    public function getDescriptiveValues(): array
    {
        return [
            self::MORNING => 'Matutino',
            self::AFTERNOON => 'Vespertino',
            self::NIGTH => 'Noturno',
            self::FULLTIME => 'Integral',
        ];
    }
}
