<?php

require_once 'lib/Portabilis/View/Helper/Input/Core.php';

class Portabilis_View_Helper_Input_Checkbox extends Portabilis_View_Helper_Input_Core
{
    public function checkbox($attrName, $options = [])
    {
        $defaultOptions = ['options' => [], 'objectName' => ''];
        $options = $this->mergeOptions($options, $defaultOptions);

        $spacer = !empty($options['objectName']) && !empty($attrName) ? '_' : '';

        $defaultInputOptions = [
            'id' => $options['objectName'] . $spacer . $attrName,
            'label' => ucwords($attrName),
            'value' => '',
            'label_hint' => '',
            'inline' => false,
            'script' => 'fixupCheckboxValue(this)',
            'disabled' => false
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

        // fixup para enviar um valor, junto ao param do checkbox.
        $js = '
            var fixupCheckboxValue = function(input) {
                var $this = $j(input);
                $this.val($this.is(\':checked\') ? \'on\' : \'\');
            }
        ';

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = false);
        call_user_func_array([$this->viewInstance, 'campoCheck'], $inputOptions);
    }
}
