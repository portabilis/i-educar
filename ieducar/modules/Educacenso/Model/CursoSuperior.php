<?php

class Educacenso_Model_CursoSuperior extends CoreExt_Entity implements \Stringable
{
    protected $_data = [
    'curso'      => null,
    'nome'       => null,
    'classe'     => null,
    'user'       => null,
    'created_at' => null,
    'updated_at' => null
  ];

    public function getDefaultValidatorCollection()
    {
        return [
      'curso'  => new CoreExt_Validate_String(['min' => 0]),
      'nome'   => new CoreExt_Validate_String(['min' => 1]),
      'classe' => new CoreExt_Validate_Numeric(['min' => 0]),
      'user'   => new CoreExt_Validate_Numeric(['min' => 0])
    ];
    }

    public function __toString(): string
    {
        return $this->nome;
    }
}
