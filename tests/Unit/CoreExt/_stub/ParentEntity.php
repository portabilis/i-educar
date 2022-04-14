<?php

class CoreExt_ParentEntityStub extends CoreExt_Entity
{
    protected $_data = [
        'nome' => null,
        'filho' => null
    ];

    protected $_references = [
        'filho' => [
            'value' => null,
            'class' => 'CoreExt_ChildEntityDataMapperStub',
            'file' => __DIR__ . '/ChildEntityDataMapper.php',
            'null' => true
        ]
    ];

    public function getDefaultValidatorCollection()
    {
        return [
            'filho' => new CoreExt_Validate_String(['max' => 1])
        ];
    }
}
