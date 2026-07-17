<?php

namespace iEducar\Modules\Educacenso\Model;

class EsferaAdministrativa
{
    public const FEDERAL = 1;

    public const ESTADUAL = 2;

    public const MUNICIPAL = 3;

    public const ESTADUAL_E_MUNICIPAL = 4;

    public const FEDERAL_SETEC = 6;

    public static function getDescriptiveValues()
    {
        return [
            self::FEDERAL => 'Federal',
            self::ESTADUAL => 'Estadual',
            self::MUNICIPAL => 'Municipal',
            self::ESTADUAL_E_MUNICIPAL => 'Estadual e Municipal',
            self::FEDERAL_SETEC => 'Federal SETEC',
        ];
    }
}
