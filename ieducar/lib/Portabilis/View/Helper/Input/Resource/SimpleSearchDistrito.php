<?php

require_once 'lib/Portabilis/View/Helper/Input/SimpleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

class Portabilis_View_Helper_Input_Resource_SimpleSearchDistrito extends Portabilis_View_Helper_Input_SimpleSearch
{
    public function simpleSearchDistrito($attrName, $options = [])
    {
        $defaultOptions = [
            'objectName' => 'distrito',
            'apiController' => 'Distrito',
            'apiResource' => 'distrito-search',
            'showIdOnValue' => false
        ];

        $options = $this->mergeOptions($options, $defaultOptions);

        parent::simpleSearch($options['objectName'], $attrName, $options);
    }

    protected function resourceValue($id)
    {
        if ($id) {
            $sql = 'select nome from public.distrito where iddis = $1';
            $options = ['params' => $id, 'return_only' => 'first-field'];
            $distrito = $id . ' - ' . Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

            return Portabilis_String_Utils::toLatin1($distrito, ['transform' => true, 'escape' => false]);
        }
    }

    protected function inputPlaceholder($inputOptions)
    {
        return 'Informe o nome do distrito';
    }

    protected function loadAssets()
    {
        $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/SimpleSearchDistrito.js';
        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
    }
}
