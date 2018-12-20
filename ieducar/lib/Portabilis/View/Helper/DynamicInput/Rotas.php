<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';

class Portabilis_View_Helper_DynamicInput_Rotas extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ref_cod_rota_transporte_escolar';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        return $this->insertOption(null, 'Selecione uma rota', $resources);
    }

    public function rotas($options = [])
    {
        parent::select($options);
    }
}
