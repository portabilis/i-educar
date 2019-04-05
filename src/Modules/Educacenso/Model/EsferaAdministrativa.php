<?php

namespace iEducar\Modules\Educacenso\Model;

class EsferaAdministrativa
{
    const FEDERAL = 1;
    const ESTADUAL = 2;
    const MUNICIPAL = 3;

    public static function getDescriptiveValues()
    {
        return [
            self::FEDERAL => 'Federal',
            self::ESTADUAL => 'Estadual',
            self::MUNICIPAL => 'Municipal',
        ];
    }
}
