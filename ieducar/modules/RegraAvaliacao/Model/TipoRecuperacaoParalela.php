<?php

require_once 'CoreExt/Enum.php';

class RegraAvaliacao_Model_TipoRecuperacaoParalela extends CoreExt_Enum
{

    const NAO_USAR = 0;
    const USAR_POR_ETAPA = 1;
    const USAR_POR_ETAPAS_ESPECIFICAS = 2;

    protected $_data = [
        self::NAO_USAR => 'N&atilde;o usar recupera&ccedil;&atilde;o paralela',
        self::USAR_POR_ETAPA => 'Usar uma recupera&ccedil;&atilde;o paralela por etapa',
        self::USAR_POR_ETAPAS_ESPECIFICAS => 'Usar uma recupera&ccedil;&atilde;o paralela por etapas espec&iacute;ficas'
    ];

    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }
}
