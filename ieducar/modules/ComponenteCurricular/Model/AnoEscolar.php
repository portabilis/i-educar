<?php

require_once 'CoreExt/Entity.php';
require_once 'ComponenteCurricular/Model/TipoBase.php';

class ComponenteCurricular_Model_AnoEscolar extends CoreExt_Entity
{
    protected $_data = [
        'componenteCurricular' => null,
        'anoEscolar' => null,
        'cargaHoraria' => null,
        'anosLetivos' => null
    ];

    protected $_dataTypes = [
        'cargaHoraria' => 'numeric'
    ];

    protected $_references = [
        'componenteCurricular' => [
            'value' => null,
            'class' => 'ComponenteCurricular_Model_ComponenteDataMapper',
            'file' => 'ComponenteCurricular/Model/ComponenteDataMapper.php'
        ]
    ];

    /**
     * Construtor. Remove o campo identidade já que usa uma chave composta.
     *
     * @see CoreExt_Entity::__construct($options = array())
     */
    public function __construct($options = [])
    {
        parent::__construct($options);
        unset($this->_data['id']);
    }

    /**
     * @see CoreExt_Entity::getDataMapper()
     */
    public function getDataMapper()
    {
        if (is_null($this->_dataMapper)) {
            require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
            $this->setDataMapper(new ComponenteCurricular_Model_AnoEscolarDataMapper());
        }

        return parent::getDataMapper();
    }

    /**
     * @see CoreExt_Entity_Validatable::getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        $validators = [];

        if (isset($this->anoEscolar)) {
            $validators['cargaHoraria'] = new CoreExt_Validate_Numeric(['min' => 1]);
        }

        return $validators;
    }
}
