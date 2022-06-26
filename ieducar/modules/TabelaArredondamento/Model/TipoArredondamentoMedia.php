<?php

class TabelaArredondamento_Model_TipoArredondamentoMedia extends CoreExt_Enum
{
    const NAO_ARREDONDAR = 0;
    const ARREDONDAR_PARA_NOTA_INFERIOR = 1;
    const ARREDONDAR_PARA_NOTA_SUPERIOR = 2;
    const ARREDONDAR_PARA_NOTA_ESPECIFICA = 3;

    protected $_data = [
        self::NAO_ARREDONDAR => 'Não utilizar arredondamento para esta casa decimal',
        self::ARREDONDAR_PARA_NOTA_INFERIOR => 'Arredondar para o número inteiro imediatamente inferior',
        self::ARREDONDAR_PARA_NOTA_SUPERIOR => 'Arredondar para o número inteiro imediatamente superior',
        self::ARREDONDAR_PARA_NOTA_ESPECIFICA => 'Arredondar para a casa decimal específica'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
