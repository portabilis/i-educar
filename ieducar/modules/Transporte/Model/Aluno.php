<?php

class Transporte_Model_Aluno extends CoreExt_Entity
{
    protected $_data = [
        'aluno' => null,
        'responsavel' => null,
        'user' => null,
        'created_at' => null,
        'updated_at' => null
    ];

    protected $_references = [
        'responsavel' => [
            'value' => null,
            'class' => 'Transporte_Model_Responsavel',
            'file' => 'Transporte/Model/Responsavel.php'
        ]
    ];

    public function __construct($options = [])
    {
        parent::__construct($options);
        unset($this->_data['id']);
    }

    public function getDefaultValidatorCollection()
    {
        $responsavel = Transporte_Model_Responsavel::getInstance();

        return [
            'aluno' => new CoreExt_Validate_Numeric(),
            'responsavel' => new CoreExt_Validate_Choice(['choices' => $responsavel->getKeys()]),
            'user' => new CoreExt_Validate_Numeric()
        ];
    }
}
