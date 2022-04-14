<?php

class Portabilis_View_Helper_Input_Resource_MultipleSearchCursoAluno extends Portabilis_View_Helper_Input_MultipleSearch
{
    protected function getOptions($resources)
    {
        return $this->insertOption(null, '', $resources);
    }

    public function multipleSearchCursoAluno($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'cursoaluno',
            'apiController' => 'CursoAluno',
            'apiResource' => 'cursoaluno-search'
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
            $optionsVarName.placeholder = safeUtf8Decode('Selecione os cursos do aluno');
        ";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
    }

    protected function loadAssets()
    {
        Portabilis_View_Helper_Application::loadChosenLib($this->viewInstance);
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/MultipleSearch.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/MultipleSearchCursoaluno.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
    }
}
