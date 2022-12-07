<?php

namespace Tests\Api;

use App\Models\LegacyMaritalStatus;
use Database\Factories\CityFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyRaceFactory;
use Database\Factories\LegacyStudentFactory;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyIndividualEndpointTest extends TestCase
{
    use DatabaseTransactions;
    use LoginFirstUser;

    public function testCreateEndpoint(): void
    {
        $this->loginWithFirstUser();
        $faker = Factory::create();

        $maritalStatus = LegacyMaritalStatus::create([
            'descricao' => 'Solteiro'
        ]);

        $race = LegacyRaceFactory::new()->create();

        $city = CityFactory::new()->create();

        $institution = LegacyInstitutionFactory::new()->make();
        $individual = LegacyIndividualFactory::new()->make([
            'idmun_nascimento' => $city,
        ]);

        $request = [
            'tipoacao' => 'Novo',
            'naturalidade_id' => $individual->idmun_nascimento,
        ];

        $data = [
            'obrigar_campos_censo' => 0,
            'pai_id' => $individual->father->getKey(),
            'mae_id' => $individual->mother->getKey(),
            'naturalidade_id' => $individual->idmun_nascimento,
            'nm_pessoa' => $individual->person->name,
            'sexo' => $individual->sexo,
            'estado_civil_id' => $maritalStatus->getKey(),
            'data_nasc' => $individual->data_nasc->format('d/m/Y'),
            'rg' => $faker->randomNumber(8),
            'data_emissao_rg' => now()->format('d/m/Y'),
            'orgao_emissao_rg' => 1,
            'uf_emissao_rg' => 'SC',
            'certidao_nascimento' => $faker->randomNumber(8),
            'uf_emissao_certidao_civil' => 'SC',
            'data_emissao_certidao_civil' => now()->format('d/m/Y'),
            'cor_raca' => $race->getKey(),
            'tipo_nacionalidade' => 1,
            'pais_residencia' => $individual->pais_residencia,
            'zona_localizacao_censo' => $individual->zona_localizacao_censo,
        ];

        $payload = array_merge($request, $data);

        $this->post('/intranet/atendidos_cad.php', $payload)
            ->assertSuccessful();

        $this->assertDatabaseHas($individual->getTable(), [
            'data_nasc' => $individual->data_nasc->format('Y-m-d'),
            'idpes_mae' => $individual->mother->getKey(),
            'idpes_pai' => $individual->father->getKey(),
            'ideciv' => $maritalStatus->getKey(),
            'pais_residencia' => 76,
            'zona_localizacao_censo' => $individual->zona_localizacao_censo,
        ])->assertDatabaseHas($individual->person->getTable(), [
            'nome' => $individual->person->name
        ])->assertDatabaseHas('cadastro.fisica_raca', [
            'ref_cod_raca' => $race->getKey(),
        ]);
    }

    public function testUpdateEndpoint(): void
    {
        $faker = Factory::create();

        $individual = LegacyIndividualFactory::new()->create();
        $newIndividual = LegacyIndividualFactory::new()->make();

        $request = [
            'tipoacao' => 'Editar',
            'cod_pessoa_fj' => (string)$individual->getKey(),
        ];

        $data = [
            'pai_id' => $newIndividual->father->getKey(),
            'mae_id' => $newIndividual->mother->getKey(),
            'naturalidade_id' => $newIndividual->idmun_nascimento,
            'nm_pessoa' => $newIndividual->person->name,
            'nome_social' => $newIndividual->person->name,
            'sexo' => $newIndividual->sexo,
            'data_nasc' => $newIndividual->data_nasc->format('d/m/Y'),
            'rg' => $faker->randomNumber(8),
            'data_emissao_rg' => now()->format('d/m/Y'),
            'orgao_emissao_rg' => 1,
            'uf_emissao_rg' => 'SC',
            'certidao_nascimento' => $faker->randomNumber(8),
            'uf_emissao_certidao_civil' => 'SC',
            'data_emissao_certidao_civil' => now()->format('d/m/Y'),
            'zona_localizacao_censo' => $newIndividual->zona_localizacao_censo,
        ];

        $payload = array_merge($request, $data);

        $this->post('/intranet/atendidos_cad.php', $payload)
            ->assertSuccessful();
        $this->assertDatabaseHas($individual->getTable(), [
            'idpes' => $individual->person->getKey(),
            'data_nasc' => $newIndividual->data_nasc->format('Y-m-d'),
        ])->assertDatabaseHas($individual->person->getTable(), [
            'idpes' => $individual->person->getKey(),
            'nome' => $newIndividual->person->name
        ]);
    }

    public function testDeleteEndpoint(): void
    {
        $individual = LegacyIndividualFactory::new()->create();

        $request = [
            'tipoacao' => 'Excluir',
            'cod_pessoa_fj' => (string) $individual->getKey(),
        ];

        $data = [];

        $payload = array_merge($request, $data);

        $this->post('/intranet/atendidos_cad.php', $payload)->assertRedirect('atendidos_lst.php');
        $this->assertDatabaseHas($individual->getTable(), [
            'idpes' => $individual->person->getKey(),
            'ativo' => 0,
        ]);
    }

    public function testDeleteWhenHasStudent(): void
    {
        $student = LegacyStudentFactory::new()->create();

        $request = [
            'tipoacao' => 'Excluir',
            'cod_pessoa_fj' => (string) $student->ref_idpes,
        ];

        $data = [];

        $payload = array_merge($request, $data);

        $this->post('/intranet/atendidos_cad.php', $payload)
            ->assertSuccessful()
            ->assertSee('Não foi possível excluir. Esta pessoa possuí vínculo com aluno.');
        $this->assertDatabaseHas($student->getTable(), [
            'ref_idpes' => $student->ref_idpes,
            'ativo' => 1,
        ]);
    }

    public function testDeleteWhenHasResponsible(): void
    {
        $student = LegacyStudentFactory::new()->create();

        $request = [
            'tipoacao' => 'Excluir',
            'cod_pessoa_fj' => (string) $student->individual->idpes_mae,
        ];

        $data = [];

        $payload = array_merge($request, $data);

        $this->post('/intranet/atendidos_cad.php', $payload)
            ->assertSuccessful()
            ->assertSee('Não foi possível excluir. A pessoa possuí vínculo(s) com aluno(s) como mãe, pai ou outro responsável.');
    }

    public function testDeleteWhenHasEmployee(): void
    {
        $employee = EmployeeFactory::new()->create();

        $request = [
            'tipoacao' => 'Excluir',
            'cod_pessoa_fj' => (string) $employee->ref_cod_pessoa_fj,
        ];

        $data = [];

        $payload = array_merge($request, $data);

        $this->post('/intranet/atendidos_cad.php', $payload)
            ->assertSuccessful()
            ->assertSee('Não foi possível excluir. Esta pessoa possuí vínculo com usuário do sistema.');
        $this->assertDatabaseHas($employee->getTable(), [
            'cod_servidor' => $employee->getKey(),
            'ativo' => 1,
        ]);
    }
}
