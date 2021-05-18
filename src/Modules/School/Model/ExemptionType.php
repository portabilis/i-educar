<?php

namespace iEducar\Modules\School\Model;

use iEducar\Support\DescriptionValue;

class ExemptionType
{
    use DescriptionValue;

    public const DISPENSA_COMPONENTES = 1;
    public const DISPENSA_BUSCA_ATIVA = 2;

    public static function getDescriptiveValues(): array
    {
        return [
            self::DISPENSA_COMPONENTES => 'Dispensa de componentes',
            self::DISPENSA_BUSCA_ATIVA => 'Busca Ativa',
        ];
    }
}
