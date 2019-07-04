<?php

// Objeto de requisição
$request = new CoreExt_Controller_Request();

// Helper de URL. Auxilia para criar uma URL no formato http://www.example.org/module
$url = CoreExt_View_Helper_UrlHelper::getInstance();
$url = $url->url($request->get('REQUEST_URI'), ['components' => CoreExt_View_Helper_UrlHelper::URL_HOST]);

// Configura o baseurl da request
$request->setBaseurl(sprintf('%s/module', $url));

// Configura o DataMapper para usar uma instância de clsBanco com fetch de resultados
// usando o tipo FETCH_ASSOC
CoreExt_DataMapper::setDefaultDbAdapter(new clsBanco(['fetchMode' => clsBanco::FETCH_ASSOC]));

// Inicia o Front Controller
$frontController = CoreExt_Controller_Front::getInstance();
$frontController->setRequest($request);

// Configura o caminho aonde os módulos estão instalados
$frontController->setOptions(
    ['basepath' => base_path('ieducar/modules')]
);

$frontController->dispatch();

// Resultado
print $frontController->getViewContents();
