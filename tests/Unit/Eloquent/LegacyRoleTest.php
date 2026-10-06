<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEmployeeRole;
use App\Models\LegacyInstitution;
use App\Models\LegacyRole;
use App\Models\LegacyUser;
use Database\Factories\LegacyRoleFactory;
use Tests\EloquentTestCase;

class LegacyRoleTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
        'employeeRoles' => LegacyEmployeeRole::class,
        'deletedByUser' => LegacyUser::class,
        'createdByUser' => LegacyUser::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyRole::class;
    }

    public function test_get_id_attribute(): void
    {
        $this->assertEquals($this->model->id, $this->model->cod_funcao);
    }

    public function test_scope_professor(): void
    {
        $allowed = LegacyRoleFactory::new()->create(['professor' => 1]);
        $denied = LegacyRoleFactory::new()->create(['professor' => 0]);

        $roleQuery = LegacyRole::query()
            ->professor()
            ->ativo()
            ->whereKey([$allowed->getKey(), $denied->getKey()])
            ->get();

        $this->assertSame([$allowed->getKey()], $roleQuery->modelKeys());
        $this->assertEquals(1, $roleQuery->first()->ativo);
    }
}
