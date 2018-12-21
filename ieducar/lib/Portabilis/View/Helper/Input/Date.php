<?php

require_once 'Portabilis/View/Helper/Input/Core.php';
require_once 'Portabilis/Date/Utils.php';

class Portabilis_View_Helper_Input_Date extends Portabilis_View_Helper_Input_Core
{
    public function date($attrName, $options = [])
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
            'required' => true,
            'label_hint' => '',
            'inline' => false,
            'callback' => false,
            'disabled' => false,
            'size' => 9, // opção suportada pelo elemento, mas não pelo helper ieducar
            'hint' => 'dd/mm/aaaa',
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

        $isDbFormated = strrpos($inputOptions['value'], '-') > -1;

        if ($isDbFormated) {
            $inputOptions['value'] = Portabilis_Date_Utils::pgSQLToBr($inputOptions['value']);
        }

        call_user_func_array([$this->viewInstance, 'campoData'], $inputOptions);

        $this->fixupPlaceholder($inputOptions);
        $this->fixupOptions($inputOptions);
    }

    protected function fixupOptions($inputOptions)
    {
        $id = $inputOptions['id'];

        $sizeFixup = '$input.attr(\'size\', ' . $inputOptions['size'] . ');';
        $disableFixup = $inputOptions['disabled'] ? '$input.attr(\'disabled\', \'disabled\');' : '';

        $script = '
            var $input = $j(\'#' . $id . "');
            $sizeFixup
            $disableFixup
            
            \$input.change(function(){
                if (this.value == '') {
                    return true;
                }

                var validateData = /^(((0[1-9]|[12][0-9]|3[01])([-.\/])(0[13578]|10|12)([-.\/])(\d{4}))|(([0][1-9]|[12][0-9]|30)([-.\/])(0[469]|11)([-.\/])(\d{4}))|((0[1-9]|1[0-9]|2[0-8])([-.\/])(02)([-.\/])(\d{4}))|((29)(\.|-|\/)(02)([-.\/])([02468][048]00))|((29)([-.\/])(02)([-.\/])([13579][26]00))|((29)([-.\/])(02)([-.\/])([0-9][0-9][0][48]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][2468][048]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][13579][26])))$/;

                if (!validateData.test(this.value)){
                    messageUtils.error('Informe data válida.', this);
                    this.value = '';
                }
            });
        ";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $script, $afterReady = true);
    }
}
