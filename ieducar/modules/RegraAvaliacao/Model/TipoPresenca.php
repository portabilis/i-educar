<?php

class RegraAvaliacao_Model_TipoPresenca extends CoreExt_Enum
{
    const GERAL = 1;
    const POR_COMPONENTE = 2;

    protected $_data = [
        self::GERAL => 'Apura falta no geral (unificada)',
        self::POR_COMPONENTE => 'Apura falta por componente curricular',
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
