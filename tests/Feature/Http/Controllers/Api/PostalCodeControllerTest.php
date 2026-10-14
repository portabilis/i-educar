<?php

use Database\Factories\CityFactory;
use Database\Factories\StateFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->postalCodeServiceUrl = function (): string {
        return 'https://opencep.com/v1/' . '88010020';
    };

    /**
     * @return array<string, string>
     */
    $this->openCepPayload = function (array $overrides = []): array {
        return array_merge([
            'cep' => '88010020',
            'logradouro' => 'Rua Felipe Schmidt',
            'complemento' => '',
            'bairro' => 'Centro',
            'localidade' => 'Florianópolis',
            'uf' => 'SC',
            'ibge' => '4205407',
        ], $overrides);
    };
});

test('search returns normalized postal code data', function () {
    $state = StateFactory::new()->create([
        'abbreviation' => 'SC',
    ]);

    $city = CityFactory::new()->create([
        'state_id' => $state,
        'name' => 'Florianópolis',
        'ibge_code' => 4205407,
    ]);

    $client = $this->createMock(Client::class);
    $client->expects($this->once())
        ->method('get')
        ->with(($this->postalCodeServiceUrl)())
        ->willReturn(new Response(200, [], json_encode(($this->openCepPayload)([
            'complemento' => 'Apê à direita #2',
        ]), JSON_UNESCAPED_UNICODE)));

    $this->app->instance(Client::class, $client);

    $this->getJson('/api/postal-code/' . '88010020')
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
});

test('search returns not found when postal code cannot be resolved', function (string $scenario) {
    $client = $this->createMock(Client::class);

    $expectation = $client->expects($this->once())
        ->method('get')
        ->with(($this->postalCodeServiceUrl)());

    switch ($scenario) {
        case 'erro_payload':
            $expectation->willReturn(new Response(200, [], json_encode([
                'erro' => true,
            ])));

            break;

        case 'unknown_city':
            $expectation->willReturn(new Response(200, [], json_encode(($this->openCepPayload)([
                'complemento' => '',
                'ibge' => '9999999',
            ]), JSON_UNESCAPED_UNICODE)));

            break;

        default:
            $expectation->willThrowException(new RuntimeException('Timeout while contacting postal code service.'));
    }

    $this->app->instance(Client::class, $client);

    $this->getJson('/api/postal-code/' . '88010020')
        ->assertNotFound()
        ->assertJson([
            'message' => 'Not found',
        ]);
})->with([
    'upstream returns error payload' => ['erro_payload'],
    'upstream city is not mapped locally' => ['unknown_city'],
    'upstream request fails' => ['exception'],
]);
