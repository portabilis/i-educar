<?php

namespace Tests;

use App\Models\Employee;
use App\Models\EmployeeGraduation;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyCourse;
use App\Models\LegacyDiscipline;
use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;
use App\Models\LegacyEnrollment;
use App\Models\LegacyGrade;
use App\Models\LegacyIndividual;
use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClassTeacher;
use App\Models\LegacySchoolGradeDiscipline;
use App\Models\LegacySchoolingDegree;
use App\Models\LegacyStageType;
use App\Models\LegacyStudent;
use App\Models\Place;
use App\Models\SchoolInep;
use App\Models\SchoolManager;
use App\User;
use Carbon\Carbon;
use Database\Factories\CityFactory;
use Database\Factories\CountryFactory;
use Database\Factories\DistrictFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\StateFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

abstract class EducacensoTestCase extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    protected int $year;
    protected User $user;
    protected Carbon $dateEnrollment;

    public function setUp(): void
    {
        parent::setUp();
        \Artisan::call('db:seed', ['--class' => 'DefaultPmieducarTurmaTurnoTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultManagerRolesTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultManagerLinkTypesTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultManagerAccessCriteriasTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultModulesEducacensoIesTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultModulesEducacensoCursoSuperiorTableSeeder']);

        DistrictFactory::new()->create([
            'name' => 'IÇARA',
            'ibge_code' => '420700705',
            'city_id' => CityFactory::new()->create([
                'state_id' => StateFactory::new()->create([
                    'country_id' => CountryFactory::new()->create([
                        'id' => 1,
                        'name' => 'Brasil',
                        'ibge_code' => '76',
                    ]),
                    'name' => 'Santa Catarina',
                    'abbreviation' => 'SC',
                    'ibge_code' => '42',
                ]),
                'name' => 'IÇARA',
                'ibge_code' => '4207007',
            ])
        ]);

        $this->user = LegacyUserFactory::new()->admin()->create();

        $this->actingAs($this->user);
    }

    /** @test */
    public function validationImportRegister00()
    {
        $count = LegacySchool::count();
        $legacySchool = LegacySchool::first();

        $this->assertEquals(1, $count);
        $this->assertEquals('ESCOLA PORTABILIS', $legacySchool->name);
        $this->assertTrue($legacySchool->data_cadastro->isToday());
        $this->assertEquals(1, $legacySchool->ativo);
        $this->assertEquals($this->user->cod_usuario, $legacySchool->ref_usuario_cad);
        $this->assertEquals('ESCOL', $legacySchool->sigla);
        $this->assertEquals(1, $legacySchool->situacao_funcionamento);
        $this->assertEquals(3, $legacySchool->dependencia_administrativa);

        $schoolInep = $legacySchool->inep;
        $this->assertNotNull($schoolInep);
        $this->assertInstanceOf(SchoolInep::class, $schoolInep);
        $this->assertTrue($schoolInep->created_at->isToday());
        $this->assertEquals('importador', $schoolInep->fonte);
        $this->assertEquals('12345678', $schoolInep->cod_escola_inep);

        $organization = $legacySchool->organization;
        $this->assertNotNull($organization);
        $this->assertInstanceOf(LegacyOrganization::class, $organization);
        $this->assertTrue($organization->data_cad->isToday());
        $this->assertEquals('ESCOLA PORTABILIS', $organization->fantasia);
        $this->assertEquals($organization->fantasia, $legacySchool->name);
        $this->assertEquals($this->user->cod_usuario, $organization->idpes_cad);
        $this->assertEquals($legacySchool->ref_usuario_cad, $organization->idpes_cad);
        $this->assertEquals('I', $organization->operacao);

        $person = $legacySchool->person;
        $this->assertNotNull($person);
        $this->assertInstanceOf(LegacyPerson::class, $person);
        $this->assertEquals('PORTABILIS@PORTABILIS.COM.BR', $person->email);
        $this->assertTrue($person->data_cad->isToday());
        $this->assertEquals('escola portabilis', $person->slug);
        $this->assertEquals('J', $person->tipo);

        $this->assertNotNull($legacySchool->address);
        $this->assertCount(1, $legacySchool->address);
        $address = $legacySchool->address->first();
        $this->assertInstanceOf(Place::class, $address);
        $this->assertTrue($address->created_at->isToday());
        $this->assertEquals('0', $address->number);
        $this->assertEquals('CENTRO', $address->neighborhood);
        $this->assertEquals('RUA VITORIA', $address->address);
        $this->assertEquals('4207007', $address->city->ibge_code);
        $this->assertEquals('42', $address->city->state->ibge_code);
        $this->assertEquals('76', $address->city->state->country->ibge_code);

        $this->assertNotNull($legacySchool->stages);
        $this->assertCount(1, $legacySchool->stages);
        $legacyAcademicYearStage = $legacySchool->stages->first();
        $this->assertNotNull($legacyAcademicYearStage);
        $this->assertInstanceOf(LegacyAcademicYearStage::class, $legacyAcademicYearStage);
        $this->assertEquals($this->year, $legacyAcademicYearStage->ref_ano);
        $this->assertEquals($legacySchool->cod_escola, $legacyAcademicYearStage->ref_ref_cod_escola);
        $this->assertEquals(1, $legacyAcademicYearStage->sequencial);
        $this->assertEquals(200, $legacyAcademicYearStage->dias_letivos);

        $module = $legacyAcademicYearStage->module;
        $this->assertNotNull($module);
        $this->assertInstanceOf(LegacyStageType::class, $module);
        $this->assertEquals($legacyAcademicYearStage->ref_cod_modulo, $module->cod_modulo);
        $this->assertEquals($legacySchool->ref_usuario_cad, $module->ref_usuario_cad);
        $this->assertEquals($this->user->cod_usuario, $module->ref_usuario_cad);
        $this->assertEquals($legacySchool->ref_cod_instituicao, $module->ref_cod_instituicao);
        $this->assertEquals(1, $module->num_etapas);
        $this->assertEquals(1, $module->ativo);

        $this->assertNotNull($legacySchool->inep);
        $this->assertIsNumeric($legacySchool->inep->cod_escola_inep);
        $this->assertEquals(8, strlen($legacySchool->inep->cod_escola_inep));
        $this->assertTrue($legacySchool->inep->created_at->isToday());

        return $legacySchool;
    }

    /**
     * @test
     *
     * @depends validationImportRegister00
     */
    public function validationImportRegister10(LegacySchool $legacySchool)
    {
        $this->assertEquals('{3}', $legacySchool->local_funcionamento);
        $this->assertEquals(1, $legacySchool->agua_potavel_consumo);
        $this->assertEquals('{4}', $legacySchool->abastecimento_agua);
        $this->assertEquals('{1}', $legacySchool->abastecimento_energia);
        $this->assertEquals('{2,7,3}', $legacySchool->destinacao_lixo);
        $this->assertEquals('{1}', $legacySchool->tratamento_lixo);
        $this->assertEquals(1, $legacySchool->possui_dependencias);
        $this->assertEquals('{1}', $legacySchool->recursos_acessibilidade);
        $this->assertEquals(0, $legacySchool->acesso_internet);
    }

    /** @test */
    public function validationImportRegister20()
    {
        $legacySchool = LegacySchool::first();

        $this->assertNotNull($legacySchool->schoolClasses);
        $this->assertCount(2, $legacySchool->schoolClasses);

        list($schoolClasses01, $schoolClasses02) = $legacySchool->schoolClasses;

        $this->assertEquals($this->user->cod_usuario, $schoolClasses01->ref_usuario_cad);
        $this->assertEquals($legacySchool->cod_escola, $schoolClasses01->ref_ref_cod_escola);
        $this->assertEquals($legacySchool->ref_cod_instituicao, $schoolClasses01->ref_cod_instituicao);
        $this->assertEquals($this->user->ref_cod_instituicao, $schoolClasses01->ref_cod_instituicao);
        $this->assertTrue($schoolClasses01->data_cadastro->isToday());
        $this->assertEquals($this->year, $schoolClasses01->ano);
        $this->assertEquals(2, $schoolClasses01->turma_turno_id);
        $this->assertEquals(1, $schoolClasses01->ativo);
        $this->assertNotNull($schoolClasses01->etapa_educacenso);
        $this->assertTrue(in_array($schoolClasses01->etapa_educacenso, [
            22,
            35
        ]));
        $this->assertEquals('13:15:00', $schoolClasses01->hora_inicial);
        $this->assertEquals('17:15:00', $schoolClasses01->hora_final);
        $this->assertNotNull($schoolClasses01->inep);
        $this->assertIsNumeric($schoolClasses01->inep->cod_turma_inep);
        $this->assertEquals(8, strlen($schoolClasses01->inep->cod_turma_inep));
        $this->assertTrue($schoolClasses01->inep->created_at->isToday());
        $this->assertNotNull($schoolClasses01->grade);

        $this->assertEquals($this->user->cod_usuario, $schoolClasses02->ref_usuario_cad);
        $this->assertEquals($legacySchool->cod_escola, $schoolClasses02->ref_ref_cod_escola);
        $this->assertEquals($legacySchool->ref_cod_instituicao, $schoolClasses02->ref_cod_instituicao);
        $this->assertEquals($this->user->ref_cod_instituicao, $schoolClasses02->ref_cod_instituicao);
        $this->assertTrue($schoolClasses02->data_cadastro->isToday());
        $this->assertEquals($this->year, $schoolClasses02->ano);
        $this->assertEquals(2, $schoolClasses02->turma_turno_id);
        $this->assertEquals(1, $schoolClasses02->ativo);
        $this->assertNotNull($schoolClasses02->etapa_educacenso);
        $this->assertTrue(in_array($schoolClasses02->etapa_educacenso, [
            22,
            35
        ]));
        $this->assertEquals('13:15:00', $schoolClasses02->hora_inicial);
        $this->assertEquals('17:15:00', $schoolClasses02->hora_final);
        $this->assertNotNull($schoolClasses02->inep);
        $this->assertIsNumeric($schoolClasses02->inep->cod_turma_inep);
        $this->assertEquals(8, strlen($schoolClasses02->inep->cod_turma_inep));
        $this->assertTrue($schoolClasses02->inep->created_at->isToday());
        $this->assertNotNull($schoolClasses02->grade);

        $grade = $schoolClasses01->grade;
        $this->assertNotNull($grade);
        $this->assertInstanceOf(LegacyGrade::class, $grade);
        $this->assertEquals($this->user->cod_usuario, $grade->ref_usuario_cad);
        $this->assertTrue($grade->data_cadastro->isToday());
        $this->assertEquals(1, $grade->ativo);
        $this->assertEquals(200, $grade->dias_letivos);
        $this->assertEquals('800', $grade->carga_horaria);
        $this->assertEquals(1, $grade->etapa_curso);

        $course = $grade->course;
        $this->assertNotNull($course);
        $this->assertInstanceOf(LegacyCourse::class, $course);
        $this->assertEquals($this->user->cod_usuario, $course->ref_usuario_cad);
        $this->assertEquals('800', $course->carga_horaria);
        $this->assertTrue($course->data_cadastro->isToday());
        $this->assertEquals(1, $course->ativo);
        $this->assertEquals(1, $course->padrao_ano_escolar);
        $this->assertEquals('Ensino Fundamental de 9 anos - Multi', $course->name);
        $this->assertEquals('Ensino Fundamen', $course->sgl_curso);
        $this->assertEquals($legacySchool->ref_cod_instituicao, $course->ref_cod_instituicao);

        $educationType = $course->educationType;
        $this->assertNotNull($educationType);
        $this->assertInstanceOf(LegacyEducationType::class, $educationType);
        $this->assertEquals($legacySchool->ref_cod_instituicao, $educationType->ref_cod_instituicao);
        $this->assertEquals($this->user->cod_usuario, $educationType->ref_usuario_cad);
        $this->assertEquals(1, $educationType->ativo);
        $this->assertEquals('Padrão', $educationType->nm_tipo);
        $this->assertTrue($educationType->data_cadastro->isToday());

        $educationLevel = $course->educationLevel;
        $this->assertNotNull($educationLevel);
        $this->assertInstanceOf(LegacyEducationLevel::class, $educationLevel);
        $this->assertEquals($legacySchool->ref_cod_instituicao, $educationLevel->ref_cod_instituicao);
        $this->assertEquals($this->user->cod_usuario, $educationLevel->ref_usuario_cad);
        $this->assertEquals('Ano', $educationLevel->nm_nivel);
        $this->assertTrue($educationLevel->data_cadastro->isToday());
        $this->assertEquals(1, $educationLevel->ativo);

        $schoolGradeDisciplines = LegacySchoolGradeDiscipline::query()
            ->where('ref_ref_cod_escola', $legacySchool->getKey())
            ->where('ref_ref_cod_serie', $grade->getKey())
            ->get();

        $this->assertNotEmpty($schoolGradeDisciplines);
        $this->assertCount(8, $schoolGradeDisciplines);
        foreach ($schoolGradeDisciplines as $schoolGradeDiscipline) {
            $this->assertEquals(1, $schoolGradeDiscipline->ativo);
            $this->assertEquals('{' . $this->year . '}', $schoolGradeDiscipline->anos_letivos);

            $discipline = $schoolGradeDiscipline->discipline;
            $this->assertNotNull($discipline);
            $this->assertInstanceOf(LegacyDiscipline::class, $discipline);
            $this->assertNotNull($discipline->codigo_educacenso);
        }
    }

    /** @test */
    public function validationImportRegister30()
    {
        $students = LegacyStudent::all();

        $this->assertNotEmpty($students);
        $this->assertCount(10, $students);

        foreach ($students as $student) {
            $this->assertInstanceOf(LegacyStudent::class, $student);
            $this->assertTrue($student->data_cadastro->isToday());
            $this->assertEquals(1, $student->ativo);

            $individual = $student->individual;
            $this->assertNotNull($individual);
            $this->assertInstanceOf(LegacyIndividual::class, $individual);
            $this->assertTrue($individual->data_cad->isToday());
            $this->assertEquals(1, $individual->ativo);
            $this->assertEquals('I', $individual->operacao);
            $this->assertNotNull($individual->pais_residencia);
            $this->assertNotNull($individual->idpes_pai);
            $this->assertNotNull($individual->idpes_mae);
            $this->assertNotNull($individual->idmun_nascimento);
            $this->assertNotNull($individual->nacionalidade);
            $this->assertNotNull($individual->sexo);
            $this->assertNotNull($individual->data_nasc);

            $person = $student->person;
            $this->assertNotNull($person);
            $this->assertInstanceOf(LegacyPerson::class, $person);
            $this->assertEquals('F', $person->tipo);
            $this->assertTrue($person->data_cad->isToday());
            $this->assertNotNull($person->nome);
            $this->assertEquals('I', $person->operacao);
            $this->assertNotNull($person->slug);
        }

        $employees = Employee::all();

        $this->assertNotEmpty($employees);
        $this->assertCount(3, $employees);

        foreach ($employees as $employee) {
            $this->assertInstanceOf(Employee::class, $employee);
            $this->assertTrue($employee->data_cadastro->isToday());
            $this->assertEquals(1, $employee->ativo);
            $this->assertEquals($this->user->ref_cod_instituicao, $employee->ref_cod_instituicao);

            $this->assertNotNull($employee->schoolingDegree);
            $this->assertInstanceOf(LegacySchoolingDegree::class, $employee->schoolingDegree);
            $this->assertNotNull($employee->schoolingDegree->descricao);
            $this->assertNotNull($employee->schoolingDegree->escolaridade);

            if ($employee->schoolingDegree->escolaridade == 6) {
                foreach ($employee->graduations as $graduation) {
                    $this->assertInstanceOf(EmployeeGraduation::class, $graduation);
                    $this->assertNotNull($graduation->course_id);
                    $this->assertNotNull($graduation->completion_year);
                    $this->assertNotNull($graduation->college_id);
                }
            }

            $individual = $employee->individual;
            $this->assertNotNull($individual);
            $this->assertInstanceOf(LegacyIndividual::class, $individual);
            $this->assertTrue($individual->data_cad->isToday());
            $this->assertEquals(1, $individual->ativo);
            $this->assertEquals('I', $individual->operacao);
            $this->assertNotNull($individual->cpf);
            $this->assertTrue(validaCPF($individual->cpf));
            $this->assertNotNull($individual->pais_residencia);
            $this->assertNotNull($individual->idmun_nascimento);
            $this->assertNotNull($individual->nacionalidade);
            $this->assertNotNull($individual->sexo);
            $this->assertNotNull($individual->data_nasc);

            $person = $employee->person;
            $this->assertNotNull($person);
            $this->assertInstanceOf(LegacyPerson::class, $person);
            $this->assertEquals('F', $person->tipo);
            $this->assertTrue($person->data_cad->isToday());
            $this->assertNotNull($person->nome);
            $this->assertEquals('I', $person->operacao);
            $this->assertNotNull($person->slug);

            if ($person->email) {
                $this->assertSame($person->email, filter_var($person->email, FILTER_VALIDATE_EMAIL));
            }
        }
    }

    /** @test */
    public function validationImportRegister40()
    {
        $schoolManager = SchoolManager::all();

        $this->assertNotNull($schoolManager);
        $this->assertCount(1, $schoolManager);

        $schoolManager = $schoolManager->first();
        $this->assertNotNull($schoolManager->employee->inep->number);
        $this->assertIsNumeric($schoolManager->employee->inep->number);
        $this->assertEquals(12, strlen($schoolManager->employee->inep->number));
    }

    /** @test */
    public function validationImportRegister50()
    {
        $schoollClassTeachers = LegacySchoolClassTeacher::all();
        $this->assertNotNull($schoollClassTeachers);
        $this->assertCount(2, $schoollClassTeachers);

        foreach ($schoollClassTeachers as $schoollClassTeacher) {
            $this->assertNotNull($schoollClassTeacher->schoolClassTeacherDisciplines);
            $this->assertCount(8, $schoollClassTeacher->schoolClassTeacherDisciplines);
            $this->assertNotNull($schoollClassTeacher->funcao_exercida);
            $this->assertNotNull($schoollClassTeacher->tipo_vinculo);
            $this->assertEquals('2', $schoollClassTeacher->tipo_vinculo);
            $this->assertEquals($this->year, $schoollClassTeacher->ano);
        }
    }

    /** @test */
    public function validationImportRegister60()
    {
        $enrollments = LegacyEnrollment::all();

        $this->assertNotNull($enrollments);
        $this->assertCount(10, $enrollments);

        foreach ($enrollments as $enrollment) {
            $this->assertEquals($this->user->cod_usuario, $enrollment->ref_usuario_cad);
            $this->assertTrue($enrollment->data_cadastro->isToday());
            $this->assertEquals(1, $enrollment->ativo);
            $this->assertEquals($this->dateEnrollment->format('Y-m-d'), $enrollment->data_enturmacao->format('Y-m-d'));
            if (in_array($enrollment->schoolClass->etapa_educacenso, [
                3,
                22,
                23,
                56,
                64,
                72
            ])) {
                $this->assertNotNull($enrollment->etapa_educacenso);
            }
            $this->assertEquals('{}', $enrollment->tipo_atendimento);
        }

        $students = LegacyStudent::all();

        foreach ($students as $student) {
            $this->assertEquals(1, $student->recebe_escolarizacao_em_outro_espaco);
            $this->assertEquals(0, $student->tipo_transporte);
        }

        $registrations = LegacyRegistration::all();

        $this->assertNotNull($registrations);
        $this->assertCount(10, $registrations);

        foreach ($registrations as $registration) {
            $this->assertEquals($this->user->cod_usuario, $registration->ref_usuario_cad);
            $this->assertTrue($registration->data_cadastro->isToday());
            $this->assertEquals(1, $registration->ativo);
            $this->assertEquals(1, $registration->aprovado);
            $this->assertEquals($this->year, $registration->ano);
            $this->assertEquals(1, $registration->ultima_matricula);
        }
    }
}
