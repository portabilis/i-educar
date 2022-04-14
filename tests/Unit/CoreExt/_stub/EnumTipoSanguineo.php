<?php

class CoreExt_EnumTipoSanguineoStub extends CoreExt_Enum
{
    public const A = 1;
    public const B = 2;
    public const AB = 3;
    public const O = 4;

    protected $_data = [
        self::A => 'A',
        self::B => 'B',
        self::AB => 'AB',
        self::O => 'O'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
