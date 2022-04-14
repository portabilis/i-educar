<?php

class App_Model_SimNao extends CoreExt_Enum
{
    const NAO = 0;
    const SIM = 1;

    protected $_data = [
        self::NAO => 'Não',
        self::SIM => 'Sim'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
