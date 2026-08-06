<?php

namespace Tests\Feature\Http\Controllers\Api;

use Database\Factories\CityFactory;
use Database\Factories\StateFactory;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class PostalCodeControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_search_returns_normalized_postal_code_data(): void
    {
        $state = StateFactory::new()->create([
            'abbreviation' => 'SC',
        ]);

        $city = CityFactory::new()->create([
            'state_id' => $state,
            'name' => 'Florianópolis',
            'ibge_code' => 4205407,
        ]);

        $client = $this->createMock('GuzzleHttp\\Client');
        $client->expects($this->once())
            ->method('get')
            ->with('https://opencep.com/v1/88010020')
            ->willReturn(new Response(200, [], json_encode([
                'cep' => '88010020',
                'logradouro' => 'Rua Felipe Schmidt',
                'complemento' => 'Apê à direita #2',
                'bairro' => 'Centro',
                'localidade' => 'Florianópolis',
                'uf' => 'SC',
                'ibge' => '4205407',
            ], JSON_UNESCAPED_UNICODE)));

        $this->app->instance('GuzzleHttp\\Client', $client);

        $this->getJson('/api/postal-code/88010020')
            ->assertOk()
            ->assertJson([
                'postal_code' => '88010020',
                'address' => 'Rua Felipe Schmidt',
                'complement' => 'Ape a direita 2',
                'neighborhood' => 'Centro',
                'city_name' => 'Florianópolis',
                'state_abbreviation' => 'SC',
                'city_ibge_code' => 4205407,
                'city' => [
                    'id' => $city->getKey(),
                    'state_id' => $state->getKey(),
                    'name' => $city->name,
                    'ibge_code' => $city->ibge_code,
                ],
            ]);
    }

    #[DataProvider('postalCodeNotFoundProvider')]
    public function test_search_returns_not_found_when_postal_code_cannot_be_resolved(string $scenario): void
    {
        $client = $this->createMock('GuzzleHttp\\Client');

        $expectation = $client->expects($this->once())
            ->method('get')
            ->with('https://opencep.com/v1/88010020');

        switch ($scenario) {
            case 'erro_payload':
                $expectation->willReturn(new Response(200, [], json_encode([
                    'erro' => true,
                ])));

                break;

            case 'unknown_city':
                $expectation->willReturn(new Response(200, [], json_encode([
                    'cep' => '88010020',
                    'logradouro' => 'Rua Felipe Schmidt',
                    'complemento' => '',
                    'bairro' => 'Centro',
                    'localidade' => 'Florianópolis',
                    'uf' => 'SC',
                    'ibge' => '9999999',
                ], JSON_UNESCAPED_UNICODE)));

                break;

            default:
                $expectation->willThrowException(new RuntimeException('Timeout while contacting postal code service.'));
        }

        $this->app->instance('GuzzleHttp\\Client', $client);

        $this->getJson('/api/postal-code/88010020')
            ->assertNotFound()
            ->assertJson([
                'message' => 'Not found',
            ]);
    }

    public static function postalCodeNotFoundProvider(): array
    {
        return [
            'upstream returns error payload' => ['erro_payload'],
            'upstream city is not mapped locally' => ['unknown_city'],
            'upstream request fails' => ['exception'],
        ];
    }
}
