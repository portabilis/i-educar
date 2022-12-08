<?php

namespace Tests\Educacenso\Export;

use App\Models\Country;
use App\Models\LegacySchool;
use App\Services\Educacenso\HandleFileService;
use App\Services\Educacenso\ImportServiceFactory;
use App\User;
use Carbon\Carbon;
use Database\Factories\CityFactory;
use Database\Factories\DistrictFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\StateFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    protected int $year;
    protected User $user;
    protected Carbon $dateEnrollment;
    protected LegacySchool $legacySchool;

    public function setUp(): void
    {
        parent::setUp();
        \Artisan::call('db:seed', ['--class' => 'DefaultPmieducarTurmaTurnoTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultManagerRolesTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultManagerLinkTypesTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultManagerAccessCriteriasTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultModulesEducacensoIesTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultModulesEducacensoCursoSuperiorTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultCadastroDeficienciaTableSeeder']);
        \Artisan::call('db:seed', ['--class' => 'DefaultModulesEducacensoOrgaoRegionalTableSeeder']);

        $country = Country::updateOrCreate([
            'id' => 1
        ], [
            'name' => 'Brasil',
            'ibge_code' => '76',
        ]);

        DistrictFactory::new()->create([
            'name' => 'IÇARA',
            'ibge_code' => '05',
            'city_id' => CityFactory::new()->create([
                'state_id' => StateFactory::new()->create([
                    'country_id' => $country,
                    'name' => 'Santa Catarina',
                    'abbreviation' => 'SC',
                    'ibge_code' => '42',
                ]),
                'name' => 'IÇARA',
                'ibge_code' => '4207007',
            ])
        ]);

        $this->year = 2022;
        $this->dateEnrollment = new Carbon('2022-01-01');

        $this->user = LegacyUserFactory::new()->admin()->create([
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->create([
                'data_educacenso' => '2022-05-30'
            ]),
        ]);

        $this->actingAs($this->user);

        $yearImportService = ImportServiceFactory::createImportService(
            $this->year,
            $this->dateEnrollment->format('d/m/Y')
        );

        $importFileService = new HandleFileService($yearImportService, $this->user);

        $importFileService->handleFile(new UploadedFile(
            resource_path('../tests/Educacenso/importacao_educacenso_2022.txt'),
            'importacao_educacenso_2022.txt'
        ));

        $this->legacySchool = LegacySchool::first();
    }

    /** @test */
    public function validationExportCensoRegistro00()
    {
        $data00 = [
            'oper' => 'get',
            'resource' => 'registro-00',
            'escola' => $this->legacySchool->getKey(),
            'ano' => $this->year,
        ];
        $response00 = $this->get('/module/Api/EducacensoAnalise?' . http_build_query($data00));

        $response00->assertSuccessful()
            ->assertJsonCount(0, 'mensagens')
            ->assertJsonCount(0, 'msgs')
            ->assertJson(
                [
                    'mensagens' => [],
                    'title' => 'Análise exportação - Registro 00',
                    'oper' => 'get',
                    'resource' => 'registro-00',
                    'msgs' => [],
                    'any_error_msg' => false
                ]
            )
            ->assertJsonCount(0, 'mensagens');
    }

    /** @test */
    public function validationExportCensoRegistro10()
    {
        $data10 = [
            'oper' => 'get',
            'resource' => 'registro-10',
            'escola' => $this->legacySchool->getKey(),
            'ano' => $this->year,
        ];
        $response10 = $this->get('/module/Api/EducacensoAnalise?' . http_build_query($data10));

        $response10->assertSuccessful()
            ->assertJsonCount(0, 'mensagens')
            ->assertJsonCount(0, 'msgs')
            ->assertJson(
                [
                    'mensagens' => [],
                    'title' => 'Análise exportação - Registro 10',
                    'oper' => 'get',
                    'resource' => 'registro-10',
                    'msgs' => [],
                    'any_error_msg' => false
                ]
            );
    }

    /** @test */
    public function validationExportCensoRegistro20()
    {
        $data20 = [
            'oper' => 'get',
            'resource' => 'registro-20',
            'escola' => $this->legacySchool->getKey(),
            'ano' => $this->year,
        ];
        $response20 = $this->get('/module/Api/EducacensoAnalise?' . http_build_query($data20));

        $response20->assertSuccessful()
            ->assertJsonCount(1, 'mensagens')
            ->assertJsonCount(0, 'msgs')
            ->assertJson(
                [
                    'mensagens' => [
                        0 => [
                            'text' => '<span class=\'avisos-educacenso\'><b>Aviso não impeditivo:</b> Dados para formular o registro 20 da escola ESCOLA PORTABILIS sujeito à valor inválido. Verificamos que a turma MULTI 4&ORDM; 5&ORDM; ANO U VESP 2021 é de formação geral básica e itinerário formativo, e a etapa de ensino é 35 - Ensino Médio - Normal/Magistério 1ª Série, portanto você pode definir os itinerários dos alunos individualmente.',
                            'path' => '(Escola > Cadastros > Alunos > Visualizar > Itinerário formativo > Campo: Tipo do itinerário formativo)',
                            'linkPath' => '/intranet/educar_aluno_lst.php',
                            'fail' => false,
                        ],
                    ],
                    'title' => 'Análise exportação - Registro 20',
                    'oper' => 'get',
                    'resource' => 'registro-20',
                    'msgs' => [],
                    'any_error_msg' => false
                ]
            );
    }

    /** @test */
    public function validationExportCensoRegistro30()
    {
        $data30 = [
            'oper' => 'get',
            'resource' => 'registro-30',
            'escola' => $this->legacySchool->getKey(),
            'ano' => $this->year,
        ];
        $response30 = $this->get('/module/Api/EducacensoAnalise?' . http_build_query($data30));

        $response30->assertSuccessful()
            ->assertJsonCount(1, 'mensagens')
            ->assertJsonCount(0, 'msgs')
            ->assertJson(
                [
                    'mensagens' => [
                        0 => [
                            'text' => '<span class=\'avisos-educacenso\'><b>Aviso não impeditivo:</b> O campo: País de residência possui valor padrão: Brasil. Certifique-se que os(as) alunos(as) ou docentes residentes de outro país, que não seja Brasil, possuam o País de residência informado corretamente.</span>',
                            'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: País de residência)',
                            'linkPath' => '/intranet/atendidos_lst.php',
                            'fail' => false,
                        ],
                    ],
                    'title' => 'Análise exportação - Registro 30',
                    'oper' => 'get',
                    'resource' => 'registro-30',
                    'msgs' => [],
                    'any_error_msg' => false
                ]
            );
    }

    /** @test */
    public function validationExportCensoRegistro40()
    {
        $data40 = [
            'oper' => 'get',
            'resource' => 'registro-40',
            'escola' => $this->legacySchool->getKey(),
            'ano' => $this->year,
        ];
        $response40 = $this->get('/module/Api/EducacensoAnalise?' . http_build_query($data40));

        $response40->assertSuccessful()
            ->assertJsonCount(0, 'mensagens')
            ->assertJsonCount(0, 'msgs')
            ->assertJson(
                [
                    'mensagens' => [],
                    'title' => 'Análise exportação - Registro 40',
                    'oper' => 'get',
                    'resource' => 'registro-40',
                    'msgs' => [],
                    'any_error_msg' => false
                ]
            );
    }

    /** @test */
    public function validationExportCensoRegistro50()
    {
        $data50 = [
            'oper' => 'get',
            'resource' => 'registro-50',
            'escola' => $this->legacySchool->getKey(),
            'ano' => $this->year,
        ];
        $response50 = $this->get('/module/Api/EducacensoAnalise?' . http_build_query($data50));

        $response50->assertSuccessful()
            ->assertJson(
                [
                    'mensagens' => [],
                    'title' => 'Análise exportação - Registro 50',
                    'oper' => 'get',
                    'resource' => 'registro-50',
                    'msgs' => [],
                    'any_error_msg' => false
                ]
            );
    }

    /** @test */
    public function validationExportCensoRegistro60()
    {
        $data60 = [
            'oper' => 'get',
            'resource' => 'registro-60',
            'escola' => $this->legacySchool->getKey(),
            'ano' => $this->year,
        ];
        $response60 = $this->get('/module/Api/EducacensoAnalise?' . http_build_query($data60));

        $response60->assertSuccessful()
            ->assertJson(
                [
                    'mensagens' => [],
                    'title' => 'Análise exportação - Registro 60',
                    'oper' => 'get',
                    'resource' => 'registro-60',
                    'msgs' => [],
                    'any_error_msg' => false
                ]
            );
    }
}
