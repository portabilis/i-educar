<?php

namespace iEducar\Modules\Educacenso\Model;

class PoderPublicoTransporte
{
    public const ESTADUAL = 1;

    public const MUNICIPAL = 2;

    public static function getDescriptiveValues()
    {
        return [
            self::ESTADUAL => 'Estadual',
            self::MUNICIPAL => 'Municipal',
        ];
    }
}
