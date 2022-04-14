<?php

class Portabilis_View_Helper_DynamicInput_Etapa extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'etapa';
    }

    protected function inputOptions($options)
    {
        return $this->insertOption(null, 'Selecione uma etapa', $resources);
    }

    public function etapa($options = [])
    {
        parent::select($options);
    }
}
