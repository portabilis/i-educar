<?php

class CoreExt_Enum2Stub extends CoreExt_Enum
{
    public const TWO = 2;

    protected $_data = [
        self::TWO => 2
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
