<?php

require_once 'CoreExt/Entity.php';

class ConfiguracaoMovimentoGeral extends CoreExt_Entity
{
    protected $_data = array(
        'serie'     => NULL,
        'coluna'    => NULL
    );

    protected $_dataTypes = array(
        'coluna' => 'integer'
    );

    public function getDefaultValidatorCollection()
    {
        return array();
    }
}
