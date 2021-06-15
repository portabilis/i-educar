<?php

class Educacenso_Model_Ies extends CoreExt_Entity implements \Stringable
{
    protected $_data = [
    'ies'                       => null,
    'nome'                      => null,
    'dependenciaAdministrativa' => null,
    'tipoInstituicao'           => null,
    'uf'                        => null,
    'user'                      => null,
    'created_at'                => null,
    'updated_at'                => null
  ];

    public function getDefaultValidatorCollection()
    {
        return [
      'ies'                       => new CoreExt_Validate_Numeric(['min' => 0]),
      'nome'                      => new CoreExt_Validate_String(['min' => 1]),
      'dependenciaAdministrativa' => new CoreExt_Validate_Numeric(['min' => 0]),
      'tipoInstituicao'           => new CoreExt_Validate_Numeric(['min' => 0]),
      'uf'                        => new CoreExt_Validate_String(['required' => false]),
      'user'                      => new CoreExt_Validate_Numeric(['min' => 0])
    ];
    }

    public function __toString(): string
    {
        return $this->nome;
    }
}
