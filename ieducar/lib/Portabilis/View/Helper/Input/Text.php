<?php

require_once 'lib/Portabilis/View/Helper/Input/Core.php';

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
        $inputOptions['label'] = Portabilis_String_Utils::toLatin1($inputOptions['label'], ['escape' => false]);

        call_user_func_array([$this->viewInstance, 'campoTexto'], $inputOptions);
        $this->fixupPlaceholder($inputOptions);
    }
}
