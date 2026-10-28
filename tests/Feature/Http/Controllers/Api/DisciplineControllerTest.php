<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Process;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use OpenApiGenerator\Attributes\Controller;
use OpenApiGenerator\Attributes\DELETE;
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\PathParameter;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Type;
use OpenApiGenerator\Types\SchemaType;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());

    $school = LegacySchoolFactory::new()->create();
    $this->discipline = LegacyDisciplineFactory::new()->create();
    $grade = LegacyGradeFactory::new()->create();
    LegacySchoolGradeFactory::new()->create([
        'ref_cod_escola' => $school,
        'ref_cod_serie' => $grade,
    ]);
    $schoolClass = LegacySchoolClassFactory::new()->create([
        'ref_ref_cod_serie' => $grade,
        'ref_ref_cod_escola' => $school,
    ]);
    LegacySchoolGradeDisciplineFactory::new()->create([
        'ref_ref_cod_serie' => $grade,
        'ref_ref_cod_escola' => $school,
        'ref_cod_disciplina' => $this->discipline,
    ]);
    LegacySchoolClassGradeFactory::new()->create([
        'escola_id' => $school,
        'turma_id' => $schoolClass,
        'serie_id' => $grade,
    ]);
    LegacyDisciplineAcademicYearFactory::new()->create([
        'ano_escolar_id' => $grade,
        'componente_curricular_id' => $this->discipline,
        'hora_falta' => null,
    ]);
    LegacyDisciplineSchoolClassFactory::new()->create([
        'componente_curricular_id' => $this->discipline,
        'ano_escolar_id' => $grade,
        'escola_id' => $school,
        'turma_id' => $schoolClass,
    ]);
});

test('index', #[
    GET('/api/discipline', ['Discipline'], 'Get all disciplines'),
    Response(200, schemaType: SchemaType::ARRAY, ref: 'Discipline')
] function () {
    $response = $this->get('api/discipline');
    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'cod_turma',
                'cod_serie',
                'name',
                'abreviatura',
                'ordenamento',
                'area_conhecimento_id',
                'tipo_base',
                'etapas_especificas',
                'etapas_utilizadas',
                'carga_horaria',
            ],
        ],
    ]);
    $response->assertJson([
        'data' => [
            [
                'id' => $this->discipline->id,
                'name' => $this->discipline->name,
            ],
        ]);
    }

    #[
        DELETE('/api/discipline/{id}', ['Discipline'], 'Delete discipline with ID'),
        PathParameter('id', Type::INT, required: true, example: 1),
        Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'Discipline'),
    ]
    public function test_destroy(): void
    {
        $discipline = LegacyDisciplineFactory::new()->create();

        $response = $this->delete('api/discipline/' . $discipline->getKey());

        $response->assertOk();
        $this->assertDatabaseMissing($discipline->getTable(), ['id' => $discipline->getKey()]);
    }

    public function test_fail_destroy_discipline_linked_to_grade(): void
    {
        $response = $this->delete('api/discipline/' . $this->discipline->getKey());

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Este componente está vinculado a uma ou mais séries e não pode ser excluído para preservar o histórico.',
        ]);
        $this->assertDatabaseHas($this->discipline->getTable(), ['id' => $this->discipline->getKey()]);
    }

    public function test_fail_destroy_discipline_without_permission(): void
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->create());

        $discipline = LegacyDisciplineFactory::new()->create();

        $response = $this->delete('api/discipline/' . $discipline->getKey());

        $response->assertStatus(403);
        $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
    }

    public function test_destroy_discipline_with_permission(): void
    {
        $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(Process::DISCIPLINES)->create());

        $discipline = LegacyDisciplineFactory::new()->create();

        $response = $this->delete('api/discipline/' . $discipline->getKey());

        $response->assertOk();
        $this->assertDatabaseMissing($discipline->getTable(), ['id' => $discipline->getKey()]);
    }

    public function test_fail_destroy_discipline_not_found(): void
    {
        $response = $this->delete('api/discipline/0');

        $response->assertNotFound();
    }

    public function test_fail_destroy_discipline_twice(): void
    {
        $discipline = LegacyDisciplineFactory::new()->create();

        $this->delete('api/discipline/' . $discipline->getKey())->assertOk();

        $this->delete('api/discipline/' . $discipline->getKey())->assertNotFound();
    }

    public function test_fail_destroy_discipline_with_restricted_foreign_key(): void
    {
        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
        ]);

        $response = $this->delete('api/discipline/' . $discipline->getKey());

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Este componente está vinculado a uma ou mais séries e não pode ser excluído para preservar o histórico.',
        ]);
        $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
    }

    public function test_fail_destroy_discipline_dispensed_in_school_class(): void
    {
        $discipline = LegacyDisciplineFactory::new()->create();
        LegacySchoolClassFactory::new()->create([
            'ref_cod_disciplina_dispensada' => $discipline,
        ]);

        $response = $this->delete('api/discipline/' . $discipline->getKey());

        $response->assertStatus(422);
        $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
    }
}
