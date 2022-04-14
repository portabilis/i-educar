<?php

namespace iEducar\Modules\Educacenso\Model;

class LinguaMinistrada
{
    public const PORTUGUESA = 1;
    public const INDIGENA = 2;

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
