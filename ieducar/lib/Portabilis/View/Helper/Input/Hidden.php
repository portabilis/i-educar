<?php

class Portabilis_View_Helper_Input_Hidden extends Portabilis_View_Helper_Input_Core
{
    public function hidden($attrName, $options = [])
    {
        $defaultOptions = ['options' => [], 'objectName' => ''];
        $options = $this->mergeOptions($options, $defaultOptions);
        $spacer = !empty($options['objectName']) && !empty($attrName) ? '_' : '';

        $defaultInputOptions = [
            'id' => $options['objectName'] . $spacer . $attrName,
            'value' => ''
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

        $this->viewInstance->campoOculto(...array_values($inputOptions));
    }
}
