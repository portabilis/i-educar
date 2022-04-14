<?php

/**
 * Class Portabilis_View_Helper_Input_DateDiaMes
 */
class Portabilis_View_Helper_Input_DateDiaMes extends Portabilis_View_Helper_Input_Core
{
    public function dateDiaMes($attrName, $options = [])
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
            'size' => 5, // opção suportada pelo elemento, mas não pelo helper ieducar
            'hint' => 'dd/mm',
        ];

        $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);

        $isDbFormatted = strrpos($inputOptions['value'], '-') > -1;

        if ($isDbFormatted) {
            $inputOptions['value'] = Portabilis_Date_Utils::pgSQLToBr($inputOptions['value']);
        }

        $this->viewInstance->campoDataDiaMes(...array_values($inputOptions));

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

            \$input .change(function(){
                if (this.value == '') {
                    return true;
                }

                const data = this.value.split('/');
                const year = new Date().getFullYear();

                const day = parseInt(data[0]);
                const month = parseInt(data[1]);

                const newDate = new Date(year, (+month-1), day);

                const isValidDate = (Boolean(+newDate) && newDate.getDate() === day && (newDate.getMonth() + 1) === month);

                if (! isValidDate){
                    messageUtils.error('Informe uma data válida.', this);
                    this.value = '';
                }
            });
        ";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $script, $afterReady = true);
    }
}
