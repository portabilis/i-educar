<?php

class Avaliacao_Model_NotaAluno extends CoreExt_Entity
{
    protected $_data = [
    'matricula' => null
  ];

    /**
     * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        return [
      'matricula' => new CoreExt_Validate_Numeric(['min' => 0])
    ];
    }
}
