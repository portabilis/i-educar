<?php

namespace Database\Factories;

use App\Models\LegacyRace;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyRaceFactory extends Factory
{
    protected $model = LegacyRace::class;

    public function definition()
    {
       return [
           'idpes_cad' => LegacyUserFactory::new()->unique()->make(),
           'nm_raca' => $this->faker->firstName(),
           'raca_educacenso' => random_int(0,5),
       ];
    }

}
