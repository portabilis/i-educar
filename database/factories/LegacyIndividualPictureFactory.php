<?php

namespace Database\Factories;

use App\Models\LegacyIndividualPicture;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyIndividualPictureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyIndividualPicture::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'idpes' => LegacyPersonFactory::new()->create(),
            'caminho' => $this->faker->imageUrl(),
        ];
    }
}
