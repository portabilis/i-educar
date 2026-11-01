<?php

use App\Services\AssetService;
use Tests\TestCase;

uses(TestCase::class);

test('expõe a versão, o versionamento automático e a preferência por https', function () {
    $service = new AssetService('1.0', true, true);

    expect($service->getVersion())->toBe('1.0')
        ->and($service->isAutomaticVersioning())->toBeTrue()
        ->and($service->getSecure())->toBeTrue();
});

test('no versionamento automático usa a data do arquivo, a versão configurada ou nada', function () {
    $mtime = filemtime(public_path('css/app.css'));
    $automatic = new AssetService('1.0', false, true);
    $unversioned = new AssetService(null, false, true);

    expect($automatic->get('css/app.css'))->toEndWith("/css/app.css?v={$mtime}")
        ->and($automatic->get('/css/app.css?tema=escuro.css'))->toEndWith("/css/app.css?tema=escuro.css&v={$mtime}")
        ->and($automatic->get('https://cdn.example.com/lib.js'))->toEndWith('/lib.js?v=1.0')
        ->and($automatic->get('/js/inexistente.js'))->toEndWith('/js/inexistente.js?v=1.0')
        ->and($unversioned->get('/js/inexistente.js'))->toEndWith('/js/inexistente.js')
        ->and($unversioned->findRealPath('/js/inexistente.js'))->toBeNull();
});

test('em ambiente local gera uma versão aleatória para arquivo que não existe', function () {
    $env = [$_ENV['APP_ENV'] ?? null, $_SERVER['APP_ENV'] ?? null, getenv('APP_ENV')];
    $_ENV['APP_ENV'] = $_SERVER['APP_ENV'] = 'local';
    putenv('APP_ENV=local');

    try {
        $url = (new AssetService(null, false, true))->get('/js/inexistente.js');
    } finally {
        [$_ENV['APP_ENV'], $_SERVER['APP_ENV']] = $env;
        putenv($env[2] === false ? 'APP_ENV' : 'APP_ENV=' . $env[2]);
    }

    expect($url)->toMatch('#/js/inexistente\.js\?v=[0-9a-f]{10}$#');
});
