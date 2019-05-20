<?php

namespace iEducar\Modules\Educacenso\Model;

class PaisResidencia
{
    const ARGENTINA = 32;
    const BOLIVIA = 68;
    const BRASIL = 76;
    const COLOMBIA = 170;
    const GUIANA = 328;
    const GUIANA_FRANCESA = 254;
    const PARAGUAI = 600;
    const PERU = 604;
    const SURINAME = 740;
    const URUGUAI = 858;
    const VENEZUELA = 862;

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