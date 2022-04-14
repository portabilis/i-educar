<?php

namespace iEducar\Modules\Educacenso\Model;

class RedeLocal
{
    public const NENHUMA = 1;
    public const A_CABO = 2;
    public const WIRELESS = 3;

    public static function getDescriptiveValues()
    {
        return [
            self::A_CABO => 'A cabo',
            self::WIRELESS => 'Wireless',
            self::NENHUMA => 'Não há rede local interligando computadores',
        ];
    }
}
