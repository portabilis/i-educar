<?php

require_once 'lib/Portabilis/View/Helper/Input/Core.php';

class Portabilis_View_Helper_Input_TextArea extends Portabilis_View_Helper_Input_Core
{
    public function textArea($attrName, $options = [])
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
            'cols' => 49,
            'rows' => 5,
            'required' => true,
            'label_hint' => '',
            'max_length' => '',
            'inline' => false,
            'script' => false,
            'event' => 'onClick',
            'disabled' => false
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

        call_user_func_array([$this->viewInstance, 'campoMemo'], $inputOptions);
        $this->fixupPlaceholder($inputOptions);
    }
}
