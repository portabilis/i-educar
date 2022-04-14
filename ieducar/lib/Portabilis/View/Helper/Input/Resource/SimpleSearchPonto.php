<?php

class Portabilis_View_Helper_Input_Resource_SimpleSearchPonto extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select descricao from modules.ponto_transporte_escolar where cod_ponto_transporte_escolar = $1';
            $options = ['params' => $id, 'return_only' => 'first-field'];

            return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
        }
    }

    public function simpleSearchPonto($attrName = '', $options = [])
    {
        $defaultOptions = [
            'objectName' => 'ponto',
            'apiController' => 'Ponto',
            'apiResource' => 'ponto-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o código ou a descrição do ponto';
    }
}
