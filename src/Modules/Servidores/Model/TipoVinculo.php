<?php

namespace iEducar\Modules\Servidores\Model;

use iEducar\Support\DescriptionValue;

class TipoVinculo
{
    use DescriptionValue;

    public const EFETIVO = 1;
    public const TEMPORARIO = 2;
    public const TERCEIRIZADO = 3;
    public const CLT = 4;

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
