<?php

namespace Tests\Api;

use Database\Factories\LegacyIndividualFactory;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyIndividualEndpointTestUpdate extends TestCase
{
    use DatabaseTransactions;
    use LoginFirstUser;

    public function testUpdateEndpoint(): void
    {
        $this->loginWithFirstUser();
        $faker = Factory::create();

        $individual = LegacyIndividualFactory::new()->create([
            'sexo' => 'M',
        ]);
        $newIndividual = LegacyIndividualFactory::new()->make([
            'data_nasc' => now()->subYears(18),
        ]);

        $request = [
            'tipoacao' => 'Editar',
            'cod_pessoa_fj' => (string) $individual->getKey(),
        ];

        $data = [
            'pai_id' => $newIndividual->father->getKey(),
            'mae_id' => $newIndividual->mother->getKey(),
            'naturalidade_id' => $newIndividual->idmun_nascimento,
            'nm_pessoa' => 'DOMINIC TORRETO',
            'nome_social' => 'DOMINIC TORRETO',
            'sexo' => 'F',
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
            'sexo' => 'F',
            'data_nasc' => $newIndividual->data_nasc,
        ])->assertDatabaseHas($individual->person->getTable(), [
            'idpes' => $individual->person->getKey(),
            'nome' => 'DOMINIC TORRETO',
        ]);
    }
}
