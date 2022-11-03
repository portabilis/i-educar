<?php

namespace Tests;

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyOrganization;
use App\Models\LegacyPerson;
use App\Models\LegacySchool;
use App\Models\LegacyStageType;
use App\Models\Place;
use App\Models\SchoolInep;
use App\User;
use Database\Factories\CityFactory;
use Database\Factories\CountryFactory;
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

    public function setUp(): void
    {
        parent::setUp();
        \Artisan::call('db:seed', ['--class' => 'DefaultPmieducarTurmaTurnoTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultManagerRolesTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultManagerAccessCriteriasTableSeeder']);

        CityFactory::new()->create([
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
        $this->assertEquals(1, $legacySchool->address->count());
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
        $this->assertEquals(1, $legacySchool->stages->count());
        $legacyAcademicYearStage = $legacySchool->stages->first();
        $this->assertNotNull($legacyAcademicYearStage);
        $this->assertInstanceOf(LegacyAcademicYearStage::class, $legacyAcademicYearStage);
        $this->assertEquals($this->year, $legacyAcademicYearStage->ref_ano);
        $this->assertEquals($legacySchool->cod_escola, $legacyAcademicYearStage->ref_ref_cod_escola);
        $this->assertEquals(1, $legacyAcademicYearStage->sequencial);
        $this->assertEquals($this->year . '-03-03', $legacyAcademicYearStage->data_inicio->format('Y-m-d'));
        $this->assertEquals($this->year . '-12-12', $legacyAcademicYearStage->data_fim->format('Y-m-d'));
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
    }

    /** @test */
    public function validationImportRegister10()
    {
        $legacySchool = LegacySchool::first();

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
        //Código da Turma na Entidade/Escola
        //Nome da Turma
        //tem que ser da mesma escola 00
        //Disciplinas
        $legacySchool = LegacySchool::first();

        dd($legacySchool->schoolClasses);

        $this->assertTrue(true);
    }
}
