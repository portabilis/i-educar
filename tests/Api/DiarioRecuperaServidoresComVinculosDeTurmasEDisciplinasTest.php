<?php

namespace Tests\Api;

use Carbon\Carbon;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolClassTeacherDisciplineFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DiarioRecuperaServidoresComVinculosDeTurmasEDisciplinasTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function test_recupera_servidores_com_vinculos_de_turmas_e_disciplinas()
    {
        $school = LegacySchoolFactory::new()->create();

        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();

        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos')
            ->assertJson(
                [
                    'vinculos' => [
                        0 => [
                            'id' => $legacySchoolClassTeacher->getKey(),
                            'servidor_id' => $employee->getKey(),
                            'turma_id' => $schoolClass->getKey(),
                            'turno_id' => $period->getKey(),
                            'permite_lancar_faltas_componente' => 0,
                            'deleted_at' => null,
                            'disciplinas' => [
                                [
                                    'id' => $discipline->getKey(),
                                    'serie_id' => $grade->getKey(),
                                    'tipo_nota' => $legacyDisciplineAcademicYear->tipo_nota,
                                ],
                            ],
                            'disciplinas_serie' => [
                                $grade->getKey() => [$discipline->getKey()],
                            ],
                        ],
                    ],
                    'oper' => 'get',
                    'resource' => 'servidores-disciplinas-turmas',
                    'msgs' => [],
                    'any_error_msg' => false,
                ]
            )
            ->assertJsonStructure(
                [
                    'vinculos' => [
                        '*' => [
                            'id',
                            'servidor_id',
                            'turma_id',
                            'turno_id',
                            'permite_lancar_faltas_componente',
                            'updated_at',
                            'deleted_at',
                            'disciplinas',
                            'disciplinas_serie',
                        ],
                    ],
                    'oper',
                    'resource',
                    'msgs',
                    'any_error_msg',
                ]
            );
    }

    public function test_recupera_servidores_com_modified_data_anterior()
    {
        // Criar dados com data específica (1 dia atrás)
        $dataAnterior = Carbon::now()->subDay();

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // Criar disciplina acadêmica com data anterior
        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataAnterior,
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // Criar professor_turma com data anterior
        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataAnterior,
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataAnterior->format('Y-m-d H:i:s'),
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Deve retornar o vínculo pois a data modified é igual à data dos registros
        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos');
    }

    public function test_recupera_servidores_com_modified_data_posterior()
    {
        // Criar dados com data específica (1 dia atrás)
        $dataAnterior = Carbon::now()->subDay();
        $dataModified = Carbon::now(); // Data atual (posterior aos dados)

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // Criar disciplina acadêmica com data anterior
        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataAnterior,
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // Criar professor_turma com data anterior
        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataAnterior,
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataModified->format('Y-m-d H:i:s'),
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Não deve retornar vínculos pois a data modified é posterior aos dados
        $response->assertSuccessful()
            ->assertJsonCount(0, 'vinculos');
    }

    public function test_recupera_servidores_com_modified_testando_pt_updated_at()
    {
        // Testa quando pt.updated_at é mais recente que ccae.updated_at
        $dataAntiga = Carbon::now()->subDays(2);
        $dataRecente = Carbon::now()->subDay();
        $dataModified = $dataRecente->copy()->subHour(); // 1 hora antes da data recente

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // Criar disciplina acadêmica com data ANTIGA
        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataAntiga,
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // Criar professor_turma com data RECENTE (greatest deve usar esta)
        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataRecente,
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataModified->format('Y-m-d H:i:s'),
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Deve retornar o vínculo pois greatest(pt.updated_at, ccae.updated_at) = pt.updated_at (mais recente) >= modified
        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos');
    }

    public function test_recupera_servidores_com_modified_testando_ccae_updated_at()
    {
        // Testa quando ccae.updated_at é mais recente que pt.updated_at
        $dataAntiga = Carbon::now()->subDays(2);
        $dataRecente = Carbon::now()->subDay();
        $dataModified = $dataRecente->copy()->subHour(); // 1 hora antes da data recente

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // Criar disciplina acadêmica com data RECENTE (greatest deve usar esta)
        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataRecente,
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // Criar professor_turma com data ANTIGA
        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataAntiga,
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataModified->format('Y-m-d H:i:s'),
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Deve retornar o vínculo pois greatest(pt.updated_at, ccae.updated_at) = ccae.updated_at (mais recente) >= modified
        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos');
    }

    public function test_recupera_servidores_com_modified_ambos_campos_antigos()
    {
        // Testa quando ambos pt.updated_at e ccae.updated_at são antigos
        $dataAntiga = Carbon::now()->subDays(2);
        $dataModified = Carbon::now()->subDay(); // Mais recente que ambos os campos

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // Criar disciplina acadêmica com data ANTIGA
        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataAntiga,
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // Criar professor_turma com data ANTIGA
        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataAntiga,
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataModified->format('Y-m-d H:i:s'),
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Não deve retornar vínculos pois greatest(pt.updated_at, ccae.updated_at) < modified
        $response->assertSuccessful()
            ->assertJsonCount(0, 'vinculos');
    }

    public function test_recupera_servidores_com_modified_multiplas_disciplinas_apenas_uma_modificada()
    {
        // TESTE PARA VERIFICAR CORREÇÃO DO BUG:
        // Professor tem 3 disciplinas, apenas 1 é modificada
        // Comportamento CORRETO: Deve retornar TODAS as 3 disciplinas do vínculo
        // (não apenas a disciplina modificada)

        $dataAntiga = Carbon::now()->subDays(2);
        $dataRecente = Carbon::now()->subDay();
        $dataModified = $dataRecente->copy()->subHour(); // Entre a data antiga e recente

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        // Criar 3 disciplinas
        $discipline1 = LegacyDisciplineFactory::new()->create();
        $discipline2 = LegacyDisciplineFactory::new()->create();
        $discipline3 = LegacyDisciplineFactory::new()->create();

        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline1,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline2,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline3,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // Criar componente_curricular_ano_escolar para as 3 disciplinas
        // Disciplinas 1 e 2 com data ANTIGA
        $legacyDisciplineAcademicYear1 = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline1,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataAntiga,
        ]);

        $legacyDisciplineAcademicYear2 = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline2,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataAntiga,
        ]);

        // Disciplina 3 com data RECENTE (modificada)
        $legacyDisciplineAcademicYear3 = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline3,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataRecente, // ESTA É A ÚNICA MODIFICADA
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // Criar professor_turma com data ANTIGA
        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataAntiga,
        ]);

        // Criar vínculos para as 3 disciplinas
        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline1,
        ]);
        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline2,
        ]);
        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline3,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataModified->format('Y-m-d H:i:s'),
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos');

        $vinculos = $response->json('vinculos');
        $vinculo = $vinculos[0];

        // COMPORTAMENTO CORRETO: Deve retornar TODAS as 3 disciplinas do vínculo
        // (mesmo que apenas 1 tenha sido modificada)
        $this->assertCount(3, $vinculo['disciplinas'],
            'Deve retornar todas as 3 disciplinas do vínculo, mesmo que apenas 1 tenha sido modificada');

        // Verifica se todas as disciplinas estão presentes
        $disciplinasIds = array_column($vinculo['disciplinas'], 'id');
        sort($disciplinasIds); // Ordena para comparação

        $expectedIds = [$discipline1->getKey(), $discipline2->getKey(), $discipline3->getKey()];
        sort($expectedIds); // Ordena para comparação

        $this->assertEquals($expectedIds, $disciplinasIds,
            'Todas as disciplinas do vínculo devem estar presentes');

        // Verifica individualmente cada disciplina
        $this->assertContains($discipline1->getKey(), $disciplinasIds,
            'Disciplina 1 (não modificada) deve estar presente');
        $this->assertContains($discipline2->getKey(), $disciplinasIds,
            'Disciplina 2 (não modificada) deve estar presente');
        $this->assertContains($discipline3->getKey(), $disciplinasIds,
            'Disciplina 3 (modificada) deve estar presente');
    }

    public function test_modified_apenas_professor_turma_modificado_deve_retornar_vinculo()
    {
        // TESTE: Modificar APENAS professor_turma (pt.updated_at)
        // ccae.updated_at permanece antigo
        // Deve retornar o vínculo pois greatest(pt.updated_at, ccae.updated_at) >= modified

        $dataAntiga = Carbon::now()->subDays(2);
        $dataRecente = Carbon::now()->subDay();
        $dataModified = $dataRecente->copy()->subHour(); // Entre a data antiga e recente

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // CCAE com data ANTIGA (não modificado)
        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataAntiga, // ANTIGO
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // PROFESSOR_TURMA com data RECENTE (MODIFICADO)
        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataRecente, // RECENTE - MODIFICADO
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataModified->format('Y-m-d H:i:s'),
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Deve retornar o vínculo pois greatest(dataRecente, dataAntiga) = dataRecente >= modified
        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos');

        $vinculos = $response->json('vinculos');
        $this->assertEquals($legacySchoolClassTeacher->getKey(), $vinculos[0]['id']);
    }

    public function test_modified_apenas_ccae_modificado_deve_retornar_vinculo()
    {
        // TESTE: Modificar APENAS ccae (ccae.updated_at)
        // pt.updated_at permanece antigo
        // Deve retornar o vínculo pois greatest(pt.updated_at, ccae.updated_at) >= modified

        $dataAntiga = Carbon::now()->subDays(2);
        $dataRecente = Carbon::now()->subDay();
        $dataModified = $dataRecente->copy()->subHour(); // Entre a data antiga e recente

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // CCAE com data RECENTE (MODIFICADO)
        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataRecente, // RECENTE - MODIFICADO
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // PROFESSOR_TURMA com data ANTIGA (não modificado)
        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataAntiga, // ANTIGO
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataModified->format('Y-m-d H:i:s'),
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Deve retornar o vínculo pois greatest(dataAntiga, dataRecente) = dataRecente >= modified
        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos');

        $vinculos = $response->json('vinculos');
        $this->assertEquals($legacySchoolClassTeacher->getKey(), $vinculos[0]['id']);
    }

    public function test_modified_apenas_professor_turma_antigo_nao_deve_retornar()
    {
        // TESTE: professor_turma antigo, ccae antigo, modified recente
        // Nenhum foi modificado após a data do modified
        // Não deve retornar vínculos

        $dataAntiga = Carbon::now()->subDays(2);
        $dataModified = Carbon::now()->subDay(); // Mais recente que ambos

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // CCAE com data ANTIGA
        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataAntiga, // ANTIGO
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // PROFESSOR_TURMA com data ANTIGA
        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataAntiga, // ANTIGO
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataModified->format('Y-m-d H:i:s'),
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Não deve retornar vínculos pois greatest(dataAntiga, dataAntiga) = dataAntiga < modified
        $response->assertSuccessful()
            ->assertJsonCount(0, 'vinculos');
    }

    public function test_modified_com_multiplos_vinculos_apenas_um_modificado()
    {
        // TESTE: 2 professores, apenas 1 tem vínculo modificado
        // Deve retornar apenas o vínculo modificado

        $dataAntiga = Carbon::now()->subDays(2);
        $dataRecente = Carbon::now()->subDay();
        $dataModified = $dataRecente->copy()->subHour(); // Entre a data antiga e recente

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // CCAE com data ANTIGA (mesmo para ambos os professores)
        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataAntiga,
        ]);

        $employee1 = EmployeeFactory::new()->create();
        $employee2 = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // PROFESSOR 1 - VÍNCULO ANTIGO (não modificado)
        $legacySchoolClassTeacher1 = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee1,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataAntiga, // ANTIGO
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher1,
            'componente_curricular_id' => $discipline,
        ]);

        // PROFESSOR 2 - VÍNCULO RECENTE (modificado)
        $legacySchoolClassTeacher2 = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee2,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataRecente, // RECENTE - MODIFICADO
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher2,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher1->instituicao_id,
            'ano' => $legacySchoolClassTeacher1->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataModified->format('Y-m-d H:i:s'),
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Deve retornar apenas 1 vínculo (o modificado)
        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos');

        $vinculos = $response->json('vinculos');
        // Deve ser o professor 2 (o modificado)
        $this->assertEquals($legacySchoolClassTeacher2->getKey(), $vinculos[0]['id']);
        $this->assertEquals($employee2->getKey(), $vinculos[0]['servidor_id']);
    }

    public function test_modified_data_exata_deve_retornar_vinculo()
    {
        // TESTE: modified com data EXATAMENTE igual ao updated_at
        // Deve retornar o vínculo (>= inclui igualdade)

        $dataExata = Carbon::now()->subDay();

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataExata, // MESMA DATA
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataExata, // MESMA DATA
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataExata->format('Y-m-d H:i:s'), // EXATAMENTE IGUAL
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Deve retornar o vínculo pois greatest(dataExata, dataExata) = dataExata >= modified (igualdade)
        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos');
    }

    public function test_modified_com_ambos_campos_modificados_recentemente()
    {
        // TESTE: Ambos pt.updated_at e ccae.updated_at são recentes
        // Deve retornar o vínculo (greatest pega qualquer um dos dois)

        $dataAntiga = Carbon::now()->subDays(3);
        $dataRecente1 = Carbon::now()->subDays(2);
        $dataRecente2 = Carbon::now()->subDay();
        $dataModified = $dataAntiga->copy()->addHour(); // Entre antiga e recente

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // CCAE com data RECENTE 1
        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataRecente1, // RECENTE
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // PROFESSOR_TURMA com data RECENTE 2 (ainda mais recente)
        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataRecente2, // MAIS RECENTE
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataModified->format('Y-m-d H:i:s'),
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Deve retornar o vínculo pois greatest(dataRecente2, dataRecente1) = dataRecente2 >= modified
        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos');
    }

    public function test_modified_sem_parametro_deve_retornar_todos()
    {
        // TESTE: Sem parâmetro modified deve retornar todos os vínculos
        // (independente das datas de updated_at)

        $dataAntiga = Carbon::now()->subDays(2);

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // CCAE com data ANTIGA
        $legacyDisciplineAcademicYear = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataAntiga, // ANTIGA
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // PROFESSOR_TURMA com data ANTIGA
        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataAntiga, // ANTIGA
        ]);

        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            // SEM parâmetro 'modified'
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Deve retornar o vínculo mesmo com datas antigas (sem filtro modified)
        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos');
    }

    public function test_modified_com_professor_multiplas_disciplinas_ccae_diferentes()
    {
        // TESTE: Professor com múltiplas disciplinas, cada ccae com updated_at diferente
        // Apenas algumas disciplinas modificadas após o modified
        // Deve retornar TODAS as disciplinas do vínculo (comportamento correto pós-correção)

        $dataAntiga = Carbon::now()->subDays(3);
        $dataMedia = Carbon::now()->subDays(2);
        $dataRecente = Carbon::now()->subDay();
        $dataModified = $dataMedia->copy()->subHour(); // Entre média e recente

        $school = LegacySchoolFactory::new()->create();
        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        // 3 disciplinas
        $discipline1 = LegacyDisciplineFactory::new()->create();
        $discipline2 = LegacyDisciplineFactory::new()->create();
        $discipline3 = LegacyDisciplineFactory::new()->create();

        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline1,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline2,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);
        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline3,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        // CCAE com datas diferentes
        // Disciplina 1: ANTIGA (não modificada)
        $legacyDisciplineAcademicYear1 = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline1,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataAntiga, // ANTIGA
        ]);

        // Disciplina 2: MÉDIA (modificada, mas antes do modified)
        $legacyDisciplineAcademicYear2 = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline2,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataMedia, // MÉDIA
        ]);

        // Disciplina 3: RECENTE (modificada após modified)
        $legacyDisciplineAcademicYear3 = LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline3,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'tipo_nota' => 1,
            'hora_falta' => null,
            'updated_at' => $dataRecente, // RECENTE
        ]);

        $employee = EmployeeFactory::new()->create();
        $period = LegacyPeriodFactory::new()->create();

        // PROFESSOR_TURMA com data ANTIGA
        $legacySchoolClassTeacher = LegacySchoolClassTeacherFactory::new()->create([
            'servidor_id' => $employee,
            'turma_id' => $schoolClass,
            'turno_id' => $period,
            'updated_at' => $dataAntiga, // ANTIGA
        ]);

        // Vínculos para as 3 disciplinas
        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline1,
        ]);
        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline2,
        ]);
        LegacySchoolClassTeacherDisciplineFactory::new()->create([
            'professor_turma_id' => $legacySchoolClassTeacher,
            'componente_curricular_id' => $discipline3,
        ]);

        LegacySchoolClassGradeFactory::new()->create([
            'escola_id' => $school,
            'serie_id' => $grade,
            'turma_id' => $schoolClass,
        ]);

        $data = [
            'oper' => 'get',
            'resource' => 'servidores-disciplinas-turmas',
            'instituicao_id' => $legacySchoolClassTeacher->instituicao_id,
            'ano' => $legacySchoolClassTeacher->ano,
            'escola' => $schoolClass->ref_ref_cod_escola,
            'modified' => $dataModified->format('Y-m-d H:i:s'),
        ];

        $response = $this->getResource('/module/Api/Servidor', $data);

        // Deve retornar o vínculo pois greatest(dataAntiga, max(dataAntiga, dataMedia, dataRecente)) = dataRecente >= modified
        $response->assertSuccessful()
            ->assertJsonCount(1, 'vinculos');

        $vinculos = $response->json('vinculos');
        $vinculo = $vinculos[0];

        // Deve retornar TODAS as 3 disciplinas do vínculo
        $this->assertCount(3, $vinculo['disciplinas'],
            'Deve retornar todas as disciplinas do vínculo, mesmo que apenas algumas tenham sido modificadas');

        // Verifica se todas as disciplinas estão presentes
        $disciplinasIds = array_column($vinculo['disciplinas'], 'id');
        sort($disciplinasIds);

        $expectedIds = [$discipline1->getKey(), $discipline2->getKey(), $discipline3->getKey()];
        sort($expectedIds);

        $this->assertEquals($expectedIds, $disciplinasIds,
            'Todas as disciplinas do vínculo devem estar presentes');
    }
}
