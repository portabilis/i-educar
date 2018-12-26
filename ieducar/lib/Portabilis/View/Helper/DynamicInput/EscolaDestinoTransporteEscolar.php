<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';

class Portabilis_View_Helper_DynamicInput_EscolaDestinoTransporteEscolar extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        return $this->insertOption(null, 'Todos', $resources);
    }

    public function escolaDestinoTransporteEscolar($options = [])
    {
        parent::select($options);
    }
}
