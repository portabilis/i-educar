<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEmployeeRole;
use App\Models\LegacyRole;
use Tests\EloquentTestCase;

class LegacyEmployeeRoleTest extends EloquentTestCase
{
    public $relations = [
        'role' => LegacyRole::class,
    ];

    private LegacyEmployeeRole $legacyEmployeeRole;

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEmployeeRole::class;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->legacyEmployeeRole = $this->createNewModel();
    }

    /** @test */
    public function attributes()
    {
        $this->assertEquals($this->legacyEmployeeRole->id, $this->legacyEmployeeRole->cod_servidor_funcao);
    }
}
