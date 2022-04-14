<?php

class Portabilis_View_Helper_Input_Resource_MultipleSearchEtapacurso extends Portabilis_View_Helper_Input_MultipleSearch
{
    protected function getOptions($resources)
    {
        if (empty($resources)) {
            $resources = Portabilis_Utils_Database::fetchPreparedQuery('SELECT * FROM modules.etapas_educacenso');
            $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'id', 'nome');
        }

        return $this->insertOption(null, '', $resources);
    }

    public function multipleSearchEtapacurso($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'etapacurso',
            'apiController' => 'Etapacurso',
            'apiResource' => 'etapacurso-search'
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
            $optionsVarName.placeholder = safeUtf8Decode('Selecione as etapas');
        ";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
    }
}
