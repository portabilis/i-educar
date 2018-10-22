<?php

require_once 'CoreExt/Entity.php';

class ConfiguracaoMovimentoGeral extends CoreExt_Entity
{
    protected $_data = [
        'serie' => null,
        'coluna' => null
    ];

    /**
     * @return array
     */
    public function getDefaultValidatorCollection()
    {
        return [];
    }
}
