<?php

require_once 'lib/Portabilis/View/Helper/Input/SimpleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

class Portabilis_View_Helper_Input_Resource_SimpleSearchIes extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select ies_id || \' - \' || nome AS nome from modules.educacenso_ies where id = $1';
            $options = ['params' => $id, 'return_only' => 'first-row'];
            $ies = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
            $nome = $ies['nome'];

            return Portabilis_String_Utils::toLatin1($nome, ['transform' => true, 'escape' => false]);
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
