<?php

use Database\Factories\CityFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\PlaceFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(
        LegacyUserFactory::new()
            ->admin()
            ->create()
    );
});

function buscaBairroPelaApi(string $query): array
{
    $response = test()->get('/module/Api/Bairro?' . http_build_query([
        'oper' => 'get',
        'resource' => 'bairro-search',
        'query' => $query,
    ]));

    $response->assertOk();

    return $response->json('result');
}

test('searches neighborhood by partial text', function () {
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

    $result = buscaBairroPelaApi('Vila');

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
});

test('search ignores accents and case', function () {
    $place = PlaceFactory::new()->create([
        'neighborhood' => 'Vila São José',
    ]);

    $result = buscaBairroPelaApi('VILA SAO JOSE');

    $this->assertArrayHasKey(
        "Vila São José / {$place->city_id}",
        $result
    );

    $this->assertSame(
        "Vila São José / {$place->city->name}",
        $result["Vila São José / {$place->city_id}"]
    );
});

test('does not return duplicate neighborhoods', function () {
    $firstPlace = PlaceFactory::new()->create([
        'neighborhood' => 'Vila',
        'address' => 'Rua Um',
    ]);

    PlaceFactory::new()->create([
        'neighborhood' => 'Vila',
        'city_id' => $firstPlace->city_id,
        'address' => 'Rua Dois',
    ]);

    $result = buscaBairroPelaApi('Vila');

    $this->assertSame(
        [
            "Vila / {$firstPlace->city_id}" => "Vila / {$firstPlace->city->name}",
        ],
        $result
    );
});

test('returns neighborhoods with city information', function () {
    $placeZeta = PlaceFactory::new()->create([
        'neighborhood' => 'Vila Zeta',
    ]);

    $placeAlfa = PlaceFactory::new()->create([
        'neighborhood' => 'Vila Alfa',
    ]);

    $placeMeio = PlaceFactory::new()->create([
        'neighborhood' => 'Vila Meio',
    ]);

    $result = buscaBairroPelaApi('Vila');

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
});

test('limits search to fifteen neighborhoods', function () {
    for ($i = 1; $i <= 20; $i++) {
        PlaceFactory::new()->create([
            'neighborhood' => sprintf('Bairro Limite %02d', $i),
        ]);
    }

    $result = buscaBairroPelaApi('Bairro Limite');

    $this->assertCount(15, $result);
});

test('persisted neighborhood is available in future searches', function () {
    $neighborhood = 'Bairro Teste Issue 1202';

    $place = PlaceFactory::new()->create([
        'neighborhood' => $neighborhood,
    ]);

    $this->assertDatabaseHas('places', [
        'neighborhood' => $neighborhood,
    ]);

    $result = buscaBairroPelaApi('Issue 1202');

    $this->assertArrayHasKey(
        "{$neighborhood} / {$place->city_id}",
        $result
    );

    $this->assertSame(
        "{$neighborhood} / {$place->city->name}",
        $result["{$neighborhood} / {$place->city_id}"]
    );
});
