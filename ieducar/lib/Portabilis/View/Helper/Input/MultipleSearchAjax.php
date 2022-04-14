<?php

class Portabilis_View_Helper_Input_MultipleSearchAjax extends Portabilis_View_Helper_Input_Core
{
    public function multipleSearchAjax($objectName, $attrName, $options = [])
    {
        $defaultOptions = [
            'options' => [],
            'apiModule' => 'Api',
            'apiController' => ucwords($objectName),
            'apiResource' => $objectName . '-search',
            'searchPath' => ''
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        if (empty($options['searchPath'])) {
            $options['searchPath'] = '/module/' . $options['apiModule'] . '/' . $options['apiController'] .
                '?oper=get&resource=' . $options['apiResource'];
        }

        $this->selectInput($objectName, $attrName, $options);
        $this->loadAssets();
        $this->js($objectName, $attrName, $options);
    }

    protected function selectInput($objectName, $attrName, $options)
    {
        $textHelperOptions = ['objectName' => $objectName];

        $this->inputsHelper()->select($attrName, $options['options'], $textHelperOptions);
    }

    protected function loadAssets()
    {
        Portabilis_View_Helper_Application::loadChosenLib($this->viewInstance);
        Portabilis_View_Helper_Application::loadAjaxChosenLib($this->viewInstance);

        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearchAjax.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
    }

    protected function js($objectName, $attrName, $options)
    {
        $resourceOptions = 'multipleSearchAjax' . Portabilis_String_Utils::camelize($objectName) . 'Options';

        $js = "
            $resourceOptions = typeof $resourceOptions == 'undefined' ? {} : $resourceOptions;
            multipleSearchAjaxHelper.setup('$objectName', '$attrName', '" . $options['searchPath'] . "', $resourceOptions);
        ";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
    }
}
