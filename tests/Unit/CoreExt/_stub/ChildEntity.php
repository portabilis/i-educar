<?php

class CoreExt_ChildEntityStub extends CoreExt_Entity
{
    protected $_data = [
        'nome' => null,
        'sexo' => null,
        'tipoSanguineo' => null,
        'peso' => null,
    ];

    protected $_references = [
        'sexo' => [
            'value' => null,
            'class' => 'CoreExt_EnumSexStub',
            'file' => __DIR__ . '/EnumSex.php'
        ],
        'tipoSanguineo' => [
            'value' => null,
            'class' => 'CoreExt_EnumTipoSanguineoStub',
            'file' => __DIR__ . '/EnumTipoSanguineo.php',
            'null' => true
        ]
    ];

    protected $_dataTypes = [
        'peso' => 'numeric'
    ];

    public function getDefaultValidatorCollection()
    {
        return [];
    }
}
