<?php

class Biblioteca_Model_TipoExemplar extends CoreExt_Entity
{
    protected $_data = [
    'cod_exemplar_tipo'  => null,
    'ref_cod_biblioteca' => null,
    'ref_usuario_exc'    => null,
    'ref_usuario_cad'    => null,
    'nm_tipo'            => null,
    'descricao'          => null,
    'data_cadastro'      => null,
    'data_exclusao'      => null,
    'ativo'              => null
  ];

    protected $_dataTypes = [
    'nm_tipo' => 'string'
  ];

    protected $_references = [
  ];

    public function getDataMapper()
    {
        if (is_null($this->_dataMapper)) {
            $this->setDataMapper(new Biblioteca_Model_TipoExemplarDataMapper());
        }

        return parent::getDataMapper();
    }

    public function getDefaultValidatorCollection()
    {
        return [];
    }

    protected function _createIdentityField()
    {
        $id = ['cod_exemplar_tipo' => null];
        $this->_data = array_merge($id, $this->_data);

        return $this;
    }
}
