<?php

class Portabilis_View_Helper_Input_Resource_SimpleSearchCliente extends Portabilis_View_Helper_Input_SimpleSearch
{
    public function simpleSearchCliente($attrName = '', $options = [])
    {
        $defaultOptions = [
            'objectName' => 'cliente',
            'apiController' => 'Cliente',
            'apiResource' => 'cliente-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Digite um nome para buscar';
    }

    protected function loadAssets()
    {
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/SimpleSearchCliente.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
    }
}
