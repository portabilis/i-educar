<?php

class Educacenso_Model_Escola extends Educacenso_Model_CodigoReferencia
{
    protected $_data = [
    'escola'      => null,
    'escolaInep'  => null,
    'nomeInep'   => null,
    'fonte'      => null,
    'created_at' => null,
    'updated_at' => null
  ];

    public function getDefaultValidatorCollection()
    {
        $validators = [
      'escola'     => new CoreExt_Validate_Numeric(['min' => 0]),
      'escolaInep' => new CoreExt_Validate_Numeric(['min' => 0]),
    ];

        return array_merge($validators, parent::getDefaultValidatorCollection());
    }
}
