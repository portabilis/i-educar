<?php

class Portabilis_View_Helper_Input_Resource_TipoLogradouro extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        return $this->insertOption(null, 'Tipo logradouro', $resources);
    }

    public function tipoLogradouro($options = [])
    {
        parent::select($options);
    }
}
