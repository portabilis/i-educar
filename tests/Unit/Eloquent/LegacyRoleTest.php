<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyInstitution;
use App\Models\LegacyRole;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyRoleTest extends EloquentTestCase
{
    private LegacyRole $role;

    public $relations = [
        'institution' => LegacyInstitution::class,
        'deletedByUser' => LegacyUser::class,
        'createdByUser' => LegacyUser::class,
    ];

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
        $this->assertEquals($this->role->id, $this->role->cod_funcao);
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
