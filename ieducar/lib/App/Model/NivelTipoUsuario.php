<?php

require_once 'CoreExt/Enum.php';

class App_Model_NivelTipoUsuario extends CoreExt_Enum
{
    const POLI_INSTITUCIONAL = 1;
    const INSTITUCIONAL = 2;
    const ESCOLA = 4;
    const BIBLIOTECA = 8;

    protected $_data = [
        self::POLI_INSTITUCIONAL => 'Poli-institucional',
        self::INSTITUCIONAL => 'Institucional',
        self::ESCOLA => 'Escola',
        self::BIBLIOTECA => 'Biblioteca'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
