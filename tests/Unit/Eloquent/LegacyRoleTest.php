<?php

use App\Models\LegacyEmployeeRole;
use App\Models\LegacyInstitution;
use App\Models\LegacyRole;
use App\Models\LegacyUser;
use Database\Factories\LegacyRoleFactory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyRole::class, relations: [
    'institution' => LegacyInstitution::class,
    'employeeRoles' => LegacyEmployeeRole::class,
    'deletedByUser' => LegacyUser::class,
    'createdByUser' => LegacyUser::class,
]));

EloquentModelTestCase::registerModelTests();

test('get id attribute', function () {
    $this->assertEquals($this->model->id, $this->model->cod_funcao);
});

test('scope professor', function () {
    $allowed = LegacyRoleFactory::new()->create(['professor' => 1]);
    $denied = LegacyRoleFactory::new()->create(['professor' => 0]);

    $roleQuery = LegacyRole::query()
        ->professor()
        ->ativo()
        ->whereKey([$allowed->getKey(), $denied->getKey()])
        ->get();

    $this->assertSame([$allowed->getKey()], $roleQuery->modelKeys());
    $this->assertEquals(1, $roleQuery->first()->ativo);
});
