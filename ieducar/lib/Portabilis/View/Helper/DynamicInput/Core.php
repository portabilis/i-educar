<?php

class Portabilis_View_Helper_DynamicInput_Core extends Portabilis_View_Helper_Input_Core
{
    protected function loadCoreAssets()
    {
        parent::loadCoreAssets();

        $dependencies = ['/modules/DynamicInput/Assets/Javascripts/DynamicInput.js'];

        Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $dependencies);
    }
}
