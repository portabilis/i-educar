<?php

class ComponenteCurricular_Model_Componente extends CoreExt_Entity
{
    protected $_data = [
        'instituicao' => null,
        'nome' => null,
        'abreviatura' => null,
        'tipo_base' => null,
        'area_conhecimento' => null,
        'cargaHoraria' => null,
        'codigo_educacenso' => null,
        'ordenamento' => 99999,
        'desconsidera_para_progressao' => false
    ];

    protected $_references = [
        'area_conhecimento' => [
            'value' => null,
            'class' => 'AreaConhecimento_Model_AreaDataMapper',
            'file' => 'AreaConhecimento/Model/AreaDataMapper.php'
        ],
        'tipo_base' => [
            'value' => null,
            'class' => 'ComponenteCurricular_Model_TipoBase',
            'file' => 'ComponenteCurricular/Model/TipoBase.php'
        ],
        'codigo_educacenso' => [
            'value' => null,
            'class' => 'ComponenteCurricular_Model_CodigoEducacenso',
            'file' => 'ComponenteCurricular/Model/CodigoEducacenso.php'
        ]
    ];

    public function getDataMapper()
    {
        if (is_null($this->_dataMapper)) {
            $this->setDataMapper(new ComponenteCurricular_Model_ComponenteDataMapper());
        }

        return parent::getDataMapper();
    }

    public function getDefaultValidatorCollection()
    {
        $instituicoes = array_keys(App_Model_IedFinder::getInstituicoes());

        $tipoBase = ComponenteCurricular_Model_TipoBase::getInstance();
        $tipos = $tipoBase->getKeys();

        $codigoEducacenso = ComponenteCurricular_Model_CodigoEducacenso::getInstance();
        $codigos = $codigoEducacenso->getKeys();

        $areas = $this->getDataMapper()->findAreaConhecimento();
        $areas = CoreExt_Entity::entityFilterAttr($areas, 'id');

        return [
            'instituicao' => new CoreExt_Validate_Choice(['choices' => $instituicoes]),
            'nome' => new CoreExt_Validate_String(['min' => 5, 'max' => 200]),
            'abreviatura' => new CoreExt_Validate_String(['min' => 2, 'max' => 15]),
            'tipo_base' => new CoreExt_Validate_Choice(['choices' => $tipos]),
            'area_conhecimento' => new CoreExt_Validate_Choice(['choices' => $areas]),
            'codigo_educacenso' => new CoreExt_Validate_Choice(['choices' => $codigos]),
        ];
    }

    /**
     * @see CoreExt_Entity::__toString()
     */
    public function __toString()
    {
        return $this->nome;
    }
}
