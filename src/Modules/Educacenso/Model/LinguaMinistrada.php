<?php

namespace iEducar\Modules\Educacenso\Model;

class LinguaMinistrada
{
    const PORTUGUESA = 1;
    const INDIGENA = 2;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::PORTUGUESA => 'Língua Portuguesa',
            self::INDIGENA => 'Língua Indígena',
        ];
    }
}
