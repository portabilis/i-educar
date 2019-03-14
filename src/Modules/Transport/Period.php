<?php

namespace iEducar\Modules\Transport;

class Period
{
    const MORNING = 1;
    const AFTERNOON = 2;
    const NIGHT = 3;
    const FULL_TIME = 4;
    const MORNING_AND_AFTERNOON = 5;
    const MORNING_AND_NIGHT = 6;
    const AFTERNOON_NIGHT = 7;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::MORNING => 'Matutino',
            self::AFTERNOON => 'Vespertino',
            self::NIGHT => 'Noturno',
            self::FULL_TIME => 'Integral',
            self::MORNING_AND_AFTERNOON => 'Matutino e vespertino',
            self::MORNING_AND_NIGHT => 'Matutino e noturno',
            self::AFTERNOON_NIGHT => 'Vespertino e noturno',
        ];
    }
}
