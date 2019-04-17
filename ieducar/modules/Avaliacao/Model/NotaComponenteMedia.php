<?php

class Avaliacao_Model_NotaComponenteMedia extends CoreExt_Entity
{
    protected $_data = [
        'notaAluno' => null,
        'componenteCurricular' => null,
        'media' => null,
        'mediaArredondada' => null,
        'etapa' => null,
        'situacao' => null,
        'bloqueada' => false,
    ];

    protected $_dataTypes = [
        'media' => 'numeric',
        'bloqueada' => 'boolean',
    ];

    protected $_references = [
        'notaAluno' => [
            'value' => null,
            'class' => 'Avaliacao_Model_NotaAlunoDataMapper',
            'file'  => 'Avaliacao/Model/NotaAlunoDataMapper.php'
        ],
        'componenteCurricular' => [
            'value' => null,
            'class' => 'ComponenteCurricular_Model_ComponenteDataMapper',
            'file'  => 'ComponenteCurricular/Model/ComponenteDataMapper.php'
        ]
    ];

    public function __construct($options = [])
    {
        parent::__construct($options);
        unset($this->_data['id']);
    }

    public function getDefaultValidatorCollection()
    {
        return [
            'media' => new CoreExt_Validate_Numeric(['min' => 0, 'max' => 10]),
            'mediaArredondada' => new CoreExt_Validate_String(['max' => 5]),
            'etapa' => new CoreExt_Validate_String(['max' => 2])
        ];
    }
}
