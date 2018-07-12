<?php

require_once 'CoreExt/Entity.php';

class ComponenteCurricular_Model_Turma extends CoreExt_Entity
{
    protected $_data = [
        'componenteCurricular' => null,
        'anoEscolar' => null,
        'escola' => null,
        'turma' => null,
        'cargaHoraria' => null,
        'docenteVinculado' => null,
        'etapasEspecificas' => null,
        'etapasUtilizadas' => null
    ];

    protected $_dataTypes = [
        'cargaHoraria' => 'numeric',
        'docenteVinculado' => 'numeric'
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
     * @see CoreExt_Entity_Validatable::getDefaultValidatorCollection()
     */
    public function getDefaultValidatorCollection()
    {
        return [
            'cargaHoraria' => new CoreExt_Validate_Numeric(['required' => false]),
            'docenteVinculado' => new CoreExt_Validate_Numeric(['required' => false])
        ];
    }
}
