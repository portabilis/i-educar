<?php

namespace iEducar\Modules\Educacenso\Model;

class PaisResidencia
{
    public const ARGENTINA = 32;
    public const BOLIVIA = 68;
    public const BRASIL = 76;
    public const COLOMBIA = 170;
    public const GUIANA = 328;
    public const GUIANA_FRANCESA = 254;
    public const PARAGUAI = 600;
    public const PERU = 604;
    public const SURINAME = 740;
    public const URUGUAI = 858;
    public const VENEZUELA = 862;

    public static function getDescriptiveValues()
    {
        return [
            self::ARGENTINA => 'Argentina',
            self::BOLIVIA => 'Bolívia',
            self::BRASIL => 'Brasil',
            self::COLOMBIA => 'Colômbia',
            self::GUIANA => 'Guiana',
            self::GUIANA_FRANCESA => 'Guiana Francesa',
            self::PARAGUAI => 'Paraguai',
            self::PERU => 'Peru',
            self::SURINAME => 'Suriname',
            self::URUGUAI => 'Uruguai',
            self::VENEZUELA => 'Venezuela',
        ];
    }
}
