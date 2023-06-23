<?php

namespace Tests\Api;

use App\Models\LegacyMaritalStatus;
use Database\Factories\CityFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyRaceFactory;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyIndividualEndpointTestCreate extends TestCase
{
    use DatabaseTransactions;
    use LoginFirstUser;

    public function testCreateEndpoint(): void
    {
        $this->loginWithFirstUser();
        $faker = Factory::create();

        $maritalStatus = LegacyMaritalStatus::create([
            'descricao' => 'Solteiro',
        ]);

        $race = LegacyRaceFactory::new()->create();

        $city = CityFactory::new()->create();

        $institution = LegacyInstitutionFactory::new()->make();
        $individual = LegacyIndividualFactory::new()->make([
            'idmun_nascimento' => $city,
            'data_nasc' => now(),
            'sexo' => 'M',
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
            'nm_pessoa' => 'DOMINIC TORRETO',
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
            'sexo' => $individual->sexo,
            'data_nasc' => $individual->data_nasc,
        ])->assertDatabaseHas($individual->person->getTable(), [
            'nome' => 'DOMINIC TORRETO',
        ])->assertDatabaseHas('cadastro.fisica_raca', [
            'ref_cod_raca' => $race->getKey(),
        ]);
    }
}
