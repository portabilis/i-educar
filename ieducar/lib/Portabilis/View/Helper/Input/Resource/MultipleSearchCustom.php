<?php

require_once 'lib/Portabilis/View/Helper/Input/MultipleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Utils/SafeJson.php';

class Portabilis_View_Helper_Input_Resource_MultipleSearchCustom extends Portabilis_View_Helper_Input_MultipleSearch
{
    public function MultipleSearchCustom($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'custom',
            'apiController' => 'custom',
            'apiResource' => 'custom-search',
            'type' => 'multiple'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);
        $options['options']['resources'] = $this->insertOption(null, '', $options['options']['options']['all_values']);

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

        $json = SafeJson::encode($options['options']['options']['values']);

        $js .= 'arrayOptions.push({element : $j("#' . $options['objectName'] . '"),values : ' . $json . '})';

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = false);
    }

    protected function loadAssets()
    {
        Portabilis_View_Helper_Application::loadChosenLib($this->viewInstance);
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/MultipleSearchCustom.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
    }
}
