<?php

namespace iEducar\Modules\Educacenso\Model;

use iEducar\Support\DescriptionValue;

class SchoolManagerRole
{
    use DescriptionValue;

    const DIRETOR = 1;
    const OUTRO = 2;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::DIRETOR => 'Diretor',
            self::OUTRO => 'Outro',
        ];
    }
}
