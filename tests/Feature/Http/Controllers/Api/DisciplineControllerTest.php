<?php

namespace Tests\Feature\Http\Controllers\Api;

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
use OpenApiGenerator\Attributes\GET;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Types\SchemaType;
use Tests\TestCase;

#[Controller]
class DisciplineControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $discipline;

    public function setUp(): void
    {
        parent::setUp();
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
    }

    #[
        GET('/api/discipline', ['Discipline'], 'Get all disciplines'),
        Response(200, schemaType: SchemaType::ARRAY, ref: 'Discipline')
    ]
    public function testIndex(): void
    {
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
            ],
        ]);
    }
}
