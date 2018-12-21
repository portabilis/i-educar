<?php

require_once 'CoreExt/Enum.php';

class App_Model_TipoMediacaoDidaticoPedagogico extends CoreExt_Enum
{
    const PRESENCIAL = 1;
    const SEMIPRESENCIAL = 2;
    const EDUCACAO_A_DISTANCIA = 3;

    protected $_data = [
        self::PRESENCIAL => 'Presencial',
        self::SEMIPRESENCIAL => 'Semipresencial',
        self::EDUCACAO_A_DISTANCIA => 'Educação a distância',
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
