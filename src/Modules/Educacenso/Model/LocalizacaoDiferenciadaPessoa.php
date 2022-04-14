<?php

namespace iEducar\Modules\Educacenso\Model;

class LocalizacaoDiferenciadaPessoa
{
    public const AREA_ASSENTAMENTO = 1;
    public const TERRA_INDIGENA = 2;
    public const COMUNIDADES_REMANESCENTES_QUILOMBOS = 3;
    public const NAO_SE_APLICA = 7;

    public static function getDescriptiveValues()
    {
        return [
            self::AREA_ASSENTAMENTO => 'Área de assentamento',
            self::TERRA_INDIGENA => 'Terra indígena',
            self::COMUNIDADES_REMANESCENTES_QUILOMBOS => 'Área onde se localiza comunidades remanescentes de quilombos',
            self::NAO_SE_APLICA => 'Não está em área de localização diferenciada',
        ];
    }
}
