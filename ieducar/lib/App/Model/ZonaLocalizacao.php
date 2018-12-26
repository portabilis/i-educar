<?php

require_once 'CoreExt/Enum.php';

class App_Model_ZonaLocalizacao extends CoreExt_Enum
{
    const URBANA = 1;
    const RURAL = 2;

    protected $_data = [
        self::URBANA => 'Urbana',
        self::RURAL => 'Rural'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
