<?php



class CoreExt_EnumCoffeeStub extends CoreExt_Enum
{
    const AMERICANO = 0;
    const MOCHA = 1;
    const ESPRESSO = 2;

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
