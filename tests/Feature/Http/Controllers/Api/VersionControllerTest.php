<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\VersionController;
use Tests\TestCase;

class VersionControllerTest extends TestCase
{
    public function test_get_version()
    {
        $this->get('api/version')
            ->assertSuccessful()
            ->assertJsonStructure(
                [
                    'entity',
                    'version',
                    'build',
                ]
            );
    }

    public function test_sem_conseguir_rodar_o_git_a_versao_de_build_e_desconhecida()
    {
        $basePath = $this->app->basePath();
        $this->app->setBasePath('/caminho/que/nao/existe');

        try {
            $build = (fn () => $this->getBuildVersion())->call(new VersionController);
        } finally {
            $this->app->setBasePath($basePath);
        }

        $this->assertSame('unknow', $build);
    }
}
