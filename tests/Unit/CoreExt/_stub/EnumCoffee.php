<?php

class CoreExt_EnumCoffeeStub extends CoreExt_Enum
{
    public const AMERICANO = 0;
    public const MOCHA = 1;
    public const ESPRESSO = 2;

    protected $_data = [
        self::AMERICANO => '',
        self::MOCHA => 'Mocha',
        self::ESPRESSO => 'ESPRESSO',
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
