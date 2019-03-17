<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

$app = require_once __DIR__ . '/../../bootstrap/app.php';

if ($app instanceof Application) {
    (new LoadEnvironmentVariables())->bootstrap($app);
}

if (getenv('CORE_EXT_CONFIGURATION_ENV')) {
    define('CORE_EXT_CONFIGURATION_ENV', getenv('CORE_EXT_CONFIGURATION_ENV'));
} else {
    define('CORE_EXT_CONFIGURATION_ENV', 'production');
}

$configFile = base_path('ieducar/configuration/' . CORE_EXT_CONFIGURATION_ENV . '.ini');

if (!file_exists($configFile)) {
    $configFile = base_path('ieducar/configuration/ieducar.ini');
}

global $coreExt;

$coreExt = [];
$coreExt['Config'] = new CoreExt_Config_Ini($configFile, CORE_EXT_CONFIGURATION_ENV);

setlocale(LC_ALL, 'en_US.UTF-8');
date_default_timezone_set($coreExt['Config']->app->locale->timezone);

$tenantEnv = $_SERVER['HTTP_HOST'] ?? null;
$devEnv = ['development', 'local', 'testing', 'dusk'];

if ($coreExt['Config']->hasEnviromentSection($tenantEnv)) {
    $coreExt['Config']->changeEnviroment($tenantEnv);
} else if (!in_array(CORE_EXT_CONFIGURATION_ENV, $devEnv)){
    $coreExt['Config']->app->ambiente_inexistente = true;
}

chdir(base_path('ieducar/intranet'));
