<?php

class Educacenso_Model_OrgaoRegional extends CoreExt_Entity
{
    protected $_data = [
        'sigla_uf' => null,
        'codigo' => null,
    ];

    protected $_dataTypes = [
        'sigla_uf' => 'string',
        'codigo' => 'string',
    ];

    public function getDefaultValidatorCollection()
    {
        return [];
    }

    public function __toString()
    {
        return $this->codigo;
    }
}
