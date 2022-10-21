<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRole;
use Tests\EloquentTestCase;

class LegacyRoleTest extends EloquentTestCase
{
    private LegacyRole $role;

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyRole::class;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->role = $this->createNewModel();
    }

    /** @test */
    public function getIdAttribute()
    {
        $this->assertEquals($this->role->getIdAttribute(), $this->role->cod_funcao);
    }

    /** @test  */
    public function scopeProfessor()
    {
        $this->createNewModel();

        $roleQuery = LegacyRole::query()
            ->professor()
            ->ativo()
            ->first();

        $this->assertInstanceOf(LegacyRole::class, $roleQuery);
        $this->assertEquals(1, $roleQuery->professor);
        $this->assertEquals(1, $roleQuery->ativo);
    }
}
