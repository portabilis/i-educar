<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';

class Portabilis_View_Helper_DynamicInput_Transferido extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ref_cod_matricula';
    }

    protected function inputOptions($options)
    {
        return $this->insertOption(null, 'Selecione uma matr&iacute;cula', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'Matr&iacute;cula']];
    }

    public function transferido($options = [])
    {
        parent::select($options);
    }
}
