<?php

class Portabilis_View_Helper_Input_Resource_SimpleSearchIes extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select ies_id || \' - \' || nome AS nome from modules.educacenso_ies where id = $1';
            $options = ['params' => $id, 'return_only' => 'first-row'];
            $ies = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

            return $ies['nome'];
        }
    }

    public function simpleSearchIes($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'ies',
            'apiController' => 'Ies',
            'apiResource' => 'ies-search',
            'showIdOnValue' => false
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o código ou nome da instituição';
    }
}
