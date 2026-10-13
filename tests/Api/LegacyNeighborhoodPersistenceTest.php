<?php

namespace Tests\Api;

use App\Models\Place;
use Database\Factories\CityFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\PersonHasPlaceFactory;
use Database\Factories\PlaceFactory;
use iEducar\Modules\Addressing\LegacyAddressingFields;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyNeighborhoodPersistenceTest extends TestCase
{
    use DatabaseTransactions;

    private const NEIGHBORHOOD = 'Bairro Teste Issue 1202';

    public function test_saves_new_neighborhood_and_associates_it_with_person(): void
    {
        $person = LegacyPersonFactory::new()->create();
        $city = CityFactory::new()->create();

        $addressing = $this->newAddressing();

        $addressing->fillAddress(
            address: 'Rua Teste',
            number: '10',
            complement: null,
            neighborhood: self::NEIGHBORHOOD,
            cityId: $city->getKey(),
            postalCode: '70000000'
        );

        $addressing->saveFor($person->getKey());

        $place = Place::query()
            ->where('neighborhood', self::NEIGHBORHOOD)
            ->first();

        $this->assertNotNull($place);

        $this->assertDatabaseHas('places', [
            'id' => $place->getKey(),
            'neighborhood' => self::NEIGHBORHOOD,
        ]);

        $this->assertDatabaseHas('person_has_place', [
            'person_id' => $person->getKey(),
            'place_id' => $place->getKey(),
            'type' => 1,
        ]);
    }

    public function test_loads_persisted_neighborhood_from_person_address(): void
    {
        $person = LegacyPersonFactory::new()->create();

        $place = PlaceFactory::new()->create([
            'neighborhood' => 'Vila Persistida',
        ]);

        PersonHasPlaceFactory::new()->create([
            'person_id' => $person->getKey(),
            'place_id' => $place->getKey(),
            'type' => 1,
        ]);

        $addressing = $this->newAddressing();

        $addressing->loadFor($person->getKey());

        $this->assertSame(
            'Vila Persistida',
            $addressing->getNeighborhood()
        );
    }

    private function newAddressing(): object
    {
        return new class
        {
            use LegacyAddressingFields;

            public function fillAddress(
                string $address,
                string $number,
                ?string $complement,
                string $neighborhood,
                int $cityId,
                string $postalCode
            ): void {
                $this->address = $address;
                $this->number = $number;
                $this->complement = $complement;
                $this->neighborhood = $neighborhood;
                $this->city_id = $cityId;
                $this->postal_code = $postalCode;
            }

            public function saveFor(int $personId): void
            {
                $this->saveAddress($personId);
            }

            public function loadFor(int $personId): void
            {
                $this->loadAddress($personId);
            }

            public function getNeighborhood(): ?string
            {
                return $this->neighborhood;
            }
        };
    }
}
