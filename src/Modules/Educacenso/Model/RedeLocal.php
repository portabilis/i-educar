<?php

namespace iEducar\Modules\Educacenso\Model;

class RedeLocal
{
    public const NENHUMA = 1;

    public const A_CABO = 2;

    public const WIRELESS = 3;

    public const A_CABO_E_WIRELESS = 4;

    public static function getDescriptiveValues()
    {
        return [
            self::NENHUMA => 'Não há rede local interligando computadores',
            self::A_CABO => 'A cabo',
            self::WIRELESS => 'Wireless',
            self::A_CABO_E_WIRELESS => 'A cabo e Wireless',
        ];
    }
}
