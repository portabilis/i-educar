<?php

class Portabilis_View_Helper_Input_Resource_MultipleSearchCategoriaObra extends Portabilis_View_Helper_Input_MultipleSearch
{
    protected function getOptions($resources)
    {
        if (empty($resources)) {
            $resources = new clsPmieducarCategoriaObra();
            $resources = $resources->lista();
            $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'id', 'descricao');
        }

        return $this->insertOption(null, '', $resources);
    }

    public function multipleSearchCategoriaObra($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'categorias',
            'apiController' => 'Categoria',
            'apiResource' => 'categoria-search'
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
            $optionsVarName.placeholder = safeUtf8Decode('Selecione as categorias');
        ";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
    }
}
