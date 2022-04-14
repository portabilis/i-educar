<?php

class ComponenteSerie_Model_TipoNota extends CoreExt_Enum
{
    const NUMERICA = 1;
    const CONCEITUAL = 2;

    protected $_data = [
        self::NUMERICA => 'Nota numérica',
        self::CONCEITUAL => 'Nota conceitual',
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
