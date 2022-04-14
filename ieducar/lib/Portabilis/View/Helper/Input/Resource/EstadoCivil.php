<?php

class Portabilis_View_Helper_Input_Resource_EstadoCivil extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($resources)) {
            $resources = new clsEstadoCivil();
            $resources = $resources->lista();
            $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'ideciv', 'descricao');
        }

        return $this->insertOption(null, 'Estado civil', $resources);
    }

    public function estadoCivil($options = [])
    {
        parent::select($options);
    }
}
