<?php

namespace iEducar\Modules\Educacenso\Model;

class LinguaMinistrada
{
    public const NAO_OFERECE_EDUCACAO_INDIGENA = 0;

    public const PORTUGUESA = 1;

    public const INDIGENA = 2;

    public const INDIGENA_E_PORTUGUESA = 3;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::NAO_OFERECE_EDUCACAO_INDIGENA => 'Não oferece educação indígena',
            self::INDIGENA => 'Língua indígena',
            self::PORTUGUESA => 'Língua portuguesa',
            self::INDIGENA_E_PORTUGUESA => 'Língua indígena e Língua portuguesa',
        ];
    }
}
