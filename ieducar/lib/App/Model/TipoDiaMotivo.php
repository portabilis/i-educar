<?php

require_once 'CoreExt/Enum.php';

class App_Model_TipoDiaMotivo extends CoreExt_Enum
{
    const DIA_LETIVO = 'l';
    const DIA_NAO_LETIVO = 'n';
    const DIA_EXTRA_LETIVO = 'e';

    protected $_data = [
        self::DIA_LETIVO => 'Dia letivo',
        self::DIA_NAO_LETIVO => 'Dia não letivo',
        self::DIA_EXTRA_LETIVO => 'Dia extra letivo',
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
