<?php

class Portabilis_View_Helper_Input_Resource_SimpleSearchMotorista extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select nome from modules.motorista, cadastro.pessoa where ref_idpes = idpes and cod_motorista = $1';
            $options = ['params' => $id, 'return_only' => 'first-field'];
            $nome = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

            return Portabilis_String_Utils::toUtf8($nome, ['transform' => true, 'escape' => false]);
        }
    }

    public function simpleSearchMotorista($attrName = '', $options = [])
    {
        $defaultOptions = [
            'objectName' => 'motorista',
            'apiController' => 'Motorista',
            'apiResource' => 'motorista-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o código ou nome do motorista';
    }
}
