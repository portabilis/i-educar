<?php

require_once 'CoreExt/Entity.php';

class Educacenso_Model_OrgaoRegional extends CoreExt_Entity
{
    protected $_data = array(
        'sigla_uf' => null,
        'codigo' => null,
    );

    protected $_dataTypes = array(
        'sigla_uf' => 'string',
        'codigo' => 'string',
    );

    public function getDefaultValidatorCollection()
    {
        return array();
    }

    public function __toString()
    {
        return $this->codigo;
    }
}
