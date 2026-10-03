<?php

namespace Tests\Api;

use Database\Factories\CityFactory;
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
            LegacyUserFactory::new()
                ->admin()
                ->create()
        );
    }

    public function test_searches_neighborhood_by_partial_text(): void
    {
        $city = CityFactory::new()->create([
            'name' => 'Brasília',
        ]);

        $place = PlaceFactory::new()->create([
            'neighborhood' => 'Vila Nova',
            'city_id' => $city->id,
        ]);

        PlaceFactory::new()->create([
            'neighborhood' => 'Centro',
            'city_id' => $city->id,
        ]);

        $result = $this->searchNeighborhood('Vila');

        $this->assertArrayHasKey(
            "Vila Nova / {$place->city_id}",
            $result
        );

        $this->assertSame(
            "Vila Nova / {$place->city->name}",
            $result["Vila Nova / {$place->city_id}"]
        );

        $this->assertNotContains(
            'Centro / ' . $city->name,
            array_values($result)
        );
    }

    public function test_search_ignores_accents_and_case(): void
    {
        $place = PlaceFactory::new()->create([
            'neighborhood' => 'Vila São José',
        ]);

        $result = $this->searchNeighborhood('VILA SAO JOSE');

        $this->assertArrayHasKey(
            "Vila São José / {$place->city_id}",
            $result
        );

        $this->assertSame(
            "Vila São José / {$place->city->name}",
            $result["Vila São José / {$place->city_id}"]
        );
    }

    public function test_does_not_return_duplicate_neighborhoods(): void
    {
        $firstPlace = PlaceFactory::new()->create([
            'neighborhood' => 'Vila',
            'address' => 'Rua Um',
        ]);

        PlaceFactory::new()->create([
            'neighborhood' => 'Vila',
            'city_id' => $firstPlace->city_id,
            'address' => 'Rua Dois',
        ]);

        $result = $this->searchNeighborhood('Vila');

        $this->assertSame(
            [
                "Vila / {$firstPlace->city_id}" => "Vila / {$firstPlace->city->name}",
            ],
            $result
        );
    }

    public function test_returns_neighborhoods_with_city_information(): void
    {
        $placeZeta = PlaceFactory::new()->create([
            'neighborhood' => 'Vila Zeta',
        ]);

        $placeAlfa = PlaceFactory::new()->create([
            'neighborhood' => 'Vila Alfa',
        ]);

        $placeMeio = PlaceFactory::new()->create([
            'neighborhood' => 'Vila Meio',
        ]);

        $result = $this->searchNeighborhood('Vila');

        $this->assertArrayHasKey(
            "Vila Zeta / {$placeZeta->city_id}",
            $result
        );

        $this->assertArrayHasKey(
            "Vila Alfa / {$placeAlfa->city_id}",
            $result
        );

        $this->assertArrayHasKey(
            "Vila Meio / {$placeMeio->city_id}",
            $result
        );

        $this->assertSame(
            "Vila Zeta / {$placeZeta->city->name}",
            $result["Vila Zeta / {$placeZeta->city_id}"]
        );

        $this->assertSame(
            "Vila Alfa / {$placeAlfa->city->name}",
            $result["Vila Alfa / {$placeAlfa->city_id}"]
        );

        $this->assertSame(
            "Vila Meio / {$placeMeio->city->name}",
            $result["Vila Meio / {$placeMeio->city_id}"]
        );
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

        $place = PlaceFactory::new()->create([
            'neighborhood' => $neighborhood,
        ]);

        $this->assertDatabaseHas('places', [
            self::NEIGHBORHOOD_FIELD => $neighborhood,
        ]);

        $result = $this->searchNeighborhood('Issue 1202');

        $this->assertArrayHasKey(
            "{$neighborhood} / {$place->city_id}",
            $result
        );

        $this->assertSame(
            "{$neighborhood} / {$place->city->name}",
            $result["{$neighborhood} / {$place->city_id}"]
        );
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
