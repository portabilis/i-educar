<?php

use App\Models\LegacyEmployeeRole;
use App\Models\LegacyRole;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyRoleFactory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(function () {
    $this->eloquent(LegacyEmployeeRole::class, relations: [
        'role' => LegacyRole::class,
    ], updateAttributes: function (array $attributes) {
        // Fixa a matrícula da atualização, para que sempre difira da gerada na criação.
        $attributes['matricula'] = 'Matrícula atualizada';

        return $attributes;
    });

    $this->legacyEmployeeRole = $this->createNewModel();
});

EloquentModelTestCase::registerModelTests();

test('attributes', function () {
    $this->assertEquals($this->legacyEmployeeRole->id, $this->legacyEmployeeRole->cod_servidor_funcao);
});

test('teacher role filter preserves legacy permission', function (int $permission, int $active, bool $expected) {
    $role = LegacyRoleFactory::new()->create([
        'professor' => $permission,
        'ativo' => $active,
    ]);
    $employeeRole = LegacyEmployeeRoleFactory::new()->create([
        'ref_cod_funcao' => $role->getKey(),
    ]);

    $this->assertSame($expected, LegacyEmployeeRole::query()
        ->whereKey($employeeRole->getKey())
        ->whereTeacherRole()
        ->exists());
})->with([
    'permitted active role' => [1, 1, true],
    'denied active role' => [0, 1, false],
    'permitted inactive role' => [1, 0, true],
    'denied inactive role' => [0, 0, false],
]);
