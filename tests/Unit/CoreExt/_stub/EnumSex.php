<?php

class CoreExt_EnumSexStub extends CoreExt_Enum
{
    public const MALE = 1;
    public const FEMALE = 2;

    protected $_data = [
        self::MALE => 'masculino',
        self::FEMALE => 'feminino'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
