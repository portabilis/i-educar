<?php

class Portabilis_View_Helper_Input_Numeric extends Portabilis_View_Helper_Input_Core
{
    protected function fixupValidation($inputOptions)
    {
        // fixup para remover caracteres não numericos
        $js = '
            $j(\'#' . $inputOptions['id'] . "').keyup(function(){
                var oldValue = this.value;
                this.value = this.value.replace(/[^0-9\.-]/g, '');

                if (oldValue != this.value)
                    messageUtils.error('Informe apenas números.', this);
            });

            \$j('#" . $inputOptions['id'] . '\').on(\'change\', function(){
                if (this.value.length && !new RegExp(\'^-?\\\\d*\\\\.{0,1}\\\\d+$\').test(this.value)) {
                    messageUtils.error(\'Informe apenas valores numéricos.\', this);
                    this.value = \'\';
                }
            });
        ';

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = false);
    }

    public function numeric($attrName, $options = [])
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
            'size' => 15,
            'max_length' => 15,
            'required' => true,
            'label_hint' => ' ',
            'input_hint' => '',
            'script' => false,
            'event' => 'onKeyUp',
            'inline' => false,
            'disabled' => false
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

        $this->viewInstance->campoNumero(...array_values($inputOptions));

        $this->fixupPlaceholder($inputOptions);
        $this->fixupValidation($inputOptions);
    }
}
