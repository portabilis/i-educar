<?php

class Educacenso_Model_Aluno extends Educacenso_Model_CodigoReferencia
{
    protected $_data = [
    'aluno'      => null,
    'alunoInep'  => null,
    'nomeInep'   => null,
    'fonte'      => null,
    'created_at' => null,
    'updated_at' => null
  ];

    public function getDefaultValidatorCollection()
    {
        $validators = [
      'aluno'     => new CoreExt_Validate_Numeric(['min' => 0]),
      'alunoInep' => new CoreExt_Validate_Numeric(['min' => 0]),
    ];

        return array_merge($validators, parent::getDefaultValidatorCollection());
    }
}
