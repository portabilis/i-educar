<?php

class Portabilis_Controller_Page_ListController extends Core_Controller_Page_ListController
{
    protected $backwardCompatibility = false;

    public function __construct()
    {
        $this->rodape = '';
        $this->largura = '100%';

        $this->loadAssets();
        parent::__construct();
    }

    protected function loadResourceAssets($dispatcher)
    {
        $rootPath = $_SERVER['DOCUMENT_ROOT'];
        $controllerName = ucwords($dispatcher->getControllerName());
        $actionName = ucwords($dispatcher->getActionName());

        $style = "/vendor/legacy/$controllerName/Assets/Stylesheets/$actionName.css";
        $script = "/vendor/legacy/$controllerName/Assets/Javascripts/$actionName.js";

        if (file_exists($rootPath . $style)) {
            Portabilis_View_Helper_Application::loadStylesheet($this, $style);
        }

        if (file_exists($rootPath . $script)) {
            Portabilis_View_Helper_Application::loadJavascript($this, $script);
        }
    }

    protected function loadAssets()
    {
        Portabilis_View_Helper_Application::loadJQueryFormLib($this);

        $styles = [
            '/vendor/legacy/Portabilis/Assets/Stylesheets/Frontend.css',
            '/vendor/legacy/Portabilis/Assets/Stylesheets/Frontend/Process.css',
        ];

        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);

        $scripts = [
            '/vendor/legacy/Portabilis/Assets/Javascripts/ClientApi.js',
            '/vendor/legacy/Portabilis/Assets/Javascripts/Validator.js',
            '/vendor/legacy/Portabilis/Assets/Javascripts/Utils.js',
        ];

        if (!$this->backwardCompatibility) {
            $scripts[] = '/vendor/legacy/Portabilis/Assets/Javascripts/Frontend/Process.js';
        }

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }
}
