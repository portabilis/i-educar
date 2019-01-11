<?php

require_once 'lib/Portabilis/View/Helper/Input/CoreSelect.php';

class Portabilis_View_Helper_Input_Resource_Uf extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        if (empty($options['resources'])) {
            $resources = new clsUf();
            $resources = $resources->lista();
            $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'sigla_uf', 'sigla_uf');
        }

        return $this->insertOption(null, 'Estado', $resources);
    }

    public function uf($options = [])
    {
        parent::select($options);
    }
}
