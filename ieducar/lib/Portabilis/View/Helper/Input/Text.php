<?php

class Portabilis_View_Helper_Input_Text extends Portabilis_View_Helper_Input_Core
{
    public function text($attrName, $options = [])
    {
        $defaultOptions = ['options' => [], 'objectName' => ''];

        $options = $this->mergeOptions($options, $defaultOptions);
        $spacer = !empty($options['objectName']) && !empty($attrName) ? '_' : '';

        $label = !empty($attrName) ? $attrName : $options['objectName'];
        $label = str_replace('_id', '', $label);

        $defaultInputOptions = [
            'id' => $options['objectName'] . $spacer . $attrName,
            'label' => ucwords($label),
            'value' => null,
            'size' => 50,
            'max_length' => 50,
            'required' => true,
            'script' => false,
            'inline' => false,
            'label_hint' => '',
            'input_hint' => '',
            'callback' => false,
            'event' => 'onKeyUp',
            'disabled' => false
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

        $this->viewInstance->campoTexto(...array_values($inputOptions));
        $this->fixupPlaceholder($inputOptions);
    }
}
