<?php

require_once 'lib/Portabilis/View/Helper/Input/SimpleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

class Portabilis_View_Helper_Input_Resource_SimpleSearchProjeto extends Portabilis_View_Helper_Input_SimpleSearch
{
    public function simpleSearchProjeto($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'projeto',
            'apiController' => 'Projeto',
            'apiResource' => 'projeto-search',
            'showIdOnValue' => false
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select nome from pmieducar.projeto where cod_projeto = $1';
            $options = ['params' => $id, 'return_only' => 'first-field'];
            $nome = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

            return Portabilis_String_Utils::toLatin1($nome, ['transform' => true, 'escape' => false]);
        }
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o nome do projeto';
    }
}
