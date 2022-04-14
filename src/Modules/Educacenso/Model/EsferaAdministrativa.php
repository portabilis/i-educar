<?php

namespace iEducar\Modules\Educacenso\Model;

class EsferaAdministrativa
{
    public const FEDERAL = 1;
    public const ESTADUAL = 2;
    public const MUNICIPAL = 3;

    public static function getDescriptiveValues()
    {
        return [
            self::FEDERAL => 'Federal',
            self::ESTADUAL => 'Estadual',
            self::MUNICIPAL => 'Municipal',
        ];
    }
}
