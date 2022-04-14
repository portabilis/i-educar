<?php

class Portabilis_View_Helper_Input_Resource_SimpleSearchVeiculo extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select (descricao || \',Placa: \' || placa) from modules.veiculo where cod_veiculo = $1';
            $options = ['params' => $id, 'return_only' => 'first-field'];

            return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
        }
    }

    public function simpleSearchVeiculo($attrName = '', $options = [])
    {
        $defaultOptions = [
            'objectName' => 'veiculo',
            'apiController' => 'Veiculo',
            'apiResource' => 'veiculo-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o código ou a descrição do veiculo';
    }
}
