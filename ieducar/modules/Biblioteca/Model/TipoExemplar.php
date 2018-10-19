<?php

require_once 'CoreExt/Entity.php';
require_once 'App/Model/IedFinder.php';
require_once 'CoreExt/Validate/Email.php';

/**
 * ComponenteCurricular_Model_Componente class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     ComponenteCurricular
 * @subpackage  Modules
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class Biblioteca_Model_TipoExemplar extends CoreExt_Entity
{
    /**
     * @var array $_data
     */
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

    /**
     * @var array $_dataTypes
     */
    protected $_dataTypes = ['nm_tipo' => 'string'];

    /**
     * @var array $_references
     */
    protected $_references = [];

    public function getDataMapper()
    {
        if (is_null($this->_dataMapper)) {
            require_once 'Biblioteca/Model/TipoExemplarDataMapper.php';
            $this->setDataMapper(new Biblioteca_Model_TipoExemplarDataMapper());
        }

        return parent::getDataMapper();
    }

    /**
     * @return array
     */
    public function getDefaultValidatorCollection()
    {
        return [];
    }

    /**
     * @return Biblioteca_Model_TipoExemplar
     */
    protected function _createIdentityField()
    {
        $id = ['cod_exemplar_tipo' => null];
        $this->_data = array_merge($id, $this->_data);

        return $this;
    }
}
