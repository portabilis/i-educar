<?php

require_once 'lib/Portabilis/View/Helper/Input/SimpleSearch.php';

class Portabilis_View_Helper_Input_Resource_SimpleSearchAcervo extends Portabilis_View_Helper_Input_SimpleSearch
{
    public function simpleSearchAcervo($attrName = '', $options = [])
    {
        $defaultOptions = [
            'objectName' => 'acervo',
            'apiController' => 'Acervo',
            'apiResource' => 'acervo-search'
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
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/SimpleSearchAcervo.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
    }
}
