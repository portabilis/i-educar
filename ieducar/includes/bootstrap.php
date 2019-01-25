<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

$app = require_once __DIR__ . '/../../bootstrap/app.php';

if ($app instanceof Application) {
    (new LoadEnvironmentVariables())->bootstrap($app);
}

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (getenv('CORE_EXT_CONFIGURATION_ENV')) {
    define('CORE_EXT_CONFIGURATION_ENV', getenv('CORE_EXT_CONFIGURATION_ENV'));
} else {
    define('CORE_EXT_CONFIGURATION_ENV', 'production');
}

define('PROJECT_ROOT', dirname(__DIR__));
define('APP_ROOT', PROJECT_ROOT . DS . 'intranet');

$configFile = PROJECT_ROOT . '/configuration/' . CORE_EXT_CONFIGURATION_ENV . '.ini';

if (!file_exists($configFile)) {
    $configFile = PROJECT_ROOT . '/configuration/ieducar.ini';
}

global $coreExt;

$coreExt = [];
$coreExt['Config'] = new CoreExt_Config_Ini($configFile, CORE_EXT_CONFIGURATION_ENV);

$defaultDatabase = config('database.default');
$configDatabase = config('database.connections.' . $defaultDatabase);

$coreExt['Config']->app->database->hostname = $configDatabase['host'];
$coreExt['Config']->app->database->port = $configDatabase['port'];
$coreExt['Config']->app->database->dbname = $configDatabase['database'];
$coreExt['Config']->app->database->username = $configDatabase['username'];
$coreExt['Config']->app->database->password = $configDatabase['password'];

dd($coreExt['Config']->app->database);

setlocale(LC_ALL, 'en_US.UTF-8');
date_default_timezone_set($coreExt['Config']->app->locale->timezone);

$tenantEnv = $_SERVER['HTTP_HOST'] ?? null;
$devEnv = ['development', 'local'];

if ($coreExt['Config']->hasEnviromentSection($tenantEnv)) {
    $coreExt['Config']->changeEnviroment($tenantEnv);
} else if (!in_array(CORE_EXT_CONFIGURATION_ENV, $devEnv)){
    $coreExt['Config']->changeEnviroment('production');
}

chdir(PROJECT_ROOT . DS . 'intranet');
