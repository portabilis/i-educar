<?php

require_once 'lib/Portabilis/View/Helper/Input/SimpleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

class Portabilis_View_Helper_Input_Resource_SimpleSearchMunicipio extends Portabilis_View_Helper_Input_SimpleSearch
{
    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select nome, sigla_uf from public.municipio where idmun = $1';
            $options = ['params' => $id, 'return_only' => 'first-row'];
            $municipio = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
            $nome = $municipio['nome'];
            $siglaUf = $municipio['sigla_uf'];

            return Portabilis_String_Utils::toLatin1($nome, ['transform' => true, 'escape' => false]) . " ($siglaUf)";
        }
    }

    public function simpleSearchMunicipio($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'municipio',
            'apiController' => 'Municipio',
            'apiResource' => 'municipio-search'
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o código ou nome da cidade';
    }
}
