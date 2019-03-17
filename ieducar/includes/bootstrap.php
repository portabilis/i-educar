<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$app = require_once __DIR__ . '/../../bootstrap/app.php';

if ($app instanceof Application) {
    (new LoadEnvironmentVariables())->bootstrap($app);
}

$env = env('APP_ENV', 'production');

$configFile = base_path('ieducar/configuration/' . $env . '.ini');

if (!file_exists($configFile)) {
    $configFile = base_path('ieducar/configuration/ieducar.ini');
}

global $coreExt;

$coreExt = [];
$coreExt['Config'] = new CoreExt_Config_Ini($configFile, $env);

setlocale(LC_ALL, 'en_US.UTF-8');
date_default_timezone_set($coreExt['Config']->app->locale->timezone);

$tenantEnv = $_SERVER['HTTP_HOST'] ?? null;
$devEnv = ['development', 'local', 'testing', 'dusk'];

if ($coreExt['Config']->hasEnviromentSection($tenantEnv)) {
    $coreExt['Config']->changeEnviroment($tenantEnv);
} else if (!in_array($env, $devEnv)){
    throw new NotFoundHttpException();
}

chdir(base_path('ieducar/intranet'));
