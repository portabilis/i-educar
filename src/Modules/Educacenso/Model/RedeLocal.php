<?php

namespace iEducar\Modules\Educacenso\Model;

class RedeLocal
{
    const NENHUMA = 1;
    const A_CABO = 2;
    const WIRELESS = 3;

    public static function getDescriptiveValues()
    {
        return [
            self::A_CABO => 'A cabo',
            self::WIRELESS => 'Wireless',
            self::NENHUMA => 'Não há rede local interligando computadores',
        ];
    }
}