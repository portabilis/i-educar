<?php

class Portabilis_View_Helper_Input_Resource_MultipleSearchComponenteCurricular extends Portabilis_View_Helper_Input_MultipleSearch
{
    protected function getOptions($resources)
    {
        if (empty($resources)) {
            $resources = Portabilis_Utils_Database::fetchPreparedQuery('SELECT id, nome FROM modules.componente_curricular');
            $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'id', 'nome');
        }

        return $this->insertOption(null, '', $resources);
    }

    public function multipleSearchComponenteCurricular($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'componentecurricular',
            'apiController' => 'ComponenteCurricular',
            'apiResource' => 'componentecurricular-search',
            'searchForArea' => false,
            'allDisciplinesMulti' => false
        ];

        $options = $this->mergeOptions($options, $defaultOptions);
        $options['options']['resources'] = $this->getOptions($options['options']['resources']);

        $this->placeholderJs($options);

        parent::multipleSearch($options['objectName'], $attrName, $options);
    }

    protected function placeholderJs($options)
    {
        $optionsVarName = 'multipleSearch' . Portabilis_String_Utils::camelize($options['objectName']) . 'Options';
        $searchForArea = $options['searchForArea'] ? 'true' : 'false';
        $allDisciplinesMulti = $options['allDisciplinesMulti'] ? 'true' : 'false';
        $js = "
            if (typeof $optionsVarName == 'undefined') { $optionsVarName = {} };
            $optionsVarName.placeholder = safeUtf8Decode('Selecione os componentes');
        ";
        $js .= "var searchForArea = {$searchForArea};";
        $js .= "var allDisciplinesMulti = {$allDisciplinesMulti};";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = false);
    }

    protected function loadAssets()
    {
        Portabilis_View_Helper_Application::loadChosenLib($this->viewInstance);
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/MultipleSearchComponenteCurricular.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
    }
}
