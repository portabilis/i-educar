<?php

namespace iEducar\Modules\Educacenso\Model;

class PoderPublicoConveniado
{
    public const ESTADUAL = 1;

    public const MUNICIPAL = 2;

    public static function getDescriptiveValues()
    {
        return [
            self::ESTADUAL => 'Secretaria estadual',
            self::MUNICIPAL => 'Secretaria municipal',
        ];
    }
}
