<?php

class Portabilis_View_Helper_Input_Select extends Portabilis_View_Helper_Input_Core
{
    public function select($attrName, $options = [])
    {
        $defaultOptions = ['options' => [], 'objectName' => '', 'resources' => []];

        $options = $this->mergeOptions($options, $defaultOptions);

        $spacer = !empty($options['objectName']) && !empty($attrName) ? '_' : '';

        $defaultInputOptions = [
            'id' => $options['objectName'] . $spacer . $attrName,
            'label' => ucwords($attrName),
            'resources' => $options['resources'],
            'value' => '',
            'callback' => '',
            'inline' => false,
            'label_hint' => '',
            'input_hint' => '',
            'disabled' => false,
            'required' => true,
            'multiple' => false
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

        $this->viewInstance->campoLista(...array_values($inputOptions));
    }
}
