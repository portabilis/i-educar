<?php

class Portabilis_View_Helper_DynamicInput_CoreSelect extends Portabilis_View_Helper_Input_CoreSelect
{
    protected function loadCoreAssets()
    {
        parent::loadCoreAssets();

        $dependencies = ['/modules/DynamicInput/Assets/Javascripts/DynamicInput.js'];

        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $dependencies);
    }
}
