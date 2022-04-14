<?php

class Portabilis_View_Helper_DynamicInput_DataFinal extends Portabilis_View_Helper_DynamicInput_Core
{
    protected function inputValue($value = null)
    {
        if (!is_null($value)) {
            return $value;
        }

        if (!empty($this->viewInstance->data_final)) {
            return $this->viewInstance->data_final;
        }

        return date('t/m/Y');
    }

    public function dataFinal($options = [])
    {
        $defaultOptions = ['options' => []];
        $options = $this->mergeOptions($options, $defaultOptions);
        $value = $options['options']['value'] ?? null;

        $defaultInputOptions = [
            'id' => 'data_final',
            'label' => 'Data final',
            'value' => $this->inputValue($value),
            'required' => true,
            'label_hint' => '',
            'inline' => false,
            'callback' => false,
            'disabled' => false
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

       $this->viewInstance->campoData(...array_values( $inputOptions));
    }
}
