<?php

class Portabilis_View_Helper_Input_Resource_MultipleSearchAjaxTranstornos extends Portabilis_View_Helper_Input_MultipleSearchAjax
{
    public function multipleSearchTranstornos($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'transtornos',
            'apiController' => 'Transtorno',
            'apiResource' => 'transtorno-search',
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::multipleSearchAjax($options['objectName'], $attrName, $options);
    }
}
