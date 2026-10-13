<?php

use App\Process;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyDisciplineAbsenceFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineDescriptiveOpinionFactory;
use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyDisciplineScoreAverageFactory;
use Database\Factories\LegacyDisciplineScoreFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyScoreExamFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
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
        ],
    ]);
});

test('destroy', #[
    DELETE('/api/discipline/{id}', ['Discipline'], 'Delete discipline with ID'),
    PathParameter('id', Type::INT, required: true, example: 1),
    Response(200, 'Success', schemaType: SchemaType::OBJECT, ref: 'Discipline')
] function () {
    $discipline = LegacyDisciplineFactory::new()->create();

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertOk();
    $this->assertDatabaseMissing($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('fail destroy discipline linked to grade', function () {
    $response = $this->delete('api/discipline/' . $this->discipline->getKey());

    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'Este componente está vinculado a uma ou mais séries e não pode ser excluído para preservar o histórico.',
    ]);
    $this->assertDatabaseHas($this->discipline->getTable(), ['id' => $this->discipline->getKey()]);
});

test('fail destroy discipline without permission', function () {
    $this->actingAs(LegacyUserFactory::new()->institutional()->create());

    $discipline = LegacyDisciplineFactory::new()->create();

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertStatus(403);
    $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('destroy discipline with permission', function () {
    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(Process::DISCIPLINES)->create());

    $discipline = LegacyDisciplineFactory::new()->create();

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertOk();
    $this->assertDatabaseMissing($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('fail destroy discipline not found', function () {
    $this->delete('api/discipline/0')->assertNotFound();
});

test('fail destroy discipline twice', function () {
    $discipline = LegacyDisciplineFactory::new()->create();

    $this->delete('api/discipline/' . $discipline->getKey())->assertOk();

    $this->delete('api/discipline/' . $discipline->getKey())->assertNotFound();
});

test('fail destroy discipline with restricted foreign key', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacyDisciplineSchoolClassFactory::new()->create([
        'componente_curricular_id' => $discipline,
    ]);

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'Este componente não pode ser excluído para preservar o histórico, pois possui vínculo com turmas, professores ou servidores.',
    ]);
    $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('fail destroy discipline dispensed in school class', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacySchoolClassFactory::new()->create([
        'ref_cod_disciplina_dispensada' => $discipline,
    ]);

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'Este componente não pode ser excluído para preservar o histórico, pois possui vínculo com turmas, professores ou servidores.',
    ]);
    $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('fail destroy discipline with score', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacyDisciplineScoreFactory::new()->create(['componente_curricular_id' => $discipline]);

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'Este componente não pode ser excluído para preservar o histórico, pois possui: notas lançadas.',
    ]);
    $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('fail destroy discipline with score average', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacyDisciplineScoreAverageFactory::new()->create(['componente_curricular_id' => $discipline]);

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'Este componente não pode ser excluído para preservar o histórico, pois possui: notas lançadas.',
    ]);
    $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('fail destroy discipline with exam score', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacyScoreExamFactory::new()->create(['ref_cod_componente_curricular' => $discipline]);

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'Este componente não pode ser excluído para preservar o histórico, pois possui: notas lançadas.',
    ]);
    $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('fail destroy discipline with absence', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacyDisciplineAbsenceFactory::new()->create(['componente_curricular_id' => $discipline]);

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'Este componente não pode ser excluído para preservar o histórico, pois possui: faltas lançadas.',
    ]);
    $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('fail destroy discipline with descriptive opinion', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacyDisciplineDescriptiveOpinionFactory::new()->create(['componente_curricular_id' => $discipline]);

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'Este componente não pode ser excluído para preservar o histórico, pois possui: pareceres descritivos.',
    ]);
    $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('fail destroy discipline with exemption', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacyDisciplineExemptionFactory::new()->create(['ref_cod_disciplina' => $discipline]);

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'Este componente não pode ser excluído para preservar o histórico, pois possui: dispensas de alunos.',
    ]);
    $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('fail destroy discipline with inactive exemption', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    LegacyDisciplineExemptionFactory::new()->create([
        'ref_cod_disciplina' => $discipline,
        'ativo' => 0,
    ]);

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'Este componente não pode ser excluído para preservar o histórico, pois possui: dispensas de alunos.',
    ]);
    $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('fail destroy discipline with schedule', function () {
    $discipline = LegacyDisciplineFactory::new()->create();
    $schoolClass = LegacySchoolClassFactory::new()->create();
    $employee = EmployeeFactory::new()->create();

    $scheduleId = DB::table('pmieducar.quadro_horario')->insertGetId([
        'ref_usuario_cad' => auth()->id(),
        'ref_cod_turma' => $schoolClass->getKey(),
        'data_cadastro' => now(),
    ], 'cod_quadro_horario');

    DB::table('pmieducar.quadro_horario_horarios')->insert([
        'ref_cod_quadro_horario' => $scheduleId,
        'sequencial' => 1,
        'ref_cod_disciplina' => $discipline->getKey(),
        'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
        'ref_cod_serie' => $schoolClass->ref_ref_cod_serie,
        'ref_servidor' => $employee->getKey(),
        'ref_cod_instituicao_servidor' => $employee->institution_id,
        'dia_semana' => 2,
        'hora_inicial' => '07:00',
        'hora_final' => '08:00',
        'data_cadastro' => now(),
    ]);

    $response = $this->delete('api/discipline/' . $discipline->getKey());

    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'Este componente não pode ser excluído para preservar o histórico, pois possui: horários no quadro de horários.',
    ]);
    $this->assertDatabaseHas($discipline->getTable(), ['id' => $discipline->getKey()]);
});

test('fail destroy discipline with grade and other links lists all of them', function () {
    LegacyDisciplineScoreFactory::new()->create(['componente_curricular_id' => $this->discipline]);
    LegacyDisciplineAbsenceFactory::new()->create(['componente_curricular_id' => $this->discipline]);

    $response = $this->delete('api/discipline/' . $this->discipline->getKey());

    $response->assertStatus(422);
    $response->assertJson([
        'message' => 'Este componente não pode ser excluído para preservar o histórico, pois possui: vínculo com séries, notas lançadas e faltas lançadas.',
    ]);
    $this->assertDatabaseHas($this->discipline->getTable(), ['id' => $this->discipline->getKey()]);
});
