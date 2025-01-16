<?php

namespace Tests\Feature\Http\Controllers\Api;

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
}
