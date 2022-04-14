<?php

class RegraAvaliacao_Model_TipoParecerDescritivo extends CoreExt_Enum
{
    const NENHUM = 0;
    const ETAPA_DESCRITOR = 1;
    const ETAPA_COMPONENTE = 2;
    const ETAPA_GERAL = 3;
    const ANUAL_DESCRITOR = 4;
    const ANUAL_COMPONENTE = 5;
    const ANUAL_GERAL = 6;

    protected $_data = [
        self::NENHUM => 'Não usar parecer descritivo',
        self::ETAPA_COMPONENTE => 'Um parecer por etapa e por componente curricular',
        self::ETAPA_GERAL => 'Um parecer por etapa, geral',
        self::ANUAL_COMPONENTE => 'Um parecer por ano letivo e por componente curricular',
        self::ANUAL_GERAL => 'Um parecer por ano letivo, geral',
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
