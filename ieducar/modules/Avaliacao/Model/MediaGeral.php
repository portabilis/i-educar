<?php

class Avaliacao_Model_MediaGeral extends CoreExt_Entity
{
    protected $_data = [
    'notaAluno'            => null,
    'media'                => null,
    'mediaArredondada'     => null,
    'etapa'                => null
  ];

    protected $_dataTypes = [
    'media' => 'numeric'
  ];

    protected $_references = [
    'notaAluno' => [
      'value' => null,
      'class' => 'Avaliacao_Model_NotaAlunoDataMapper',
      'file'  => 'Avaliacao/Model/NotaAlunoDataMapper.php'
    ]
  ];

    public function __construct($options = [])
    {
        parent::__construct($options);
        unset($this->_data['id']);
    }

    /**
     * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        return [
      'media' => new CoreExt_Validate_Numeric(['min' => 0, 'max' => 10]),
      'mediaArredondada' => new CoreExt_Validate_String(['max' => 5]),
      'etapa' => new CoreExt_Validate_String(['max' => 2])
    ];
    }
}
