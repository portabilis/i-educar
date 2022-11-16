<?php

namespace Database\Factories;

use App\Models\LegacyPostalCodeAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyPostalCodeAddressFactory extends Factory
{
    protected $model = LegacyPostalCodeAddress::class;

    public function definition(): array
    {
        PlaceFactory::new()->create();

        $model = new $this->model();

        return $model->first()->toArray();
    }
}
