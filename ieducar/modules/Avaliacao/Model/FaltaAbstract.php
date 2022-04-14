<?php

abstract class Avaliacao_Model_FaltaAbstract extends Avaliacao_Model_Etapa
{
    protected $_data = [
    'faltaAluno' => null,
    'quantidade' => null,
  ];

    protected $_dataTypes = [
    'quantidade' => 'numeric'
  ];

    protected $_references = [
    'faltaAluno' => [
      'value' => null,
      'class' => 'Avaliacao_Model_FaltaAluno',
      'file'  => 'Avaliacao/Model/FaltaAluno.php'
    ]
  ];

    /**
     * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        return [
      'quantidade' => new CoreExt_Validate_Numeric(['min' => 0, 'max' => 100])
    ];
    }
}
