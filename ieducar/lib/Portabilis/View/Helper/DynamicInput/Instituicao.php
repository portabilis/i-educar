<?php

class Portabilis_View_Helper_DynamicInput_Instituicao extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputValue($value = null)
    {
        return $this->getInstituicaoId($value);
    }

    protected function inputName()
    {
        return 'ref_cod_instituicao';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($resources)) {
            $resources = App_Model_IedFinder::getInstituicoes();
        }

        return $this->insertOption(null, 'Selecione uma instituição', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'Instituição']];
    }

    public function selectInput($options = [])
    {
        parent::select($options);
    }

    public function instituicao($options = [])
    {
        $this->selectInput($options);
    }
}
