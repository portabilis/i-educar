<?php

class Portabilis_View_Helper_Input_Resource_SimpleSearchEmpresa extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select nome from modules.empresa_transporte_escolar, cadastro.pessoa where ref_idpes = idpes and cod_empresa_transporte_escolar = $1';
            $options = ['params' => $id, 'return_only' => 'first-field'];

            return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
        }
    }

    public function simpleSearchEmpresa($attrName = '', $options = [])
    {
        $defaultOptions = [
            'objectName' => 'empresa',
            'apiController' => 'Empresa',
            'apiResource' => 'empresa-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o código ou nome da empresa';
    }
}
