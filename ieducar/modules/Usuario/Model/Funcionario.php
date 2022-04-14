<?php

class Usuario_Model_Funcionario extends CoreExt_Entity
{
    protected $_data = [
        'matricula' => null,
        'email' => null,
        'senha' => null,
        'data_troca_senha' => null,
        'status_token' => null
    ];

    protected $_dataTypes = [
        'matricula' => 'string'
    ];

    protected $_references = [];

    public function getDataMapper()
    {
        if (is_null($this->_dataMapper)) {
            $this->setDataMapper(new Usuario_Model_FuncionarioDataMapper());
        }

        return parent::getDataMapper();
    }

    public function getDefaultValidatorCollection()
    {
        return [
            'email' => new CoreExt_Validate_Email()
        ];
    }

    protected function _createIdentityField()
    {
        $id = ['ref_cod_pessoa_fj' => null];
        $this->_data = array_merge($id, $this->_data);

        return $this;
    }
}
