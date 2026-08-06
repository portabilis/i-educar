<?php

namespace Tests\Feature\Http\Controllers\Api;

use Database\Factories\CityFactory;
use Database\Factories\StateFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class PostalCodeControllerTest extends TestCase
{
    use DatabaseTransactions;

    private const POSTAL_CODE = '88010020';

    private const CITY_NAME = 'Florianópolis';

    private const STREET = 'Rua Felipe Schmidt';

    public function test_search_returns_normalized_postal_code_data(): void
    {
        $state = StateFactory::new()->create([
            'abbreviation' => 'SC',
        ]);

        $city = CityFactory::new()->create([
            'state_id' => $state,
            'name' => self::CITY_NAME,
            'ibge_code' => 4205407,
        ]);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('get')
            ->with($this->postalCodeServiceUrl())
            ->willReturn(new Response(200, [], json_encode($this->openCepPayload([
                'complemento' => 'Apê à direita #2',
            ]), JSON_UNESCAPED_UNICODE)));

        $this->app->instance(Client::class, $client);

        $this->getJson('/api/postal-code/' . self::POSTAL_CODE)
            ->assertOk()
            ->assertJson([
                'postal_code' => self::POSTAL_CODE,
                'address' => self::STREET,
                'complement' => 'Ape a direita 2',
                'neighborhood' => 'Centro',
                'city_name' => self::CITY_NAME,
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
        $client = $this->createMock(Client::class);

        $expectation = $client->expects($this->once())
            ->method('get')
            ->with($this->postalCodeServiceUrl());

        switch ($scenario) {
            case 'erro_payload':
                $expectation->willReturn(new Response(200, [], json_encode([
                    'erro' => true,
                ])));

                break;

            case 'unknown_city':
                $expectation->willReturn(new Response(200, [], json_encode($this->openCepPayload([
                    'complemento' => '',
                    'ibge' => '9999999',
                ]), JSON_UNESCAPED_UNICODE)));

                break;

            default:
                $expectation->willThrowException(new RuntimeException('Timeout while contacting postal code service.'));
        }

        $this->app->instance(Client::class, $client);

        $this->getJson('/api/postal-code/' . self::POSTAL_CODE)
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

    private function postalCodeServiceUrl(): string
    {
        return 'https://opencep.com/v1/' . self::POSTAL_CODE;
    }

    /**
     * @return array<string, string>
     */
    private function openCepPayload(array $overrides = []): array
    {
        return array_merge([
            'cep' => self::POSTAL_CODE,
            'logradouro' => self::STREET,
            'complemento' => '',
            'bairro' => 'Centro',
            'localidade' => self::CITY_NAME,
            'uf' => 'SC',
            'ibge' => '4205407',
        ], $overrides);
    }
}

