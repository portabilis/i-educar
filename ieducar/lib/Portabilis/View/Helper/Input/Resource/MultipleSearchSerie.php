<?php

class Portabilis_View_Helper_Input_Resource_MultipleSearchSerie extends Portabilis_View_Helper_Input_MultipleSearch
{
    protected function getOptions($resources)
    {
        return $this->insertOption(null, '', $resources);
    }

    public function multipleSearchSerie($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'multiple_search_serie',
            'apiController' => 'Serie',
            'apiResource' => 'series-curso-grouped'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);
        $options['options']['resources'] = $this->getOptions($options['options']['resources']);

        $this->placeholderJs($options);

        parent::multipleSearch($options['objectName'], $attrName, $options);
    }

    protected function placeholderJs($options)
    {
        $optionsVarName = 'multipleSearch' . Portabilis_String_Utils::camelize($options['objectName']) . 'Options';
        $js = "
            if (typeof $optionsVarName == 'undefined') { $optionsVarName = {} };
            $optionsVarName.placeholder = safeUtf8Decode('Selecione');
        ";

        $json = SafeJson::encode($options['options']['values']);

        $js .= 'arrayOptions.push({element : $j("#' . $options['objectName'] . '_serie-' . $options['options']['coluna'] . '"),values : ' . $json . '})';

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = false);
    }

    protected function loadAssets()
    {
        Portabilis_View_Helper_Application::loadChosenLib($this->viewInstance);
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/MultipleSearchSerie.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
    }
}
