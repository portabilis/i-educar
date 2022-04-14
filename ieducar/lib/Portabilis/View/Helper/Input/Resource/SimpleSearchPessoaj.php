<?php

class Portabilis_View_Helper_Input_Resource_SimpleSearchPessoaj extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select nome from cadastro.pessoa where idpes = $1 and tipo=\'J\'';
            $options = ['params' => $id, 'return_only' => 'first-field'];

            return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
        }
    }

    public function simpleSearchPessoaj($attrName = '', $options = [])
    {
        $defaultOptions = [
            'objectName' => 'pessoaj',
            'apiController' => 'Pessoaj',
            'apiResource' => 'pessoaj-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o código ou nome da pessoa jurídica';
    }
}
