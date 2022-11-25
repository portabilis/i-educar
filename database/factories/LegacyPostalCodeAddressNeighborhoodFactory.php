<?php

namespace Database\Factories;

use App\Models\LegacyPostalCodeAddressNeighborhood;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyPostalCodeAddressNeighborhoodFactory extends Factory
{
    protected $model = LegacyPostalCodeAddressNeighborhood::class;

    public function definition(): array
    {
        PlaceFactory::new()->create();

        $model = new $this->model();

        return $model->first()->toArray();
    }
}
