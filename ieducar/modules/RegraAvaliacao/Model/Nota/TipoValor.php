<?php

require_once 'CoreExt/Enum.php';

class RegraAvaliacao_Model_Nota_TipoValor extends CoreExt_Enum
{

    const NENHUM = 0;
    const NUMERICA = 1;
    const CONCEITUAL = 2;
    const NUMERICACONCEITUAL = 3;

    protected $_data = [
        self::NENHUM => 'Não usar nota',
        self::NUMERICA => 'Nota numérica',
        self::CONCEITUAL => 'Nota conceitual',
        self::NUMERICACONCEITUAL => 'Nota conceitual e numérica'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
