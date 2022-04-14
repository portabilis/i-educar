<?php

class FormulaMedia_Model_TipoFormula extends CoreExt_Enum
{
    const MEDIA_FINAL = 1;
    const MEDIA_RECUPERACAO = 2;

    protected $_data = [
        self::MEDIA_FINAL => 'Média final',
        self::MEDIA_RECUPERACAO => 'Média para recuperação'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
