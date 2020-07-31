<?php

namespace iEducar\Modules\Servidores\Model;

use iEducar\Support\DescriptionValue;

class TipoVinculo
{
    use DescriptionValue;

    const EFETIVO = 1;
    const TEMPORARIO = 2;
    const TERCEIRIZADO = 3;
    const CLT = 4;

    public static function getDescriptiveValues()
    {
        return [
            self::EFETIVO => 'Concursado/efetivo/estável',
            self::TEMPORARIO => 'Contrato temporário',
            self::TERCEIRIZADO => 'Contrato terceirizado',
            self::CLT => 'Contrato CLT',
        ];
    }
}
