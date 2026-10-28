<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Modules\Servidores\Model\FuncaoExercida;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('role form saves class assignment permission', function (string $action, bool $checked) {
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
})->with([
    'create checked' => ['Novo', true],
    'create unchecked' => ['Novo', false],
    'edit checked' => ['Editar', true],
    'edit unchecked' => ['Editar', false],
]);

test('role form preserves legacy permission', function (int $permission) {
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
})->with([
    'formerly Professor Sim' => [1],
    'formerly Professor Nao' => [0],
]);

test('employee shows class assignment only for permitted roles', function (array $permissions, bool $allowed) {
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
})->with([
    'formerly Professor Sim' => [[1], true],
    'formerly Professor Nao' => [[0], false],
    'one permitted role among multiple roles' => [[0, 1], true],
]);

test('permitted non teacher can be assigned to a class', function () {
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

    $this->post('/intranet/educar_servidor_vinculo_turma_cad.php?' . $query, [
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
});
