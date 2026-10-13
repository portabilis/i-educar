<?php

namespace Tests\Feature;

use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacyUserFactory;
use DOMDocument;
use DOMXPath;
use iEducar\Modules\Servidores\Model\FuncaoExercida;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ServerClassAssignmentPermissionTest extends TestCase
{
    use DatabaseTransactions;
    private const SERVER_CLASS_ASSIGNMENT_URL = '/intranet/educar_servidor_vinculo_turma_cad.php?';

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(LegacyUserFactory::new()->admin()->create());
    }

    public static function savePermissionProvider(): array
    {
        return [
            'create checked' => ['Novo', true],
            'create unchecked' => ['Novo', false],
            'edit checked' => ['Editar', true],
            'edit unchecked' => ['Editar', false],
        ];
    }

    #[DataProvider('savePermissionProvider')]
    public function test_role_form_saves_class_assignment_permission(string $action, bool $checked): void
    {
        $factory = LegacyRoleFactory::new();
        $role = $action === 'Editar'
            ? $factory->create(['professor' => (int) !$checked])
            : $factory->make();

        $payload = [
            'tipoacao' => $action,
            'ref_cod_instituicao' => $role->ref_cod_instituicao,
            'nm_funcao' => $role->nm_funcao,
            'abreviatura' => $role->abreviatura,
        ];

        if ($action === 'Editar') {
            $payload['cod_funcao'] = $role->getKey();
        }

        // Browsers omit unchecked checkboxes from the submitted form.
        if ($checked) {
            $payload['professor'] = 'on';
        }

        $this->post('/intranet/educar_funcao_cad.php', $payload)
            ->assertRedirectContains('educar_funcao_lst.php');

        unset($payload['tipoacao']);
        $payload['professor'] = (int) $checked;

        $this->assertDatabaseHas('pmieducar.funcao', $payload);
    }

    public static function legacyPermissionProvider(): array
    {
        return [
            'formerly Professor Sim' => [1],
            'formerly Professor Nao' => [0],
        ];
    }

    #[DataProvider('legacyPermissionProvider')]
    public function test_role_form_preserves_legacy_permission(int $permission): void
    {
        $role = LegacyRoleFactory::new()->create(['professor' => $permission]);

        $response = $this->get('/intranet/educar_funcao_cad.php?cod_funcao=' . $role->getKey())
            ->assertOk()
            ->assertSee('Permitir vincular a turmas?');

        $document = new DOMDocument;
        @$document->loadHTML($response->getContent());
        $checkboxes = (new DOMXPath($document))->query('//input[@name="professor" and @type="checkbox"]');

        $this->assertCount(1, $checkboxes);
        $this->assertSame((bool) $permission, $checkboxes->item(0)->hasAttribute('checked'));
        $this->assertEquals($permission, $role->fresh()->professor);
    }

    public static function employeePermissionProvider(): array
    {
        return [
            'formerly Professor Sim' => [[1], true],
            'formerly Professor Nao' => [[0], false],
            'one permitted role among multiple roles' => [[0, 1], true],
        ];
    }

    #[DataProvider('employeePermissionProvider')]
    public function test_employee_shows_class_assignment_only_for_permitted_roles(array $permissions, bool $allowed): void
    {
        $employee = EmployeeFactory::new()->create();

        foreach ($permissions as $permission) {
            $role = LegacyRoleFactory::new()->create([
                'professor' => $permission,
                'ref_cod_instituicao' => $employee->ref_cod_instituicao,
            ]);

            LegacyEmployeeRoleFactory::new()->create([
                'ref_cod_servidor' => $employee->getKey(),
                'ref_cod_funcao' => $role->getKey(),
                'ref_ref_cod_instituicao' => $employee->ref_cod_instituicao,
            ]);
        }

        $response = $this->get('/intranet/educar_servidor_det.php?' . http_build_query([
            'cod_servidor' => $employee->getKey(),
            'ref_cod_instituicao' => $employee->ref_cod_instituicao,
        ]))->assertOk();

        if ($allowed) {
            $response->assertSee('Vincular servidor a turma')
                ->assertSee('educar_servidor_vinculo_turma_lst.php');
        } else {
            $response->assertDontSee('Vincular servidor a turma')
                ->assertDontSee('educar_servidor_vinculo_turma_lst.php');
        }
    }

    public function test_permitted_non_teacher_can_be_assigned_to_a_class(): void
    {
        $employee = EmployeeFactory::new()->create();
        $role = LegacyRoleFactory::new()->create([
            'nm_funcao' => 'Tradutor Intérprete de LIBRAS',
            'professor' => 1,
            'ref_cod_instituicao' => $employee->ref_cod_instituicao,
        ]);
        LegacyEmployeeRoleFactory::new()->create([
            'ref_cod_servidor' => $employee->getKey(),
            'ref_cod_funcao' => $role->getKey(),
            'ref_ref_cod_instituicao' => $employee->ref_cod_instituicao,
        ]);
        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_cod_instituicao' => $employee->ref_cod_instituicao,
        ]);
        $query = http_build_query([
            'ref_cod_servidor' => $employee->getKey(),
            'ref_cod_instituicao' => $employee->ref_cod_instituicao,
        ]);

        $this->post(self::SERVER_CLASS_ASSIGNMENT_URL. $query, [
            'tipoacao' => 'Novo',
            'servidor_id' => $employee->getKey(),
            'ref_cod_instituicao' => $employee->ref_cod_instituicao,
            'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
            'ref_cod_turma' => $schoolClass->getKey(),
            'ano' => $schoolClass->ano,
            'funcao_exercida' => FuncaoExercida::INTERPRETE_LIBRAS,
        ])->assertRedirectContains('educar_servidor_vinculo_turma_lst.php');

        $this->assertDatabaseHas('modules.professor_turma', [
            'servidor_id' => $employee->getKey(),
            'turma_id' => $schoolClass->getKey(),
            'instituicao_id' => $employee->ref_cod_instituicao,
            'ano' => $schoolClass->ano,
            'funcao_exercida' => FuncaoExercida::INTERPRETE_LIBRAS,
        ]);
    }

    public function test_cannot_create_teacher_class_assignment_for_finalized_academic_year(): void
    {
        $employee = EmployeeFactory::new()->create();$schoolClass = $this->createClassWithFinalizedAcademicYear();$query = http_build_query([
            'ref_cod_servidor' => $employee->getKey(),
            'ref_cod_instituicao' => $schoolClass->ref_cod_instituicao,
        ]);

        $this->assertForbiddenPost(self::SERVER_CLASS_ASSIGNMENT_URL .$query, [
            'tipoacao' => 'Novo',
            'servidor_id' => $employee->getKey(),
            'ref_cod_instituicao' => $schoolClass->ref_cod_instituicao,
            'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
            'ref_cod_turma' => $schoolClass->getKey(),
            'ano' => $schoolClass->ano,
        ]);

        $this->assertDatabaseMissing('modules.professor_turma', [
            'servidor_id' => $employee->getKey(),
            'turma_id' => $schoolClass->getKey(),
        ]);
    }

    public function test_cannot_edit_teacher_class_assignment_for_finalized_academic_year(): void
    {
        [
            'schoolClass' => $schoolClass,
            'assignment' => $assignment,
            'query' => $query,
        ] = $this->createAssignmentWithFinalizedAcademicYear();

        $this->assertForbiddenPost(self::SERVER_CLASS_ASSIGNMENT_URL .$query, [
            'tipoacao' => 'Editar',
            'id' => $assignment->getKey(),
            'servidor_id' => $assignment->servidor_id,
            'ref_cod_instituicao' => $schoolClass->ref_cod_instituicao,
            'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
            'ref_cod_turma' => $schoolClass->getKey(),
            'ano' => $schoolClass->ano,
            'tipo_vinculo' => 1,
        ]);

        $this->assertDatabaseHas('modules.professor_turma', [
            'id' => $assignment->getKey(),
            'tipo_vinculo' => $assignment->tipo_vinculo,
        ]);
    }

    public function test_cannot_delete_teacher_class_assignment_for_finalized_academic_year(): void
    {
        [
            'schoolClass' => $schoolClass,
            'assignment' => $assignment,
            'query' => $query,
        ] = $this->createAssignmentWithFinalizedAcademicYear();

        $this->assertForbiddenPost(self::SERVER_CLASS_ASSIGNMENT_URL .$query, [
            'tipoacao' => 'Excluir',
            'id' => $assignment->getKey(),
            'servidor_id' => $assignment->servidor_id,
            'ref_cod_instituicao' => $schoolClass->ref_cod_instituicao,
        ]);

        $this->assertDatabaseHas('modules.professor_turma', [
            'id' => $assignment->getKey(),
        ]);
    }
    
    private function createAssignmentWithFinalizedAcademicYear(): array
    {
        $schoolClass = $this->createClassWithFinalizedAcademicYear();
        
        $assignment = LegacySchoolClassTeacherFactory::new()->create([
            'ano' => $schoolClass->ano,
            'instituicao_id' => $schoolClass->ref_cod_instituicao,
            'turma_id' => $schoolClass->getKey(),
        ]);

        $query = http_build_query([
            'id' => $assignment->getKey(),
            'ref_cod_servidor' => $assignment->servidor_id,
            'ref_cod_instituicao' => $schoolClass->ref_cod_instituicao,
        ]);

        return [
            'schoolClass' => $schoolClass,
            'assignment' => $assignment,
            'query' => $query,
        ];
    }

    private function createClassWithFinalizedAcademicYear(): LegacySchoolClass
    {
        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ano' => 2024,
        ]);

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
            'ano' => $schoolClass->ano,
            'andamento' => LegacySchoolAcademicYear::FINALIZED,
            'ativo' => 1,
        ]);

        return $schoolClass;
    }

    private function assertForbiddenPost(string $uri, array $payload): void
    {
        $initialOutputBufferLevel = ob_get_level();
        try {
            $this->post($uri, $payload)->assertForbidden();
        } finally {
            while (ob_get_level() > $initialOutputBufferLevel) {
                ob_end_clean();
            }
        }
    }
}
