<?php

require_once 'lib/Portabilis/View/Helper/Input/SimpleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

class Portabilis_View_Helper_Input_Resource_SimpleSearchBairro extends Portabilis_View_Helper_Input_SimpleSearch
{
    public function simpleSearchBairro($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'bairro',
            'apiController' => 'Bairro',
            'apiResource' => 'bairro-search',
            'showIdOnValue' => false
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select nome, zona_localizacao from public.bairro where idbai = $1';
            $options = ['params' => $id, 'return_only' => 'first-row'];
            $municipio = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
            $nome = $municipio['nome'];
            $zona = ($municipio['zona_localizacao'] == 1 ? 'Urbana' : 'Rural');

            return Portabilis_String_Utils::toLatin1($nome, ['transform' => true, 'escape' => false]) . " / Zona $zona";
        }
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o nome do bairro';
    }

    protected function loadAssets()
    {
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/SimpleSearchBairro.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
    }
}
