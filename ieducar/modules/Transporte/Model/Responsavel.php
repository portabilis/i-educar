<?php

class Transporte_Model_Responsavel extends CoreExt_Enum
{
    const NENHUM = 0;
    const ESTADUAL = 1;
    const MUNICIPAL = 2;

    protected $_data = [
        self::NENHUM    => 'Não utiliza',
        self::ESTADUAL  => 'Estadual',
        self::MUNICIPAL => 'Municipal'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
