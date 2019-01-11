<?php

require_once 'lib/Portabilis/View/Helper/Input/MultipleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

class Portabilis_View_Helper_Input_Resource_MultipleSearchBeneficios extends Portabilis_View_Helper_Input_MultipleSearch
{
    protected function getOptions($resources)
    {
        if (empty($resources)) {
            $resources = new clsPmieducarAlunoBeneficio();
            $resources = $resources->lista();
            $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'cod_aluno_beneficio', 'nm_beneficio');
        }

        return $this->insertOption(null, '', $resources);
    }

    public function multipleSearchBeneficios($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'beneficios',
            'apiController' => 'Beneficio',
            'apiResource' => 'beneficio-search'
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
            $optionsVarName.placeholder = 'Selecione os benefícios';
        ";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
    }
}
