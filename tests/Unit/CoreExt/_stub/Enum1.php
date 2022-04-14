<?php

class CoreExt_Enum1Stub extends CoreExt_Enum
{
    public const ONE = 1;

    protected $_data = [
        self::ONE => 1
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
