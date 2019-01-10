<?php

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

$locale = CoreExt_Locale::getInstance();
$locale->setCulture('pt_BR')->setLocale();

global $coreExt;

$coreExt = [];
$coreExt['Config'] = new CoreExt_Config_Ini($configFile, CORE_EXT_CONFIGURATION_ENV);
$coreExt['Locale'] = $locale;

date_default_timezone_set($coreExt['Config']->app->locale->timezone);

$tenantEnv = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;

if ($coreExt['Config']->hasEnviromentSection($tenantEnv)) {
    $coreExt['Config']->changeEnviroment($tenantEnv);
} else if (!$coreExt['Config']->hasEnviromentSection($tenantEnv) && CORE_EXT_CONFIGURATION_ENV !== "development"){
    $coreExt['Config']->app->ambiente_inexistente = true;
}

chdir(PROJECT_ROOT . DS . 'intranet');
