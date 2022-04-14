<?php

use App\Models\District;

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
            $district = District::query()->find($id);
            $distrito = $id . ' - ' . $district->name;

            return $distrito;
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
