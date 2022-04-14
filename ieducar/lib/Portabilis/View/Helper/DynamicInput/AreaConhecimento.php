<?php

class Portabilis_View_Helper_DynamicInput_AreaConhecimento extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'area_conhecimento_id';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        return $this->insertOption(null, 'Todas', $resources);
    }

    public function areaConhecimento($options = [])
    {
        parent::select($options);
    }
}
