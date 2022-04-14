<?php

class CoreExt_EnumStringStub extends CoreExt_Enum
{
    public const RED = 'red';

    protected $_data = [
        self::RED => '#FF0000'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
