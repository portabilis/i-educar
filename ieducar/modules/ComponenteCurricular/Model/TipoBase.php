<?php

class ComponenteCurricular_Model_TipoBase extends CoreExt_Enum
{
    const DEFAULT = self::COMUM;
    const COMUM = 1;
    const DIVERSIFICADA = 2;
    const PROFISSIONAL = 3;
    const ART33 = 4;

    protected $_data = [
        self::COMUM => 'Base nacional comum',
        self::DIVERSIFICADA => 'Base diversificada',
        self::PROFISSIONAL => 'Base profissional',
        self::ART33 => 'Art.33 (Ensino religioso)',
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
