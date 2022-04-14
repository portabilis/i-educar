<?php

class ColunaMovimentoGeral extends CoreExt_Enum
{
    const EDUCACAO_INFANTIL = 0;
    const PRIMEIRO_ANO = 1;
    const SEGUNDO_ANO = 2;
    const TERCEIRO_ANO = 3;
    const QUARTO_ANO = 4;
    const QUINTO_ANO = 5;
    const SEXTO_ANO = 6;
    const SETIMO_ANO = 7;
    const OITAVO_ANO = 8;
    const NONO_ANO = 9;

    protected $_data = [
        self::EDUCACAO_INFANTIL => 'Educação infantil',
        self::PRIMEIRO_ANO => '1° ano',
        self::SEGUNDO_ANO => '2° ano',
        self::TERCEIRO_ANO => '3° ano',
        self::QUARTO_ANO => '4° ano',
        self::QUINTO_ANO => '5° ano',
        self::SEXTO_ANO => '6° ano',
        self::SETIMO_ANO => '7° ano',
        self::OITAVO_ANO => '8° ano',
        self::NONO_ANO => '9° ano'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
