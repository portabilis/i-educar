<?php

require_once 'CoreExt/Enum.php';

class RegraAvaliacao_Model_TipoProgressao extends CoreExt_Enum
{

    const CONTINUADA = 1;
    const NAO_CONTINUADA_MEDIA_PRESENCA = 2;
    const NAO_CONTINUADA_SOMENTE_MEDIA = 3;
    const NAO_CONTINUADA_MANUAL = 4;
    const NAO_CONTINUADA_MANUAL_CICLO = 5;

    protected $_data = [
        self::CONTINUADA => 'Continuada',
        self::NAO_CONTINUADA_MEDIA_PRESENCA => 'Não-continuada: média e presença',
        self::NAO_CONTINUADA_SOMENTE_MEDIA => 'Não-continuada: somente média',
        self::NAO_CONTINUADA_MANUAL => 'Não-continuada manual',
        self::NAO_CONTINUADA_MANUAL_CICLO => 'Não-continuada manual (ciclo)'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
