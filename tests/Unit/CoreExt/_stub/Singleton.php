<?php

class CoreExt_SingletonStub extends CoreExt_Singleton
{
    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
