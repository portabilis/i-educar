<?php

use App\Models\LegacyBenefit;

class Portabilis_View_Helper_Input_Resource_MultipleSearchBeneficios extends Portabilis_View_Helper_Input_MultipleSearch
{
    public function multipleSearchBeneficios($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'beneficios',
            'apiController' => 'Beneficio',
            'apiResource' => 'beneficio-search',
        ];

        $options = $this->mergeOptions($options, $defaultOptions);
        $options['options']['resources'] = $this->getOptions($options['options']['resources']);

        $this->placeholderJs($options);

        parent::multipleSearch($options['objectName'], $attrName, $options);
    }

    protected function getOptions($resources)
    {
        if (empty($resources)) {
            $resources = LegacyBenefit::query()
                ->where('ativo', 1)
                ->orderBy('nm_beneficio', 'ASC')
                ->pluck('nm_beneficio', 'cod_aluno_beneficio');
        }

        return $this->insertOption(null, '', $resources);
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
