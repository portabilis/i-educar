<?php

namespace iEducar\Modules\SchoolClass;

use App\Contracts\Enum;

class Period implements Enum
{
    const MORNING = 1;
    const AFTERNOON = 2;
    const NIGTH = 3;
    const FULLTIME = 4;

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
