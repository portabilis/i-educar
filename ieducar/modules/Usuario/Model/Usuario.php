<?php

require_once 'CoreExt/Entity.php';
require_once 'App/Model/IedFinder.php';
require_once 'CoreExt/Validate/Email.php';

class Usuario_Model_Usuario extends CoreExt_Entity
{
    protected $_data = [
        'id' => null,
        'escolaId' => null,
        'instituicaoId' => null,
        'funcionarioCadId' => null,
        'funcionarioExcId' => null,
        'tipoUsuarioId' => null,
        'dataCadastro' => null,
        'dataExclusao' => null,
        'ativo' => null
    ];

    public function getDataMapper()
    {
        if (is_null($this->_dataMapper)) {
            require_once 'Usuario/Model/UsuarioDataMapper.php';
            $this->setDataMapper(new Usuario_Model_UsuarioDataMapper());
        }

        return parent::getDataMapper();
    }

    public function getDefaultValidatorCollection()
    {
        return [];
    }

    // TODO remover metodo? já que foi usado $_attributeMap id
    protected function _createIdentityField()
    {
        $id = ['id' => null];
        $this->_data = array_merge($id, $this->_data);

        return $this;
    }
}
