<?php
require_once 'lib/Portabilis/View/Helper/Input/SimpleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

class Portabilis_View_Helper_Input_Resource_SimpleSearchRotinasAuditoria extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        return $id;
    }

    public function simpleSearchRotinasAuditoria($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'rotinas-auditoria',
            'apiController' => 'RotinasAuditoria',
            'apiResource' => 'rotinas-auditoria-search',
            'showIdOnValue' => false
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o nome da rotina';
    }
}
