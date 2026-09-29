<?php

namespace Tests\Api;

use Database\Factories\LegacyUserFactory;
use Database\Factories\PlaceFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyNeighborhoodSearchTest extends TestCase
{
    use DatabaseTransactions;

    private const ACCENTED_NEIGHBORHOOD = 'Vila São José';

    private const NEIGHBORHOOD_FIELD = 'neighborhood';

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            LegacyUserFactory::new()->admin()->create()
        );
    }

    public function test_searches_neighborhood_by_partial_text(): void
    {
        PlaceFactory::new()->create([
            self::NEIGHBORHOOD_FIELD => 'Vila Nova',
        ]);

        PlaceFactory::new()->create([
            self::NEIGHBORHOOD_FIELD => 'Centro',
        ]);

        $result = $this->searchNeighborhood('Vila');

        $this->assertArrayHasKey('Vila Nova', $result);
        $this->assertArrayNotHasKey('Centro', $result);
    }

    public function test_search_ignores_accents_and_case(): void
    {
        PlaceFactory::new()->create([
            self::NEIGHBORHOOD_FIELD => self::ACCENTED_NEIGHBORHOOD,
        ]);

        $result = $this->searchNeighborhood('VILA SAO JOSE');

        $this->assertArrayHasKey(self::ACCENTED_NEIGHBORHOOD, $result);
        $this->assertSame(
            self::ACCENTED_NEIGHBORHOOD,
            $result[self::ACCENTED_NEIGHBORHOOD]
        );
    }

    public function test_does_not_return_duplicate_neighborhoods(): void
    {
        PlaceFactory::new()->create([
            self::NEIGHBORHOOD_FIELD => 'Vila',
            'address' => 'Rua Um',
        ]);

        PlaceFactory::new()->create([
            self::NEIGHBORHOOD_FIELD => 'Vila',
            'address' => 'Rua Dois',
        ]);

        $result = $this->searchNeighborhood('Vila');

        $this->assertSame([
            'Vila' => 'Vila',
        ], $result);
    }

    public function test_returns_neighborhoods_in_alphabetical_order(): void
    {
        PlaceFactory::new()->create([
            self::NEIGHBORHOOD_FIELD => 'Vila Zeta',
        ]);

        PlaceFactory::new()->create([
            self::NEIGHBORHOOD_FIELD => 'Vila Alfa',
        ]);

        PlaceFactory::new()->create([
            self::NEIGHBORHOOD_FIELD => 'Vila Meio',
        ]);

        $result = $this->searchNeighborhood('Vila');

        $this->assertSame([
            'Vila Alfa',
            'Vila Meio',
            'Vila Zeta',
        ], array_keys($result));
    }

    public function test_limits_search_to_fifteen_neighborhoods(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            PlaceFactory::new()->create([
                self::NEIGHBORHOOD_FIELD => sprintf('Bairro Limite %02d', $i),
            ]);
        }

        $result = $this->searchNeighborhood('Bairro Limite');

        $this->assertCount(15, $result);
    }

    public function test_persisted_neighborhood_is_available_in_future_searches(): void
    {
        $neighborhood = 'Bairro Teste Issue 1202';

        PlaceFactory::new()->create([
            self::NEIGHBORHOOD_FIELD => $neighborhood,
        ]);

        $this->assertDatabaseHas('places', [
            self::NEIGHBORHOOD_FIELD => $neighborhood,
        ]);

        $result = $this->searchNeighborhood('Issue 1202');

        $this->assertArrayHasKey($neighborhood, $result);
        $this->assertSame($neighborhood, $result[$neighborhood]);
    }

    private function searchNeighborhood(string $query): array
    {
        $response = $this->get('/module/Api/Bairro?' . http_build_query([
            'oper' => 'get',
            'resource' => 'bairro-search',
            'query' => $query,
        ]));

        $response->assertOk();

        return $response->json('result');
    }
}
