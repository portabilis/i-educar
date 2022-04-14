<?php

class Portabilis_View_Helper_Input_Resource_SimpleSearchRota extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select descricao from modules.rota_transporte_escolar where cod_rota_transporte_escolar = $1';
            $options = ['params' => $id, 'return_only' => 'first-field'];

            return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
        }
    }

    public function simpleSearchRota($attrName = '', $options = [])
    {
        $defaultOptions = [
            'objectName' => 'rota',
            'apiController' => 'Rota',
            'apiResource' => 'rota-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o código ou a descrição da rota';
    }
}
