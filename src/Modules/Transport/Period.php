<?php

namespace iEducar\Modules\Transport;

class Period
{
    public const MORNING = 1;
    public const AFTERNOON = 2;
    public const NIGHT = 3;
    public const FULL_TIME = 4;
    public const MORNING_AND_AFTERNOON = 5;
    public const MORNING_AND_NIGHT = 6;
    public const AFTERNOON_NIGHT = 7;

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
