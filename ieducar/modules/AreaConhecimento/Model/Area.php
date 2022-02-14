<?php

class AreaConhecimento_Model_Area extends CoreExt_Entity implements \Stringable
{
    protected $_data = [
        'instituicao' => null,
        'nome' => null,
        'secao' => null,
        'ordenamento_ac' => null,
        'agrupar_descritores' => null,
    ];

    public function getDefaultValidatorCollection()
    {
        $instituicoes = array_keys(App_Model_IedFinder::getInstituicoes());

        return [
            'instituicao' => new CoreExt_Validate_Choice(['choices' => $instituicoes]),
            'nome' => new CoreExt_Validate_String(['min' => 5, 'max' => 60]),
            'secao' => new CoreExt_Validate_String(['min' => 0, 'max' => 50]),
            'ordenamento_ac' => new CoreExt_Validate_Choice(['min' => 0, 'max' => 50])
        ];
    }

    public function __toString(): string
    {
        return $this->nome;
    }
}
