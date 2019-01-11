<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';

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

        return $this->insertOption(null, 'Selecione uma institui&ccedil;&atilde;o', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'Institui&ccedil;&atilde;o']];
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
