<?php

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

        $this->viewInstance->campoMemo(...array_values($inputOptions));
        $this->fixupPlaceholder($inputOptions);

        if ($inputOptions['max_length'] > 0) {
            $this->loadAssets();

            if (empty($inputOptions['max_length_plugin_options']['max'])) {
                $inputOptions['max_length_plugin_options']['max'] = $inputOptions['max_length'];
            }

            $this->enableMaxLenthPluginJs($inputOptions['id'], $inputOptions['max_length_plugin_options']);
        }
    }

    /**
     * Implementação do plugin jQuery Max Length
     * Consultar a documentação para ter acesso a todos as opções de configuração do plugin.
     * Passa no array ['max_length_plugin_options'] do options do elemento
     *
     * @docs http://keith-wood.name/maxlengthRef.html
     */
    protected function enableMaxLenthPluginJs($elementId, $options)
    {
        $this->css();
        $this->js($elementId, $options);
    }

    protected function loadAssets()
    {
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/TextArea.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
    }

    protected function js($elementId, $options = [])
    {
        $objectOptions = json_encode($options);

        $js = "
           TextArea.init(\$j('#{$elementId}'), {$objectOptions});
        ";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
    }

    protected function css()
    {
        $css = '
            .maxlength-feedback {
                    display: table;
                    margin: 0;
                    vertical-align: bottom;
                    position: relative;
                    left: 22.75em;
                    top: -4px;
                    width: 4em;
                    padding-right: 0.25em;
                    padding-left: 0.25em;
                    color: #fff;
                    background-color: #0ac336;
                    text-align: center;
                    border-radius: 3px;
            }
        ';
        Portabilis_View_Helper_Application::embedStylesheet($this->viewInstance, $css);
    }
}
