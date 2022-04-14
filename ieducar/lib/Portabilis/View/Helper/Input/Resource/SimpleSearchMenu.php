<?php

class Portabilis_View_Helper_Input_Resource_SimpleSearchMenu extends Portabilis_View_Helper_Input_SimpleSearch
{
    public function simpleSearchMenu($attrName = '', $options = [])
    {
        $defaultOptions = [
            'objectName' => 'menu',
            'apiController' => 'Menu',
            'apiResource' => 'menu-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o nome do menu';
    }

    protected function loadAssets()
    {
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/simpleSearchMenu.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
        $style = '/modules/Portabilis/Assets/Stylesheets/Frontend/Inputs/Resource/simpleSearchMenu.css';
        Portabilis_View_Helper_Application::loadStylesheet($this->viewInstance, $style);
    }
}
