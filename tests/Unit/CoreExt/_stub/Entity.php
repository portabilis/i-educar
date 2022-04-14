<?php

class CoreExt_EntityStub extends CoreExt_Entity
{
    protected $_data = [
        'nome' => null,
        'estadoCivil' => null,
        'doador' => null,
    ];

    protected $_dataTypes = [
        'doador' => 'bool'
    ];

    public function getDefaultValidatorCollection()
    {
        return [
            'nome' => new CoreExt_Validate_String(),
        ];
    }
}
