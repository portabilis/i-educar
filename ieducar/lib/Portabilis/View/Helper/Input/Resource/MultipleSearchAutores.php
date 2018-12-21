<?php

require_once 'lib/Portabilis/View/Helper/Input/MultipleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

class Portabilis_View_Helper_Input_Resource_MultipleSearchAutores extends Portabilis_View_Helper_Input_MultipleSearch
{
    protected function getOptions($resources)
    {
        if (empty($resources)) {
            $resources = new clsPmieducarAcervoAutor();
            $resources = $resources->lista();
            $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'cod_acervo_autor', 'nm_autor');
        }

        return $this->insertOption(null, '', $resources);
    }

    public function multipleSearchAutores($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'autores',
            'apiController' => 'Autor',
            'apiResource' => 'autor-search'
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
            $optionsVarName.placeholder = safeUtf8Decode('Selecione os autores');
        ";

        Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
    }
}
