<?php

class Portabilis_View_Helper_DynamicInput_Matricula extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ref_cod_matricula';
    }

    protected function inputOptions($options)
    {
        return $this->insertOption(null, 'Selecione uma matrícula', $resources);
    }

    protected function defaultOptions()
    {
        return ['options' => ['label' => 'Matrícula']];
    }

    public function matricula($options = [])
    {
        parent::select($options);
    }
}
