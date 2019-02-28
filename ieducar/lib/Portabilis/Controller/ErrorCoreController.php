<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'Core/View.php';
require_once 'Core/Controller/Page/ViewController.php';
require_once 'lib/Portabilis/View/Helper/Application.php';

class Portabilis_Controller_ErrorCoreController extends Core_Controller_Page_ViewController
{
    protected $_titulo = 'Error';

    public function __construct()
    {
        parent::__construct();

        $this->loadAssets();
    }

    public function generate(CoreExt_Controller_Page_Interface $instance)
    {
        $this->setHeader();

        $viewBase = new Core_View($instance);
        $viewBase->titulo = $this->_titulo;
        $viewBase->addForm($instance);

        $html = $viewBase->MakeHeadHtml();

        foreach ($viewBase->clsForm as $form) {
            $html .= $form->Gerar();
        }

        $html .= $form->getAppendedOutput();
        $html .= $viewBase->MakeFootHtml();

        return view('legacy.body', ['body' => $html])->render();
    }

    protected function loadAssets()
    {
        $styles = [
            'styles/reset.css',
            'styles/portabilis.css',
            'styles/min-portabilis.css',
            '/modules/Error/Assets/Stylesheets/Error.css'
        ];

        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
    }

    protected function setHeader()
    {
        die('setHeader must be overwritten!');
    }

    public function Gerar()
    {
        die('Gerar must be overwritten!');
    }
}
