<?php

class Portabilis_View_Helper_DynamicInput_Setor extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'id_setor';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        return $this->insertOption(null, 'Selecione um setor', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'Setor']];
    }

    public function setor($options = [])
    {
        parent::select($options);
    }
}
