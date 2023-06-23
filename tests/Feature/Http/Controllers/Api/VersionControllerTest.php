<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;

class VersionControllerTest extends TestCase
{
    public function testGetVersion()
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
