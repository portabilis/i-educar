<?php

class Educacenso_Model_Docente extends Educacenso_Model_CodigoReferencia
{
    public function getDefaultValidatorCollection()
    {
        $validators = [
      'docente'     => new CoreExt_Validate_Numeric(['min' => 0]),
      'docenteInep' => new CoreExt_Validate_Numeric(['min' => 0]),
    ];

        return array_merge($validators, parent::getDefaultValidatorCollection());
    }

    public function __construct(array $options = [])
    {
        $this->_data['docente'] = null;
        $this->_data['docenteInep'] = null;

        parent::__construct($options);
    }
}
