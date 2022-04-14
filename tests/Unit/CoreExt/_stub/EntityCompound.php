<?php

class CoreExt_EntityCompoundStub extends CoreExt_Entity
{
    protected $_data = [
        'pessoa' => null,
        'curso' => null,
        'confirmado' => null
    ];

    protected $_dataTypes = [
        'confirmado' => 'bool'
    ];

    public function __construct($options = [])
    {
        parent::__construct($options);
        unset($this->_data['id']);
    }

    public function getDefaultValidatorCollection()
    {
        return [];
    }
}
