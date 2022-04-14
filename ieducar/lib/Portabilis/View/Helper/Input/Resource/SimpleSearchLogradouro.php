<?php

class Portabilis_View_Helper_Input_Resource_SimpleSearchLogradouro extends Portabilis_View_Helper_Input_SimpleSearch
{
    public function simpleSearchLogradouro($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'logradouro',
            'apiController' => 'Logradouro',
            'apiResource' => 'logradouro-search',
            'showIdOnValue' => false
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function resourceValue($id)
    {
        if ($id) {
            $sql = '
                select nome, descricao as tipo_logradouro
                from public.logradouro l
                left join urbano.tipo_logradouro tl
                on (l.idtlog = tl.idtlog)
                where idlog = $1
            ';

            $options = ['params' => $id, 'return_only' => 'first-row'];
            $resource = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

            return $resource['tipo_logradouro'] . ' ' . $resource['nome'];
        }
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o nome do logradouro';
    }

    protected function loadAssets()
    {
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/SimpleSearchLogradouro.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
    }
}
