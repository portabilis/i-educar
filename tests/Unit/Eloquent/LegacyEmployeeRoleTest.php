<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEmployeeRole;
use App\Models\LegacyRole;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyRoleFactory;
use PHPUnit\Framework\Attributes\DataProvider;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->legacyEmployeeRole = $this->createNewModel();
    }

    public function test_attributes()
    {
        $this->assertEquals($this->legacyEmployeeRole->id, $this->legacyEmployeeRole->cod_servidor_funcao);
    }

    public static function teacherRoleProvider(): array
    {
        return [
            'permitted active role' => [1, 1, true],
            'denied active role' => [0, 1, false],
            'permitted inactive role' => [1, 0, true],
            'denied inactive role' => [0, 0, false],
        ];
    }

    #[DataProvider('teacherRoleProvider')]
    public function test_teacher_role_filter_preserves_legacy_permission(int $permission, int $active, bool $expected): void
    {
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
    }

    protected function getAttributesForUpdate()
    {
        $attributes = parent::getAttributesForUpdate();
        $attributes['matricula'] = 'Matrícula atualizada';

        return $attributes;
    }
}
