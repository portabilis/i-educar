<?php

require_once 'lib/Portabilis/View/Helper/Input/Core.php';

class Portabilis_View_Helper_Input_Ano extends Portabilis_View_Helper_Input_Core
{
    protected function inputValue($value = null)
    {
        if (!is_null($value) && is_numeric($value)) {
            return $value;
        }

        if (isset($this->viewInstance->ano) && is_numeric($this->viewInstance->ano)) {
            return $this->viewInstance->ano;
        }

        return date('Y');
    }

    public function ano($options = [])
    {
        $defaultOptions = ['options' => []];
        $options = $this->mergeOptions($options, $defaultOptions);
        $value = $options['options']['value'] ?? null;

        $defaultInputOptions = [
            'id' => 'ano',
            'label' => 'Ano',
            'value' => $this->inputValue($value),
            'size' => 4,
            'max_length' => 4,
            'required' => true,
            'label_hint' => '',
            'input_hint' => '',
            'script' => false,
            'callback' => false,
            'inline' => false,
            'disabled' => false
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

        call_user_func_array([$this->viewInstance, 'campoNumero'], $inputOptions);
    }
}
