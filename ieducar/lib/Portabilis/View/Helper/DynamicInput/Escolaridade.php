<?php

use App\Models\LegacySchoolingDegree;

class Portabilis_View_Helper_DynamicInput_Escolaridade extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputValue($value = null)
    {
        return $this->getEscolaridadesId($value);
    }

    protected function inputName()
    {
        return 'idesco';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($resources)) {
            $resources = LegacySchoolingDegree::all()->getKeyValueArray('descricao');
        }

        return $this->insertOption(null, 'Selecione uma Escolaridade', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'Escolaridade']];
    }

    public function selectInput($options = [])
    {
        parent::select($options);
    }

    public function escolaridade($options = [])
    {
        $this->selectInput($options);
    }
}
