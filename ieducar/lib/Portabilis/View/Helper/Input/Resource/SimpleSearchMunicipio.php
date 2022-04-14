<?php

class Portabilis_View_Helper_Input_Resource_SimpleSearchMunicipio extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select nome, sigla_uf from public.municipio where idmun = $1';
            $options = ['params' => $id, 'return_only' => 'first-row'];
            $query = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

            return $query['nome']  . " ({$query['sigla_uf']})";
        }
    }

    public function simpleSearchMunicipio($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'municipio',
            'apiController' => 'Municipio',
            'apiResource' => 'municipio-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return $inputOptions['placeholder'] ?? 'Informe o código ou nome da cidade';
    }
}
