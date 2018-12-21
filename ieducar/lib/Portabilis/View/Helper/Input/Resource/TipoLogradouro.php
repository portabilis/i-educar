<?php

require_once 'lib/Portabilis/View/Helper/Input/CoreSelect.php';

class Portabilis_View_Helper_Input_Resource_TipoLogradouro extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($options['resources'])) {
            $resources = new clsTipoLogradouro();
            $resources = $resources->lista();
            $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'idtlog', 'descricao');
        }

        return $this->insertOption(null, 'Tipo logradouro', $resources);
    }

    public function tipoLogradouro($options = [])
    {
        parent::select($options);
    }
}
