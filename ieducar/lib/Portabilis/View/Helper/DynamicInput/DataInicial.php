<?php

require_once 'lib/Portabilis/View/Helper/DynamicInput/Core.php';

class Portabilis_View_Helper_DynamicInput_DataInicial extends Portabilis_View_Helper_DynamicInput_Core
{
    protected function inputValue($value = null)
    {
        if (!is_null($value)) {
            return $value;
        }

        if (!empty($this->viewInstance->data_inicial)) {
            return $this->viewInstance->data_inicial;
        }

        return date('01/m/Y');
    }

    public function dataInicial($options = [])
    {
        $defaultOptions = ['options' => []];
        $options = $this->mergeOptions($options, $defaultOptions);
        $value = $options['options']['value'] ?? null;

        $defaultInputOptions = [
            'id' => 'data_inicial',
            'label' => 'Data inicial',
            'value' => $this->inputValue($value),
            'required' => true,
            'label_hint' => '',
            'inline' => false,
            'callback' => false,
            'disabled' => false
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

        call_user_func_array([$this->viewInstance, 'campoData'], $inputOptions);
    }
}
