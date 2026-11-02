<?php

use App\Http\Controllers\Api\VersionController;
use Tests\TestCase;

uses(TestCase::class);

test('get version', function () {
    $this->get('api/version')
        ->assertSuccessful()
        ->assertJsonStructure(
            [
                'entity',
                'version',
                'build',
            ]
        );
});

test('sem conseguir rodar o git a versao de build e desconhecida', function () {
    $basePath = $this->app->basePath();
    $this->app->setBasePath('/caminho/que/nao/existe');

    try {
        $build = (fn () => $this->getBuildVersion())->call(new VersionController);
    } finally {
        $this->app->setBasePath($basePath);
    }

    $this->assertSame('unknow', $build);
});
