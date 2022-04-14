<?php

abstract class Educacenso_Model_CodigoReferencia extends CoreExt_Entity
{
    protected $_data = [
    'nomeInep'   => null,
    'fonte'      => null,
    'created_at' => null,
    'updated_at' => null
  ];

    public function getDefaultValidatorCollection()
    {
        return [
      'nomeInep' => new CoreExt_Validate_String(),
      'fonte'    => new CoreExt_Validate_String()
    ];
    }

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        unset($this->_data['id']);
    }
}
