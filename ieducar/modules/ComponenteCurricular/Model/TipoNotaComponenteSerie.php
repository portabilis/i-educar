<?php

require_once 'CoreExt/Enum.php';

class ComponenteSerie_Model_TipoNota extends CoreExt_Enum
{
    const CONCEITUAL = 1;
    const NUMERICA = 2;

    protected $_data = [
        self::CONCEITUAL => 'Nota conceitual',
        self::NUMERICA => 'Nota numérica',
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
