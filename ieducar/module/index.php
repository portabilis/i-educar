<?php

use iEducar\Modules\ErrorTracking\TrackerFactory;

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'includes/bootstrap.php';
require_once 'include/clsBanco.inc.php';
require_once 'App/Model/IedFinder.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';
require_once 'CoreExt/Controller/Request.php';
require_once 'CoreExt/Controller/Front.php';
require_once 'CoreExt/DataMapper.php';

try {
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
        ['basepath' => PROJECT_ROOT . DS . 'modules']
    );

    $frontController->dispatch();

    // Resultado
    print $frontController->getViewContents();

} catch (Exception $e) {
    if (config('app.debug')) {
        throw $e;
    }

    $lastError = error_get_last();

    @session_start();
    $_SESSION['last_error_message'] = $e->getMessage();
    $_SESSION['last_php_error_message'] = $lastError['message'];
    $_SESSION['last_php_error_line'] = $lastError['line'];
    $_SESSION['last_php_error_file'] = $lastError['file'];
    @session_write_close();

    if ($GLOBALS['coreExt']['Config']->modules->error->track) {
        $tracker = TrackerFactory::getTracker($GLOBALS['coreExt']['Config']->modules->error->tracker_name);
        $tracker->notify($e);
    }

    error_log('Erro inesperado (pego em /module/index.php): ' . $e->getMessage());

    die('<script>document.location.href = \'/module/Error/unexpected\';</script>');
}
